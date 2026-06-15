<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Str;
use App\Models\MessageAttachment;
use Illuminate\Support\Facades\Schema;

class DataEntryOperatorMessagesController extends Controller
{
    public function index(Request $request)
    {
        $operatorId = (int) auth()->id();
        abort_unless($operatorId, 401);

        // ---- 0) Resolve supervisors for this DEO (via pivot) ----
        // Table: data_entry_operators_supervisors (data_entry_operator_id, supervisor_id)
        $supervisorIds = DB::table('data_entry_operators_supervisors')
            ->where('data_entry_operator_id', $operatorId)
            ->pluck('supervisor_id')
            ->map(fn($v) => (int) $v)
            ->all();

        // ---- 1) Clients assigned to this DEO (via pivot) ----
        $rawClients = User::query()
            ->select('users.id', 'users.name')
            ->join('clients_data_entry_operators as cdeo', 'cdeo.client_id', '=', 'users.id')
            ->where('cdeo.data_entry_operator_id', $operatorId)
            ->orderBy('users.name', 'asc')
            ->get();

        // Helper: role check for managers (no mapping table needed)
        $isManager = fn($column) => function ($q) use ($column) {
            $q->whereHas($column, fn($u) => $u->where('type', 'manager'));
        };

        // ---- 2) Last message per client: involve client + (this DEO OR any supervisor of this DEO OR ANY manager) ----
        $clients = $rawClients->map(function ($u) use ($operatorId, $supervisorIds, $isManager) {
            $clientId = (int) $u->id;

            $last = Message::query()
                // must involve THIS client on either side
                ->where(function ($q) use ($clientId) {
                    $q->where('sender_id', $clientId)
                        ->orWhere('receiver_id', $clientId);
                })
                // and other side must be DEO|supervisor|manager
                ->where(function ($q) use ($clientId, $operatorId, $supervisorIds, $isManager) {
                    // client -> (deo/supervisor/manager)
                    $q->where(function ($qq) use ($clientId, $operatorId, $supervisorIds, $isManager) {
                        $qq->where('sender_id', $clientId)
                            ->where(function ($rx) use ($operatorId, $supervisorIds, $isManager) {
                                $rx->whereIn('receiver_id', array_merge([$operatorId], $supervisorIds))
                                    ->orWhere($isManager('receiver')); // receiver is manager
                            });
                    })
                        // (deo/supervisor/manager) -> client
                        ->orWhere(function ($qq) use ($clientId, $operatorId, $supervisorIds, $isManager) {
                            $qq->where('receiver_id', $clientId)
                                ->where(function ($sx) use ($operatorId, $supervisorIds, $isManager) {
                                    $sx->whereIn('sender_id', array_merge([$operatorId], $supervisorIds))
                                        ->orWhere($isManager('sender')); // sender is manager
                                });
                        });
                })
                ->orderByDesc('created_at')
                ->select('description', 'created_at')
                ->first();

            return (object) [
                'id'              => $clientId,
                'name'            => $u->name ?? 'Client',
                'last_message'    => $last?->description,
                'last_message_at' => $last?->created_at,
                'unread_count'    => 0, // keep your logic (or compute if you track read status)
                'avatar_url'      => null,
            ];
        })
            ->sortByDesc(fn($c) => $c->last_message_at ? $c->last_message_at->timestamp : 0)
            ->values();

        // ---- 3) Selected thread (optional ?client=<id>) ----
        $selectedClient = null;
        $messages = collect();

        if ($request->filled('client')) {
            $clientId = (int) $request->integer('client');

            // ensure client really belongs to this DEO
            $isAssigned = DB::table('clients_data_entry_operators')
                ->where('client_id', $clientId)
                ->where('data_entry_operator_id', $operatorId)
                ->exists();
            abort_unless($isAssigned, 403);

            $selectedClient = User::query()->select('id', 'name')->findOrFail($clientId);

            $messages = Message::query()
                ->with(['attachments', 'sender:id,type'])
                // must involve THIS client
                ->where(function ($q) use ($clientId) {
                    $q->where('sender_id', $clientId)
                        ->orWhere('receiver_id', $clientId);
                })
                // other party must be you, a supervisor, or any manager
                ->where(function ($q) use ($clientId, $operatorId, $supervisorIds, $isManager) {
                    // client -> (deo/supervisor/manager)
                    $q->where(function ($qq) use ($clientId, $operatorId, $supervisorIds, $isManager) {
                        $qq->where('sender_id', $clientId)
                            ->where(function ($rx) use ($operatorId, $supervisorIds, $isManager) {
                                $rx->whereIn('receiver_id', array_merge([$operatorId], $supervisorIds))
                                    ->orWhere($isManager('receiver'));
                            });
                    })
                        // (deo/supervisor/manager) -> client
                        ->orWhere(function ($qq) use ($clientId, $operatorId, $supervisorIds, $isManager) {
                            $qq->where('receiver_id', $clientId)
                                ->where(function ($sx) use ($operatorId, $supervisorIds, $isManager) {
                                    $sx->whereIn('sender_id', array_merge([$operatorId], $supervisorIds))
                                        ->orWhere($isManager('sender'));
                                });
                        });
                })
                ->orderBy('created_at')
                ->get()
                ->map(function ($m) use ($operatorId) {
                    $m->is_mine = ((int) $m->sender_id === (int) $operatorId);
                    $m->body    = $m->description; // normalize for your Blade
                    return $m;
                });
        }

        return view('data_entry_operators.messages.index', [
            'clients'         => $clients,
            'selected_client' => $selectedClient ? (object)[
                'id'         => $selectedClient->id,
                'name'       => $selectedClient->name ?? 'Client',
                'avatar_url' => null,
            ] : null,
            'messages'        => $messages,
        ]);
    }

