<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Str;
use App\Models\MessageAttachment;
use App\Services\FirebaseNotificationService;

class DataEntryOperatorMessagesController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index(Request $request)
    {
        $operatorId = (int) auth()->id();
        abort_unless($operatorId, 401);

        // Get supervisors for this DEO
        $supervisorIds = DB::table('data_entry_operators_supervisors')
            ->where('data_entry_operator_id', $operatorId)
            ->pluck('supervisor_id')
            ->map(fn($v) => (int) $v)
            ->all();

        // Helper: role check for managers
        $isManager = fn($column) => function ($q) use ($column) {
            $q->whereHas($column, fn($u) => $u->where('type', 'manager'));
        };

        // Get clients assigned to this DEO
        $rawClients = User::query()
            ->select('users.id', 'users.name')
            ->join('clients_data_entry_operators as cdeo', 'cdeo.client_id', '=', 'users.id')
            ->where('cdeo.data_entry_operator_id', $operatorId)
            ->orderBy('users.name', 'asc')
            ->get();

        // Process clients with last message
        $clients = $rawClients->map(function ($u) use ($operatorId, $supervisorIds, $isManager) {
            $clientId = (int) $u->id;

            $last = Message::query()
                ->where(function ($q) use ($clientId) {
                    $q->where('sender_id', $clientId)
                        ->orWhere('receiver_id', $clientId);
                })
                ->where(function ($q) use ($clientId, $operatorId, $supervisorIds, $isManager) {
                    $q->where(function ($qq) use ($clientId, $operatorId, $supervisorIds, $isManager) {
                        $qq->where('sender_id', $clientId)
                            ->where(function ($rx) use ($operatorId, $supervisorIds, $isManager) {
                                $rx->whereIn('receiver_id', array_merge([$operatorId], $supervisorIds))
                                    ->orWhere($isManager('receiver'));
                            });
                    })
                        ->orWhere(function ($qq) use ($clientId, $operatorId, $supervisorIds, $isManager) {
                            $qq->where('receiver_id', $clientId)
                                ->where(function ($sx) use ($operatorId, $supervisorIds, $isManager) {
                                    $sx->whereIn('sender_id', array_merge([$operatorId], $supervisorIds))
                                        ->orWhere($isManager('sender'));
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
                'unread_count'    => 0,
                'avatar_url'      => null,
            ];
        })
            ->sortByDesc(fn($c) => $c->last_message_at ? $c->last_message_at->timestamp : 0)
            ->values();

        // Selected thread
        $selectedClient = null;
        $messages = collect();

        if ($request->filled('client')) {
            $clientId = (int) $request->integer('client');

            $isAssigned = DB::table('clients_data_entry_operators')
                ->where('client_id', $clientId)
                ->where('data_entry_operator_id', $operatorId)
                ->exists();
            abort_unless($isAssigned, 403);

            $selectedClient = User::query()->select('id', 'name')->findOrFail($clientId);

            $messages = Message::query()
                ->with(['attachments', 'sender:id,type'])
                ->where(function ($q) use ($clientId) {
                    $q->where('sender_id', $clientId)
                        ->orWhere('receiver_id', $clientId);
                })
                ->where(function ($q) use ($clientId, $operatorId, $supervisorIds, $isManager) {
                    $q->where(function ($qq) use ($clientId, $operatorId, $supervisorIds, $isManager) {
                        $qq->where('sender_id', $clientId)
                            ->where(function ($rx) use ($operatorId, $supervisorIds, $isManager) {
                                $rx->whereIn('receiver_id', array_merge([$operatorId], $supervisorIds))
                                    ->orWhere($isManager('receiver'));
                            });
                    })
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
                    $m->body    = $m->description;
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

        $validated = $request->validate([
            'client_id' => 'required|integer|exists:users,id',
            'body'      => 'nullable|string|max:5000|required_without:files',
            'files'     => 'nullable',
            'files.*'   => 'file|max:20480',
        ]);

        if (empty($validated['body']) && empty($request->file('files'))) {
            return back()->withErrors(['body' => 'Type a message or attach a file.'])->withInput();
        }

        // Ensure client is assigned to this DEO
        $isAssigned = DB::table('clients_data_entry_operators')
            ->where('client_id', $validated['client_id'])
            ->where('data_entry_operator_id', $operatorId)
            ->exists();

        abort_unless($isAssigned, 403);

        DB::beginTransaction();
        try {
            // Get DEO details for notification
            $deo = User::find($operatorId);
            $client = User::find($validated['client_id']);

            // Create message
            $msg = Message::create([
                'sender_id'   => $operatorId,
                'receiver_id' => (int)$validated['client_id'],
                'description' => trim($request->input('body', '')),
            ]);

            // Handle file uploads
            $files = $request->file('files', []);
            if ($files instanceof \Illuminate\Http\UploadedFile) {
                $files = [$files];
            }

            if (!empty($files)) {
                $this->handleFileUploads($msg->id, $files);
            }

            DB::commit();

            // Send notifications after successful message creation
            $this->sendNotifications($deo, $client, $validated['body'] ?? '', !empty($files));

            return back()->with('status', 'Message sent');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['body' => 'Failed to send message.'])->withInput();
        }
    }

    /**
     * Handle file uploads for the message
     */
    private function handleFileUploads(int $messageId, array $files): void
    {
        $dir = public_path('chat/' . $messageId);
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

            $url = asset('chat/' . $messageId . '/' . $fname);

            MessageAttachment::create([
                'message_id'    => $messageId,
                'original_name' => $oname,
                'file_name'     => $fname,
                'mime'          => $mime,
                'size'          => $size,
                'url'           => $url,
            ]);
        }
    }

    /**
     * Send notifications to client, supervisors, and managers
     */
    private function sendNotifications($deo, $client, string $messageText, bool $hasFiles): void
    {
        try {
            $clientId = (int) $client->id;
            $deoId = (int) $deo->id;

            // Get all actors involved in this conversation
            $actors = $this->getConversationActors($clientId, $deoId);

            // Remove DEO from recipients (shouldn't notify themselves)
            $recipientIds = array_filter($actors, fn($id) => $id !== $deoId);

            if (empty($recipientIds)) {
                return;
            }

            $messagePreview = $this->getMessagePreview($messageText, $hasFiles);
            $title = "New message from {$deo->name}";
            $body = "To {$client->name}: {$messagePreview}";

            // Notification data
            $data = [
                'msg_type' => 'deo_message',
                'client_id' => (string) $clientId,
                'deo_id' => (string) $deoId,
                'deo_name' => $deo->name,
                'client_name' => $client->name,
                'timestamp' => now()->toISOString(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ];

            // Send notifications to all recipients
            $results = $this->firebaseService->sendToUsers($recipientIds, $title, $body, $data);

            // Log results
            if ($results['success_count'] > 0) {
                \Log::info("DEO notifications sent successfully", [
                    'deo_id' => $deoId,
                    'client_id' => $clientId,
                    'recipients' => $recipientIds,
                    'success_count' => $results['success_count']
                ]);
            }

            if ($results['failure_count'] > 0) {
                \Log::warning("Some DEO notifications failed to send", [
                    'failures' => $results['failure_count'],
                    'errors' => $results['errors']
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send DEO notifications: ' . $e->getMessage(), [
                'deo_id' => $deo->id,
                'client_id' => $client->id
            ]);
        }
    }

    /**
     * Get all actors involved in the conversation for a specific client
     */
    private function getConversationActors(int $clientId, int $deoId): array
    {
        // Get supervisors for this DEO
        $supervisorIds = DB::table('data_entry_operators_supervisors')
            ->where('data_entry_operator_id', $deoId)
            ->pluck('supervisor_id')
            ->map(fn($v) => (int) $v)
            ->all();

        // Get managers for this client (via clients_managers table)
        $managerIds = DB::table('clients_managers')
            ->where('client_id', $clientId)
            ->pluck('manager_id')
            ->map(fn($v) => (int) $v)
            ->all();

        return array_values(array_unique(array_filter([
            $clientId,
            $deoId,
            ...$supervisorIds,
            ...$managerIds,
        ], fn($v) => $v)));
    }

    /**
     * Create a preview of the message for the notification
     */
    private function getMessagePreview(string $messageText, bool $hasFiles): string
    {
        if (!empty($messageText)) {
            return strlen($messageText) > 100
                ? substr($messageText, 0, 100) . '...'
                : $messageText;
        }

        if ($hasFiles) {
            return '📎 Attachment';
        }

        return 'New message';
    }
}
