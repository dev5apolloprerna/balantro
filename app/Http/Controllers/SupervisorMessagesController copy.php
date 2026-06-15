<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\Message;
use Illuminate\Support\Str;
use App\Models\MessageAttachment;

class SupervisorMessagesController extends Controller
{
    /**
     * Supervisor inbox
     * GET /supervisor/messages?agent={agentId}&client={clientId}
     */
    public function index(Request $request)
    {
        $sup   = auth()->user();
        $supId = (int) $sup->id;

        // 1) DEOs reporting to this supervisor
        $agents = DB::table('data_entry_operators_supervisors as link')
            ->join('users as deo', 'deo.id', '=', 'link.data_entry_operator_id')
            ->where('link.supervisor_id', $supId)
            ->orderBy('deo.name')
            ->select('deo.id', 'deo.name', 'deo.type', 'deo.role')
            ->get();

        $selectedAgentId = (int) $request->integer('agent') ?: (int) ($agents->first()->id ?? 0);

        // 2) Clients under selected DEO (with last message + when)
        $clients = DB::table('clients_data_entry_operators as cdeo')
            ->join('users as u', 'u.id', '=', 'cdeo.client_id')
            ->where('cdeo.data_entry_operator_id', $selectedAgentId)
            ->select([
                'u.id as client_user_id',
                'u.name as client_name',
                DB::raw("
                    (
                      SELECT TOP (1) m.description
                      FROM messages m
                      WHERE (m.sender_id = u.id AND m.receiver_id = {$selectedAgentId})
                         OR (m.sender_id = {$selectedAgentId} AND m.receiver_id = u.id)
                      ORDER BY m.created_at DESC
                    ) as last_message
                "),
                DB::raw("
                    (
                      SELECT TOP (1) m.created_at
                      FROM messages m
                      WHERE (m.sender_id = u.id AND m.receiver_id = {$selectedAgentId})
                         OR (m.sender_id = {$selectedAgentId} AND m.receiver_id = u.id)
                      ORDER BY m.created_at DESC
                    ) as last_message_at
                "),
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = str_replace('%', '\%', trim($request->input('search')));
                $q->where('u.name', 'LIKE', "%{$term}%");
            })
            ->get()
            ->sortByDesc(fn($r) => $r->last_message_at ? \Carbon\Carbon::parse($r->last_message_at)->timestamp : 0)
            ->values();

        // 3) Build the conversation set and load messages
        $clientUserId = (int) $request->integer('client_user');
        $messages     = collect();

        if ($clientUserId && $selectedAgentId) {
            // Seed actors with the base triad
            $actors = collect([$clientUserId, $selectedAgentId, $supId])->filter()->unique()->values();

            // 3a) Discover any other participants who have chatted with any base actor
            $participants = DB::table('messages')
                ->select('sender_id', 'receiver_id')
                ->whereIn('sender_id', $actors)
                ->orWhereIn('receiver_id', $actors)
                ->get();

            $discovered = collect($participants)
                ->flatMap(fn($r) => [$r->sender_id, $r->receiver_id])
                ->filter()
                ->unique();

            // Merge discovered actors (this brings in Managers/Admins/etc. automatically)
            $actors = $actors->merge($discovered)->unique()->values();

            // 3b) Load messages strictly among these actors
            $messages = Message::query()
                ->leftJoin('users as s', 's.id', '=', 'messages.sender_id')
                ->select(['messages.*', DB::raw('s.type as sender_type')])
                ->whereIn('messages.sender_id', $actors)
                ->whereIn('messages.receiver_id', $actors)
                ->orderBy('messages.created_at')
                ->with(['attachments', 'sender:id,type'])
                ->get();

            // 3c) Attach legacy files (if some rows were stored in message_attachments without model relation)
            $ids = $messages->pluck('id')->all();
            if (!empty($ids)) {
                $attByMsg = DB::table('message_attachments')
                    ->whereIn('message_id', $ids)
                    ->get()
                    ->groupBy('message_id');

                $messages = $messages->map(function ($m) use ($attByMsg) {
                    $list = collect($attByMsg->get($m->id, collect()))->map(function ($a) {
                        return (object) [
                            'url'  => $a->url ?? null,
                            'name' => $a->original_name ?? $a->file_name ?? '',
                            'mime' => $a->mime ?? $a->mime_type ?? null,
                            'size' => (int) ($a->size ?? 0),
                        ];
                    });
                    // If Eloquent relation already loaded, merge them so blade can read either way.
                    $eloquent = collect($m->attachments ?? []);
                    $m->attachments = $eloquent->merge($list)->all();
                    return $m;
                });
            }
        }

        // 4) Resolve client name for header
        $clientName = ($clientUserId && isset($clients))
            ? optional($clients->firstWhere('client_user_id', $clientUserId))->client_name
            : null;

        return view('supervisors.messages.index', [
            'agents'          => $agents,
            'selectedAgentId' => $selectedAgentId,
            'clients'         => $clients,
            'clientUserId'    => $clientUserId,
            'messages'        => $messages,
            'clientName'      => $clientName,
        ]);
    }

    public function store(Request $request)
    {
        $senderId = (int) auth()->id();

        // The view sends: receiver_id, description (text), attachments[] (files)
        $validated = $request->validate([
            'receiver_id'   => 'required|integer|exists:users,id',
            'description'   => 'nullable|string',
            'files'   => 'nullable|array',
            'files.*' => 'file|max:20480', // 20MB per file; adjust if you like
            // optional context so we can redirect back to the same thread:
            'agent_id'       => 'nullable|integer|exists:users,id',
            'client_user_id' => 'nullable|integer|exists:users,id',
        ]);

        // Require either text or at least one file
        if (!$request->hasFile('files') && !trim((string) ($validated['description'] ?? ''))) {
            return back()->with('error', 'Type a message or attach a file.');
        }

        // Create message row
        $messageId = DB::table('messages')->insertGetId([
            'sender_id'   => $senderId,
            'receiver_id' => (int) $validated['receiver_id'],
            'description' => $validated['description'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Save files to public/chat/{message_id}/… and create message_attachments rows
        if ($request->hasFile('files')) {
            $dir = public_path("chat/{$messageId}");
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            foreach ($request->file('files') as $upload) {
                if (!$upload || !$upload->isValid()) continue;

                $ext     = $upload->getClientOriginalExtension();
                $newName = now()->format('YmdHis') . '-' . Str::random(6) . ($ext ? ".{$ext}" : '');
                $upload->move($dir, $newName);

                $absUrl = url("chat/{$messageId}/{$newName}");

                DB::table('message_attachments')->insert([
                    'message_id'    => $messageId,
                    'original_name' => $upload->getClientOriginalName(),
                    'file_name'     => $newName,
                    'mime'          => $upload->getClientMimeType(),
                    'size'          => $upload->getSize(),
                    'url'           => $absUrl,   // matches your existing rows
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        // Redirect back to the same conversation context if provided
        $agentId      = $request->input('agent_id');
        $clientUserId = $request->input('client_user_id') ?? $request->input('client_user');

        // If only receiver_id is present and it's a client, try to infer their DEO for redirect
        if (!$agentId && $clientUserId) {
            $agentId = DB::table('clients_data_entry_operators')
                ->where('client_id', (int) $clientUserId)
                ->value('data_entry_operator_id');
        }

        return redirect()->route('supervisor.messages.index', array_filter([
            'agent'       => $agentId,
            'client_user' => $clientUserId ?: (int) $validated['receiver_id'],
        ]))->with('success', 'Message sent.');
    }

    private function loadAttachmentsFor(array $messageIds): array
    {
        if (empty($messageIds)) return [];
        $map = [];

        $schema = DB::getSchemaBuilder();

        if ($schema->hasTable('message_attachments')) {
            $rows = DB::table('message_attachments')
                ->select('id', 'message_id', 'original_name', 'file_name', 'mime', 'size', 'url', 'created_at')
                ->whereIn('message_id', $messageIds)
                ->orderBy('id')
                ->get();

            foreach ($rows as $r) {
                $map[$r->message_id][] = [
                    'id'   => $r->id,
                    'name' => $r->original_name ?: $r->file_name,
                    'url'  => $r->url ?: asset("chat/{$r->message_id}/{$r->file_name}"),
                    'mime' => $r->mime,
                    'size' => (int)($r->size ?? 0),
                ];
            }
            return $map;
        }

        // Back-compat: documents table with message_id + relative path in `file`
        if ($schema->hasTable('documents') && $this->columnExists('documents', 'message_id')) {
            $rows = DB::table('documents')
                ->select('id', 'message_id', 'file', 'status', 'created_at')
                ->whereIn('message_id', $messageIds)
                ->orderBy('id')
                ->get();

            foreach ($rows as $r) {
                $rel  = ltrim($r->file ?? '', '/');
                $full = public_path($rel);
                $map[$r->message_id][] = [
                    'id'     => $r->id,
                    'name'   => basename($rel) ?: 'file',
                    'url'    => $rel ? asset($rel) : null,
                    'mime'   => ($rel && File::exists($full)) ? File::mimeType($full) : null,
                    'size'   => ($rel && File::exists($full)) ? File::size($full) : null,
                    'status' => $r->status,
                ];
            }
        }

        return $map;
    }

    /** Tiny utility so we can safely check fallback schema */
    private function columnExists(string $table, string $column): bool
    {
        static $cache = [];
        $key = $table . '.' . $column;
        if (array_key_exists($key, $cache)) return $cache[$key];
        return $cache[$key] = DB::getSchemaBuilder()->hasColumn($table, $column);
    }
}
