<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use App\Models\Message;
use Illuminate\Support\Facades\File;

class ManagerMessagesController extends Controller
{
    /**
     * GET /manager/messages?client={clientId}&agent={agentId}
     */
    public function index(Request $request)
    {
        $managerId = (int) auth()->id();
        abort_unless($managerId, 401);

        // Optional filter: a specific DEO the manager wants to focus on
        $selectedAgentId = $request->integer('agent') ?: null;

        // Agents dropdown (role = 4 as per your code)
        $agents = User::query()
            ->where('role', 4)
            ->orderBy('name')
            ->get(['id', 'name']);

        /* -----------------------------
     * Clients list for this manager
     * ----------------------------- */
        $baseClients = DB::table('clients_managers as cm')
            ->join('users as u', 'u.id', '=', 'cm.client_id')
            ->when($selectedAgentId, function ($q) use ($selectedAgentId) {
                $q->whereExists(function ($q2) use ($selectedAgentId) {
                    $q2->from('clients_data_entry_operators as cdeo')
                        ->whereColumn('cdeo.client_id', 'cm.client_id')
                        ->where('cdeo.data_entry_operator_id', $selectedAgentId);
                });
            })
            ->where('cm.manager_id', $managerId)
            ->orderBy('u.name')
            ->get(['u.id as client_id', 'u.name as client_name']);

        // For each client, compute "actors" and then get the last message among those actors
        $clients = $baseClients->map(function ($row) use ($managerId) {
            $clientId = (int) $row->client_id;

            // DEOs linked to this client
            $deoIds = DB::table('clients_data_entry_operators')
                ->where('client_id', $clientId)
                ->pluck('data_entry_operator_id')
                ->map(fn($v) => (int) $v)
                ->all();

            // Supervisors linked to those DEOs
            $supervisorIds = [];
            if (!empty($deoIds)) {
                $supervisorIds = DB::table('data_entry_operators_supervisors')
                    ->whereIn('data_entry_operator_id', $deoIds)
                    ->pluck('supervisor_id')
                    ->map(fn($v) => (int) $v)
                    ->all();
            }

            // Build the actors set for this client's shared room
            $actors = array_values(array_unique(array_filter([
                $clientId,
                $managerId,
                ...$deoIds,
                ...$supervisorIds,
            ], fn($v) => $v)));

            // Last message that involves this client, among the actors
            $last = Message::query()
                ->whereIn('sender_id', $actors)
                ->whereIn('receiver_id', $actors)
                ->where(function ($q) use ($clientId) {
                    $q->where('sender_id', $clientId)->orWhere('receiver_id', $clientId);
                })
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->first(['description', 'created_at']);

            return (object) [
                'client_id'       => $clientId,
                'client_name'     => $row->client_name,
                'last_message'    => $last ? ($last->description ?? $last->body ?? null) : null,
                'last_message_at' => $last->created_at ?? null,
            ];
        })
            ->sortByDesc(fn($c) => $c->last_message_at ? $c->last_message_at->timestamp : 0)
            ->values();

        /* -----------------------------
     * Selected conversation (room)
     * ----------------------------- */
        $selectedClientId = $request->integer('client') ?: ($clients->first()->client_id ?? null);

        $messages = collect();
        $selectedClient = null;
        $selectedConversation = null; // keep for blade compatibility if you use it

        if ($selectedClientId) {
            // Make sure this client belongs to this manager
            $isLinked = DB::table('clients_managers')
                ->where('manager_id', $managerId)
                ->where('client_id', $selectedClientId)
                ->exists();

            if ($isLinked) {
                // Gather actors for THIS client's room
                $deoIds = DB::table('clients_data_entry_operators')
                    ->where('client_id', $selectedClientId)
                    ->pluck('data_entry_operator_id')
                    ->map(fn($v) => (int) $v)
                    ->all();

                $supervisorIds = [];
                if (!empty($deoIds)) {
                    $supervisorIds = DB::table('data_entry_operators_supervisors')
                        ->whereIn('data_entry_operator_id', $deoIds)
                        ->pluck('supervisor_id')
                        ->map(fn($v) => (int) $v)
                        ->all();
                }

                $actors = array_values(array_unique(array_filter([
                    (int) $selectedClientId,
                    $managerId,
                    ...$deoIds,
                    ...$supervisorIds,
                ], fn($v) => $v)));

                $messages = Message::query()
                    ->with(['attachments', 'sender:id,type'])
                    ->whereIn('sender_id', $actors)
                    ->whereIn('receiver_id', $actors)
                    // keep only messages that involve THIS client (either side)
                    ->where(function ($q) use ($selectedClientId) {
                        $q->where('sender_id', $selectedClientId)
                            ->orWhere('receiver_id', $selectedClientId);
                    })
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->get()
                    ->map(function ($m) use ($managerId) {
                        $m->is_mine = ((int) $m->sender_id === (int) $managerId);
                        $m->body    = $m->description ?? $m->body; // normalize for blade
                        return $m;
                    });

                $selectedClient = (object) [
                    'id'   => (int) $selectedClientId,
                    'name' => optional(User::find($selectedClientId))->name ?? 'Client',
                ];
            }
        }

        return view('managers.messages.index', [
            'agents'                => $agents,
            'selected_agent_id'     => $selectedAgentId,
            'clients'               => $clients,
            'selected_client'       => $selectedClient,
            'selected_conversation' => $selectedConversation,
            'messages'              => $messages,
        ]);
    }

