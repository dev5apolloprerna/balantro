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
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SupervisorMessagesController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Supervisor inbox
     * GET /Supervisor/messages?agent={agentId}&Client={clientId}
     */
    public function index(Request $request)
{
    $sup   = auth()->user();
    $supId = (int) $sup->id;

    // 1) DEOs reporting to this Supervisor
    $agents = DB::table('data_entry_operators_supervisors as link')
        ->join('users as deo', 'deo.id', '=', 'link.data_entry_operator_id')
        ->where('link.supervisor_id', $supId)
        ->orderBy('deo.name')
        ->select('deo.id', 'deo.name', 'deo.type', 'deo.role')
        ->get();

    $selectedAgentId = (int) $request->integer('agent') ?: (int) ($agents->first()->id ?? 0);

    // 2) Clients under selected DEO (with last message + when)
    $clientsQuery = DB::table('clients_data_entry_operators as cdeo')
        ->join('users as u', 'u.id', '=', 'cdeo.client_id')
        ->where('cdeo.data_entry_operator_id', $selectedAgentId)
        ->select([
            'u.id as client_user_id',
            'u.name as client_name',
            DB::raw("
            (
                SELECT TOP 1 m.description
                FROM messages m
                WHERE (m.sender_id = u.id AND m.receiver_id = {$selectedAgentId})
                   OR (m.sender_id = {$selectedAgentId} AND m.receiver_id = u.id)
                   OR (m.sender_id = u.id AND m.receiver_id = {$supId})
                   OR (m.sender_id = {$supId} AND m.receiver_id = u.id)
                ORDER BY m.created_at DESC
            ) as last_message
        "),
            DB::raw("
            (
                SELECT TOP 1 m.created_at
                FROM messages m
                WHERE (m.sender_id = u.id AND m.receiver_id = {$selectedAgentId})
                   OR (m.sender_id = {$selectedAgentId} AND m.receiver_id = u.id)
                   OR (m.sender_id = u.id AND m.receiver_id = {$supId})
                   OR (m.sender_id = {$supId} AND m.receiver_id = u.id)
                ORDER BY m.created_at DESC
            ) as last_message_at
        "),
        ]);

    // Apply search filter if provided
    if ($request->filled('search')) {
        $term = str_replace('%', '\%', trim($request->input('search')));
        $clientsQuery->where('u.name', 'LIKE', "%{$term}%");
    }

    $clients = $clientsQuery->get()
        ->sortByDesc(fn($r) => $r->last_message_at ? Carbon::parse($r->last_message_at)->timestamp : 0)
        ->values();

    // 3) Build the conversation set and load messages ONLY for selected Client
    $clientUserId = (int) $request->integer('client');
    $messages     = collect();

    if ($clientUserId && $selectedAgentId) {
        // Get all managers who might be involved in conversations
        $managers = DB::table('users')
            ->where('type', 'manager')
            ->pluck('id')
            ->toArray();

        // Include all possible participants: Client, DEO, Supervisor, and Managers
        $actors = collect([$clientUserId, $selectedAgentId, $supId])
            ->merge($managers)
            ->filter()
            ->unique()
            ->values();

        // Load messages with a more inclusive where clause
        $messages = Message::query()
            ->leftJoin('users as s', 's.id', '=', 'messages.sender_id')
            ->select(['messages.*', DB::raw('s.type as sender_type')])
            ->where(function ($query) use ($clientUserId, $selectedAgentId, $supId, $managers) {
                // Messages between Client and DEO
                $query->where(function ($q) use ($clientUserId, $selectedAgentId) {
                    $q->where('sender_id', $clientUserId)
                      ->where('receiver_id', $selectedAgentId);
                })->orWhere(function ($q) use ($clientUserId, $selectedAgentId) {
                    $q->where('sender_id', $selectedAgentId)
                      ->where('receiver_id', $clientUserId);
                })
                // Messages between Client and Supervisor
                ->orWhere(function ($q) use ($clientUserId, $supId) {
                    $q->where('sender_id', $clientUserId)
                      ->where('receiver_id', $supId);
                })->orWhere(function ($q) use ($clientUserId, $supId) {
                    $q->where('sender_id', $supId)
                      ->where('receiver_id', $clientUserId);
                })
                // Messages between Supervisor and DEO
                ->orWhere(function ($q) use ($supId, $selectedAgentId) {
                    $q->where('sender_id', $supId)
                      ->where('receiver_id', $selectedAgentId);
                })->orWhere(function ($q) use ($supId, $selectedAgentId) {
                    $q->where('sender_id', $selectedAgentId)
                      ->where('receiver_id', $supId);
                })
                // Messages involving Managers with Client
                ->orWhere(function ($q) use ($clientUserId, $managers) {
                    $q->whereIn('sender_id', $managers)
                      ->where('receiver_id', $clientUserId);
                })->orWhere(function ($q) use ($clientUserId, $managers) {
                    $q->where('sender_id', $clientUserId)
                      ->whereIn('receiver_id', $managers);
                })
                // Messages involving Managers with DEO
                ->orWhere(function ($q) use ($selectedAgentId, $managers) {
                    $q->whereIn('sender_id', $managers)
                      ->where('receiver_id', $selectedAgentId);
                })->orWhere(function ($q) use ($selectedAgentId, $managers) {
                    $q->where('sender_id', $selectedAgentId)
                      ->whereIn('receiver_id', $managers);
                })
                // Messages involving Managers with Supervisor
                ->orWhere(function ($q) use ($supId, $managers) {
                    $q->whereIn('sender_id', $managers)
                      ->where('receiver_id', $supId);
                })->orWhere(function ($q) use ($supId, $managers) {
                    $q->where('sender_id', $supId)
                      ->whereIn('receiver_id', $managers);
                });
            })
            ->orderBy('messages.created_at')
            ->with(['attachments', 'sender:id,type'])
            ->get();
    }

    // 4) Resolve Client name for header
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
		
        // DB::beginTransaction();
        //try {
            $senderId = (int) auth()->id();
            $sender = auth()->user(); // The view sends: receiver_id, description (text), attachments[] (files)
            $validated = $request->validate(
                [
                    'receiver_id' => 'required|integer|exists:users,id',
                    'description' => 'nullable|string',
                    'files' => 'nullable|array',
                    'files.*' => 'file|max:20480',
                    'agent_id' => 'nullable|integer|exists:users,id',
                    'client_user_id' => 'nullable|integer|exists:users,id'
                ]
            );
	
            // Authorization check - ensure supervisor can message this user
            if (!$this->canMessageUser($sender, User::find($validated['receiver_id']))) {
                return back()->with('error', 'Not authorized to message this user.');
            }
		
            // Require either text or at least one file
            if (!$request->hasFile('files') && !trim((string) ($validated['description'] ?? ''))) {
                return back()->with('error', 'Type a message or attach a file.');
            }
	
            // Create message row
            $messageId = DB::table('messages')->insertGetId(['sender_id' => $senderId, 'receiver_id' => (int) $validated['receiver_id'], 'description' => $validated['description'] ?? null, 'created_at' => now(), 'updated_at' => now(),]);

            // Save files to public/chat/{message_id}/… and create message_attachments rows
            if ($request->hasFile('files')) {
                $this->saveMessageAttachments($request->file('files'), $messageId);
            }
            // 🔥 SEND NOTIFICATIONS TO ALL RELEVANT USERS
            $this->sendGroupNotifications($sender, $validated['receiver_id'], $validated['description'] ?? '', $messageId);

            //DB::commit();
            // Redirect back to the same conversation context if provided
            $agentId = $request->input('agent_id');
            $clientUserId = $request->input('client_user_id') ?? $request->input('client_user');
            // If only receiver_id is present and it's a client, try to infer their DEO for redirect
            if (!$agentId && $clientUserId) {
                $agentId = DB::table('clients_data_entry_operators')->where('client_id', (int) $clientUserId)->value('data_entry_operator_id');
            }

            //return redirect()->route('supervisor.messages.index', array_filter(['agent' => $agentId, 'client_user' => $clientUserId ?: (int) $validated['receiver_id'],]))->with('success', 'Message sent.');
            return redirect()->back()->with('success', 'Message sent.');
        //} catch (\Exception $e) {
        //    DB::rollBack();
        //    Log::error('Message sending failed: ' . $e->getMessage());
        //    return back()->with('error', 'Failed to send message. Please try again.');
        //}
    }

    /**
     * Save message attachments
     */
    private function saveMessageAttachments($files, $messageId)
    {
        $dir = public_path("chat/{$messageId}");
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        foreach ($files as $upload) {
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
                'url'           => $absUrl,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    /**
     * Check if user can message another user
     */
    private function canMessageUser($sender, $receiver)
    {
        if (!$receiver) {
            return false;
        }

        $senderType = strtolower($sender->type);
        $receiverType = strtolower($receiver->type);

        // Supervisor can message their DEOs, clients of their DEOs, and other supervisors/managers
        if ($senderType === 'supervisor') {
            if ($receiverType === 'dataentryoperator') {
                // Check if DEO reports to this Supervisor
                return DB::table('data_entry_operators_supervisors')
                    ->where('supervisor_id', $sender->id)
                    ->where('data_entry_operator_id', $receiver->id)
                    ->exists();
            } elseif (in_array($receiverType, ['client', 'customer'])) {
                // Check if Client belongs to one of Supervisor's DEOs
                return DB::table('clients_data_entry_operators as cdeo')
                    ->join('data_entry_operators_supervisors as deos', 'deos.data_entry_operator_id', '=', 'cdeo.data_entry_operator_id')
                    ->where('deos.supervisor_id', $sender->id)
                    ->where('cdeo.client_id', $receiver->id)
                    ->exists();
            } elseif (in_array($receiverType, ['supervisor', 'manager'])) {
                return true; // Supervisors can message other supervisors/managers
            }
        }

        return false;
    }

    /**
     * Send notifications to all relevant users in the conversation group
     */
    private function sendGroupNotifications(User $sender, int $receiverId, string $messageBody, int $messageId)
    {
        try {

            $receiver = User::find($receiverId);
            if (!$receiver) {
                Log::warning("Receiver not found for notification: {$receiverId}");
                return;
            }

            // Prepare notification content
            $title = "New message from {$sender->name}";
            $body = strlen($messageBody) > 100
                ? substr($messageBody, 0, 100) . '...'
                : ($messageBody ?: '📎 Attachment');

            $data = [
                'message_id' => (string) $messageId,
                'sender_id' => (string) $sender->id,
                'sender_name' => $sender->name,
                'sender_type' => $sender->type,
                'message_preview' => $body,
                'timestamp' => now()->toISOString(),
                'type' => 'chat_message',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ];

            Log::info("Preparing notification from {$sender->name} to receiver {$receiver->name}");

            // Get all users who should be notified
            // //$usersToNotify = $this->getUsersToNotify($sender, $receiver);
            // $usersToNotify = $this->firebaseService->sendToUser($receiverId, $title, $body, $data);

            $usersToNotify = $this->getUsersToNotify($sender, $receiver);
            $this->firebaseService->sendToUsers($usersToNotify, $title, $body, $data);

            Log::info("Users to notify: " . json_encode($usersToNotify));

            // Send Firebase notifications
            $results = [];
            foreach ($usersToNotify as $userId) {
                if ($userId != $sender->id) {
                    $result = $this->firebaseService->sendToUser($userId, $title, $body, $data);
                    $results[$userId] = $result;
                    Log::info("Firebase notification result for user {$userId}: " . json_encode($result));
                }
            }

            Log::info("All notification results: ", $results);
        } catch (\Exception $e) {
            Log::error('Failed to send group notifications: ' . $e->getMessage());
        }
    }

    /**
     * Get all users who should be notified based on the conversation context
     */
    private function getUsersToNotify(User $sender, User $receiver): array
    {
        $usersToNotify = [$receiver->id];

        Log::info("Sender type: {$sender->type}, Receiver type: {$receiver->type}");

        // Scenario 1: Supervisor messaging a Client
        if (strtolower($sender->type) === 'supervisor' && $this->isClientUser($receiver)) {
            Log::info("Scenario 1: Supervisor → Client");
            $usersToNotify = array_merge($usersToNotify, $this->getSupportTeamForClient($receiver->id));
        }
        // Scenario 2: Supervisor messaging a DEO
        elseif (strtolower($sender->type) === 'supervisor' && strtolower($receiver->type) === 'dataentryoperator') {
            Log::info("Scenario 2: Supervisor → DEO");
            $usersToNotify = array_merge($usersToNotify, $this->getRelatedUsersForDEO($receiver->id));

            // Also include clients of this DEO if they're involved in the conversation
            $clients = $this->getClientsForDEO($receiver->id);
            $usersToNotify = array_merge($usersToNotify, $clients);
        }
        // Scenario 3: Supervisor messaging another Supervisor or Manager
        elseif (
            in_array(strtolower($sender->type), ['supervisor', 'manager']) &&
            in_array(strtolower($receiver->type), ['supervisor', 'manager'])
        ) {
            Log::info("Scenario 3: Supervisor/Manager → Supervisor/Manager");
            $usersToNotify = array_merge($usersToNotify, $this->getConversationParticipants($sender->id, $receiver->id));
        }

        // Remove duplicates and the sender
        $usersToNotify = array_unique($usersToNotify);
        $usersToNotify = array_values(array_filter($usersToNotify, fn($id) => $id != $sender->id));

        Log::info("Final users to notify: " . json_encode($usersToNotify));

        return $usersToNotify;
    }

    /**
     * Get clients assigned to a DEO
     */
    private function getClientsForDEO(int $deoId): array
    {
        return DB::table('clients_data_entry_operators')
            ->where('data_entry_operator_id', $deoId)
            ->pluck('client_id')
            ->toArray();
    }

    /**
     * Check if user is a Client (case-insensitive)
     */
    private function isClientUser(User $user): bool
    {
        $clientTypes = ['Client', 'customer'];
        return in_array(strtolower($user->type), $clientTypes) ||
            DB::table('clients_data_entry_operators')->where('client_id', $user->id)->exists();
    }

    /**
     * Get entire support team for a Client (DEO, Supervisor, Manager)
     */
    private function getSupportTeamForClient(int $clientUserId): array
    {
        $team = [];

        // Get assigned DEO
        $deoId = DB::table('clients_data_entry_operators')
            ->where('client_id', $clientUserId)
            ->value('data_entry_operator_id');

        if ($deoId) {
            $team[] = $deoId;

            // Get DEO's Supervisor
            $supervisorId = DB::table('data_entry_operators_supervisors')
                ->where('data_entry_operator_id', $deoId)
                ->value('supervisor_id');

            if ($supervisorId) {
                $team[] = $supervisorId;

                // Get Supervisor's Manager
                $managerId = DB::table('managers_supervisors')
                    ->where('supervisor_id', $supervisorId)
                    ->value('manager_id');

                if ($managerId) {
                    $team[] = $managerId;
                }
            }

            // Get DEO's direct Manager
            $directManagerId = DB::table('data_entry_operators_managers')
                ->where('data_entry_operator_id', $deoId)
                ->value('manager_id');

            if ($directManagerId && !in_array($directManagerId, $team)) {
                $team[] = $directManagerId;
            }
        }

        return array_filter($team);
    }

    /**
     * Get related users for a DEO (Supervisor, Manager, and their Clients)
     */
    private function getRelatedUsersForDEO(int $deoId): array
    {
        $users = [];

        // Get Supervisor
        $supervisorId = DB::table('data_entry_operators_supervisors')
            ->where('data_entry_operator_id', $deoId)
            ->value('supervisor_id');

        if ($supervisorId) {
            $users[] = $supervisorId;
        }

        // Get Manager
        $managerId = DB::table('data_entry_operators_managers')
            ->where('data_entry_operator_id', $deoId)
            ->value('manager_id');

        if ($managerId) {
            $users[] = $managerId;
        }

        // Get clients assigned to this DEO
        $clientIds = DB::table('clients_data_entry_operators')
            ->where('data_entry_operator_id', $deoId)
            ->pluck('client_id')
            ->toArray();

        $users = array_merge($users, $clientIds);

        return array_filter($users);
    }

    /**
     * Get all participants in a conversation between two users
     */
    private function getConversationParticipants(int $user1Id, int $user2Id): array
    {
        $participants = [$user1Id, $user2Id];

        // Get all unique users who have participated in messages between these two users
        $messageParticipants = DB::table('messages')
            ->select('sender_id', 'receiver_id')
            ->where(function ($query) use ($user1Id, $user2Id) {
                $query->where('sender_id', $user1Id)
                    ->where('receiver_id', $user2Id);
            })
            ->orWhere(function ($query) use ($user1Id, $user2Id) {
                $query->where('sender_id', $user2Id)
                    ->where('receiver_id', $user1Id);
            })
            ->get()
            ->flatMap(fn($msg) => [$msg->sender_id, $msg->receiver_id])
            ->unique()
            ->toArray();

        return array_unique(array_merge($participants, $messageParticipants));
    }

    /**
     * Load attachments for messages
     */
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

    /**
     * Check if column exists in table
     */
    private function columnExists(string $table, string $column): bool
    {
        static $cache = [];
        $key = $table . '.' . $column;
        if (array_key_exists($key, $cache)) return $cache[$key];
        return $cache[$key] = DB::getSchemaBuilder()->hasColumn($table, $column);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        $userId = auth()->id();
        $clientUserId = $request->input('client_user_id');

        if ($clientUserId) {
            DB::table('messages')
                ->where('receiver_id', $userId)
                ->where('sender_id', $clientUserId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get unread message count
     */
    public function getUnreadCount()
    {
        $userId = auth()->id();

        $unreadCount = DB::table('messages')
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }
}
