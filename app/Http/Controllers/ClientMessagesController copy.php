<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Services\FirebaseNotificationService;


class ClientMessagesController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    private function sendMessageNotification(int $senderId, int $receiverId, string $messageBody, int $messageId)
    {
        try {
            $sender = User::find($senderId);
            $receiver = User::find($receiverId);

            if (!$sender || !$receiver) {
                return;
            }

            // Prepare notification content
            $title = "New message from {$sender->name}";
            $body = strlen($messageBody) > 100
                ? substr($messageBody, 0, 100) . '...'
                : $messageBody;

            $data = [
                'message_id' => (string) $messageId,
                'sender_id' => (string) $senderId,
                'sender_name' => $sender->name,
                'receiver_id' => (string) $receiverId,
                'message_preview' => $body,
                'timestamp' => now()->toISOString(),
                'type' => 'chat_message',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ];

            // Send notification to the specific receiver
            $result = $this->firebaseService->sendToUser($receiverId, $title, $body, $data);

            // Log the result
            if ($result['success_count'] > 0) {
                Log::info("Notification sent to user {$receiverId}", $result);
            } else {
                Log::warning("No notifications delivered to user {$receiverId}", $result);
            }

            // Notify related support staff
            $this->notifyRelatedSupportStaff($senderId, $title, $body, $data);
        } catch (\Exception $e) {
            \Log::error('Failed to send message notification: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $clientId = (int) auth()->id();

        // 1) Resolve the assigned DEO for this client (adjust table/column names if different)
        $deoId = (int) DB::table('clients_data_entry_operators')
            ->where('client_id', $clientId)
            ->value('data_entry_operator_id');

        abort_if(!$deoId, 404, 'No support user (DEO) assigned for this client.');

        // Optional safety: ensure the counterpart is actually a DEO
        $selected_user = User::query()
            ->where('id', $deoId)
            ->whereIn('type', ['data_entry_operator', 'DataEntryOperator', 'deo']) // adapt to your enum
            ->firstOrFail(['id', 'name', 'type']);

        // 2) Only messages where BOTH ends are either the client or the DEO
        $allowed = [$clientId, $deoId];

        $messages = Message::query()
            ->with(['attachments', 'sender:id,type'])
            ->whereIn('sender_id',   $allowed)
            ->orWhereIn('receiver_id', $allowed)
            ->orderBy('created_at')
            ->get()
            ->map(function ($m) {
                // normalize body/description
                $m->body = $m->description ?? $m->body;
                return $m;
            });
        return view('clients.messages.index', compact('selected_user', 'messages'));
    }

    public function store(Request $request)
    {
        $me = auth()->id();
        abort_unless($me, 401);

        // Validate "text OR files"
        $validated = $request->validate([
            'to_user_id' => 'required|integer|exists:users,id',
            'body'      => 'nullable|string|max:5000|required_without:files',
            'files'      => ['nullable'],
            'files.*'    => ['file', 'max:20480'], // 20 MB
        ]);

        if (!$request->filled('body') && !$request->hasFile('files')) {
            return $this->fail($request, 422, 'Type a message or attach a file.');
        }

        // Resolve client & receiver
        $clientId = $this->resolveClientIdFromUserId($me);
        $to = $validated['to_user_id']
            ?? $this->resolveSupportUserIdForClient($clientId)
            ?? $this->resolveDefaultSupportUserId();

        if (!$to) {
            return $this->fail($request, 422, 'No support user is linked to this client.');
        }
        if ((int)$to === (int)$me) {
            $fallback = $this->resolveDefaultSupportUserId();
            if ($fallback) $to = $fallback;
            else return $this->fail($request, 422, 'Receiver resolved to the same user.');
        }

        // DB::beginTransaction();
        try {
            // 1) Insert message
            $messageId = DB::table('messages')->insertGetId([
                'sender_id'   => $me,
                'receiver_id' => $to,
                'description' => trim($request->input('body', '')),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // 2) Save attachments to /public/chat/{messageId} and record in the right table
            $files = $request->file('files', []);
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
                    // elseif ($hasDoc) {
                    //     // Back-compat: documents table
                    //     DB::table('documents')->insert([
                    //         'user_id'    => $me,
                    //         'status'     => 'uploaded',
                    //         'message_id' => $messageId,
                    //         'file'       => $relative,
                    //         'created_at' => now(),
                    //         'updated_at' => now(),
                    //     ]);
                    // }
                }
            }

            // DB::commit();

            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message_id' => $messageId]);
            }
            return back()->with('success', 'Message sent');
        } catch (\Throwable $e) {
            // DB::rollBack();
            report($e);
            return $this->fail($request, 500, 'Failed to send message.');
        }
    }

    /**
     * Load attachments for a set of message IDs from either message_attachments (preferred)
     * or documents (fallback). Returns: [message_id => [ {name,url,mime,size,id,status?}, ... ]]
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

    /* ===== helpers ===== */

    // Expects a CLIENT ID (not user id)
    protected function resolveSupportUserForClientId(?int $clientId): ?int
    {
        if (!$clientId) return null;

        if ($id = DB::table('clients_data_entry_operators')->where('client_id', $clientId)->value('data_entry_operator_id')) {
            return (int) $id;
        }
        if ($id = DB::table('clients_supervisors')->where('client_id', $clientId)->value('supervisor_id')) {
            return (int) $id;
        }
        if ($id = DB::table('clients_managers')->where('client_id', $clientId)->value('manager_id')) {
            return (int) $id;
        }
        return null;
    }

    protected function defaultSupportUserId(): ?int
    {
        $id = config('support.default_user_id'); // set in .env if you want a fallback
        return $id ? (int) $id : null;
    }

    protected function fail(\Illuminate\Http\Request $request, int $code, string $message)
    {
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], $code);
        }
        return back()->withErrors(['error' => $message])->withInput();
    }


    private function resolveSupportUserIdForClient(int $clientId): ?int
    {
        $deo = DB::table('clients_data_entry_operators')->where('client_id', $clientId)->value('data_entry_operator_id');
        if ($deo) return (int) $deo;

        $sup = DB::table('clients_supervisors')->where('client_id', $clientId)->value('supervisor_id');
        if ($sup) return (int) $sup;

        $mgr = DB::table('clients_managers')->where('client_id', $clientId)->value('manager_id');
        if ($mgr) return (int) $mgr;



        $admin = DB::table('users')->where('role', \App\Models\User::ROLES['super_admin'])->value('id');
        return $admin ? (int) $admin : null;
    }

    private function resolveClientIdFromUserId(?int $userId): ?int
    {
        // if (!$userId) return null;
        // return DB::table('clients')->where('user_id', $userId)->value('id');
        return $userId
            ? DB::table('users')->where('id', $userId)->value('id')
            : null;
    }

    /**
     * Optional default support user (config/support.php → DEFAULT_SUPPORT_USER_ID in .env)
     */
    private function resolveDefaultSupportUserId(): ?int
    {
        $id = config('support.default_user_id');
        return $id ? (int) $id : null;
    }

    /**
     * Small helper to check if a column exists (so we can set client_id only if available)
     */
    private function columnExists(string $table, string $column): bool
    {
        static $cache = [];
        $key = $table . '.' . $column;
        if (array_key_exists($key, $cache)) return $cache[$key];

        $exists = DB::getSchemaBuilder()->hasColumn($table, $column);
        $cache[$key] = $exists;
        return $exists;
    }
}