    /**
     * POST /manager/messages
     * body: description?, client_id? (or to_user_id?), files[]?
     */
    public function store(Request $request)
    {
        $managerId = auth()->id();
        abort_unless($managerId, 401);

        // Validate what your form actually sends
        $validated = $request->validate([
            'description'   => 'nullable|string',
            'receiver_id'   => 'required|integer|exists:users,id',
            'attachments.*' => 'nullable|file|max:20480', // 20 MB
        ]);

        // Must have text or at least one file
        $hasFiles = $request->hasFile('attachments') || $request->hasFile('files');
        if (empty($validated['description']) && !$hasFiles) {
            return back()->withErrors(['description' => 'Type a message or attach a file.']);
        }

        $toUserId = (int) $validated['receiver_id'];

        // Relationship guard: by default only allow messaging an assigned CLIENT
        $isLinkedClient = DB::table('clients_managers')
            ->where('manager_id', $managerId)
            ->where('client_id', $toUserId)
            ->exists();

        if (!$isLinkedClient) {
            return back()->withErrors(['receiver_id' => 'This client is not assigned to you.']);
        }

        // DB::beginTransaction();
        try {
            // 1) Create message
            $messageId = DB::table('messages')->insertGetId([
                'sender_id'   => $managerId,
                'receiver_id' => $toUserId,
                'description' => $validated['description'] ?? null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // 2) Upload any files (from attachments[] or files[])

            $files = $request->file('attachments', []);
            if ($files instanceof \Illuminate\Http\UploadedFile) {
                $files = [$files];
            }

            if (!empty($files)) {
                $baseDir = public_path("chat/{$messageId}");
                if (!is_dir($baseDir)) @mkdir($baseDir, 0775, true);

                $hasMA  = DB::getSchemaBuilder()->hasTable('message_attachments');
                //$hasDoc = DB::getSchemaBuilder()->hasTable('documents') && $this->columnExists('documents', 'message_id');

                foreach ($files as $file) {
                    if (!$file || !$file->isValid()) continue;

                    $ext   = $file->getClientOriginalExtension();
                    $clean = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $name  = $clean . '-' . Str::random(8) . ($ext ? ".{$ext}" : '');
                    $file->move($baseDir, $name);

                    $relative = "chat/{$messageId}/{$name}";
                    $full     = public_path($relative);
                    $url      = asset($relative);
                    $mime     = File::exists($full) ? File::mimeType($full) : $file->getClientMimeType();
                    $size     = File::exists($full) ? File::size($full) : $file->getSize();

                    if ($hasMA) {
                        // Preferred: dedicated attachments table
                        DB::table('message_attachments')->insert([
                            'message_id'    => $messageId,
                            'original_name' => $file->getClientOriginalName(),
                            'file_name'     => $name,
                            'mime'          => $mime,
                            'size'          => $size,
                            'url'           => $url,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]);
                    }
                }
            }

            //DB::commit();
        } catch (\Throwable $e) {
            //DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'Failed to send message.']);
        }

        // Back to same thread
        return redirect()
            ->route('manager.messages.index', [
                'client' => $toUserId,
                'agent'  => $request->integer('agent'),
            ])
            ->with('success', 'Message sent.');
    }
}