    public function store(Request $request)
    {

        $operatorId = auth()->id();
        abort_unless($operatorId, 401);

        // we send to a client; description is the message text
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:users,id',
            // require body when no files, and vice-versa
            'body'      => 'nullable|string|max:5000|required_without:files',
            'files'     => 'nullable',
            'files.*'   => 'file|max:20480', // 20 MB per file
        ]);


        if (empty($validated['body']) && empty($request->file('files'))) {
            return back()->withErrors(['body' => 'Type a message or attach a file.'])->withInput();
        }

        // Ensure that client is assigned to this DEO
        $isAssigned = DB::table('clients_data_entry_operators')
            ->where('client_id', $validated['client_id'])
            ->where('data_entry_operator_id', $operatorId)
            ->exists();

        abort_unless($isAssigned, 403);
        try {
            $msg = Message::create([
                'sender_id'   => $operatorId,
                'receiver_id' => (int)$validated['client_id'],
                'description' => trim($request->input('body', '')),
            ]);

            // files[] may be null or a single UploadedFile or an array
            $files = $request->file('files', []);
            if ($files instanceof \Illuminate\Http\UploadedFile) {
                $files = [$files];
            }

            if (!empty($files)) {
                $dir = public_path('chat/' . $msg->id);
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }

                foreach ($files as $file) {
                    if (!$file || !$file->isValid()) continue;

                    $ext   = $file->getClientOriginalExtension();
                    $oname = $file->getClientOriginalName();
                    $mime  = $file->getMimeType();
                    $size  = (int)$file->getSize();

                    $fname = now()->format('YmdHis') . '-' . Str::random(6) . ($ext ? '.' . $ext : '');
                    $file->move($dir, $fname);

                    $url = asset('chat/' . $msg->id . '/' . $fname);

                    MessageAttachment::create([
                        'message_id'    => $msg->id,
                        'original_name' => $oname,
                        'file_name'     => $fname,
                        'mime'          => $mime,
                        'size'          => $size,
                        'url'           => $url,
                    ]);
                }
            }

            return back()->with('status', 'Message sent');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['body' => 'Failed to send message.'])->withInput();
        }
    }
}
