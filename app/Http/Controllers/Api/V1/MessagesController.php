<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Services\FirebaseNotificationService;

class MessagesController extends BaseApiController
{
    // staff types visible to client in listing
    private array $visibleStaffTypes = ['data_entry_operator', 'manager', 'supervisor'];

    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    private function getClient(): \App\Models\User
    {
        $u = auth()->user();
        $role = $u->role  ?? $u->type ?? null;

        if (!in_array($role, [0, 'client', 'Client'], true)) {
            abort(403, 'Only clients can access this endpoint.');
        }
        return $u;
    }

    public function index(Request $request)
    {
        $client = $this->getClient();
        $clientId = (int) $client->id;

        // Get pagination parameters
        $limit = (int) max(1, min($request->integer('limit', 50), 200));
        $page = (int) max(1, $request->integer('page', 1));
        $order = strtolower($request->get('order', 'desc')) === 'asc' ? 'asc' : 'desc';

        // Get assigned DEO
        $deoId = (int) DB::table('clients_data_entry_operators')
            ->where('client_id', $clientId)
            ->value('data_entry_operator_id');

        if (!$deoId) {
            return response()->json([
                'ok' => false,
                'error' => 'No DEO assigned to this client'
            ], 422);
        }

        $allowed = [$clientId, $deoId];

        // Build query with proper relationship loading
        $query = Message::query()
            ->with([
                'sender:id,name,type',
                'receiver:id,name,type',
                'attachments:id,message_id,original_name,file_name,mime,size,url'
            ])
            ->where(function ($q) use ($allowed) {
                $q->whereIn('sender_id', $allowed)
                    ->whereIn('receiver_id', $allowed);
            })
            ->orderBy('created_at', $order)
            ->orderBy('id', $order);

        // Paginate results
        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        $messages = $paginator->getCollection()->map(function (Message $m) use ($clientId) {
            return [
                'id' => (int) $m->id,
                'from' => [
                    'id'   => (int) $m->sender->id,
                    'name' => $m->sender->name,
                    'type' => $m->sender->type,
                ],
                'to' => [
                    'id'   => (int) $m->receiver->id,
                    'name' => $m->receiver->name,
                    'type' => $m->receiver->type,
                ],
                'description' => $m->description,
                'attachments' => $m->relationLoaded('attachments')
                    ? $m->attachments->map(fn($a) => [
                        'original_name' => $a->original_name,
                        'file_name'     => $a->file_name,
                        'mime'          => $a->mime,
                        'size'          => (int) $a->size,
                        'url'           => $a->url,
                    ])->values()
                    : [],
                'created_at' => optional($m->created_at)->toIso8601String(),
                'from_me'    => $m->sender_id === $clientId,
            ];
        });

        return response()->json([
            'ok' => true,
            'messages' => $messages,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $client = $this->getClient();
        $clientId = (int) $client->id;

        $validator = Validator::make($request->all(), [
            'description' => ['nullable', 'string', 'required_without:attachments'],
            'attachments' => ['sometimes', 'array'],
            'attachments.*' => ['file', 'max:20480']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find assigned DEO
        $deoId = DB::table('clients_data_entry_operators')
            ->where('client_id', $clientId)
            ->value('data_entry_operator_id');

        if (!$deoId) {
            return response()->json([
                'ok' => false,
                'error' => 'No DEO assigned to this client'
            ], 422);
        }

        DB::beginTransaction();
        //try {
            // Create message
            $message = Message::create([
                'sender_id'   => $clientId,
                'receiver_id' => $deoId,
                'description' => trim($request->input('description', '')),
				// 'created_at' => now('Asia/Kolkata')
            ]);

            // Handle attachments
            $files = $request->file('attachments');
            $hasFiles = false;

            if ($files) {
                $hasFiles = true;
                if (!is_array($files)) {
                    $files = [$files];
                }
                $this->handleFileUploads($message->id, $files);
            }

            DB::commit();

            // Send notifications after successful message creation - PASS MESSAGE ID
            $this->sendNotifications($client, $deoId, $request->input('description', ''), $hasFiles, $message->id);

            // Load relationships for response
            $message->load([
                'sender:id,name,type',
                'receiver:id,name,type',
                'attachments:id,message_id,original_name,file_name,mime,size,url',
            ]);

            return response()->json([
                'ok'      => true,
                'message' => $this->formatMessageResponse($message, $clientId),
            ], 201);
        // } catch (\Throwable $e) {
        //     DB::rollBack();
        //     \Log::error('Message sending failed: ' . $e->getMessage());

        //     return response()->json([
        //         'ok' => false,
        //         'error' => 'Failed to send message. Please try again.'
        //     ], 500);
        // }
    }

    /**
     * Handle file uploads for the message
     */
    private function handleFileUploads(int $messageId, array $files): void
    {
        $dir = public_path('chat/' . $messageId);
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \Exception('Failed to create upload directory');
            }
        }

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $clientMime   = $file->getClientMimeType();
            $tmpSize      = (int) $file->getSize();
            $ext          = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin';

            $fileName = time() . '_' . Str::random(12) . '.' . strtolower($ext);
            $file->move($dir, $fileName);

            $finalPath = $dir . DIRECTORY_SEPARATOR . $fileName;
            $finalSize = is_file($finalPath) ? (int) filesize($finalPath) : $tmpSize;

            $url = asset('chat/' . $messageId . '/' . $fileName);

            MessageAttachment::create([
                'message_id'    => $messageId,
                'original_name' => $originalName,
                'file_name'     => $fileName,
                'mime'          => $clientMime,
                'size'          => $finalSize,
                'url'           => $url,
            ]);
        }
    }


    private function sendNotifications($client, int $deoId, string $messageText, bool $hasFiles, int $messageId): void
    {
        // try {
            $clientId = (int) $client->id;

            \Log::info('Starting notification process - USING WEB CONTROLLER APPROACH', [
                'client_id' => $clientId,
                'deo_id' => $deoId,
                'message_length' => strlen($messageText),
                'has_files' => $hasFiles
            ]);

            // Use the SAME approach as your working web controller
            $sender = User::find($clientId);
            $receiver = User::find($deoId);

            if (!$sender || !$receiver) {
                \Log::warning('Sender or receiver not found', [
                    'sender_id' => $clientId,
                    'receiver_id' => $deoId
                ]);
                return;
            }

            // Prepare notification content - SAME as web controller
            $title = "New message from {$sender->name}";
            $body = strlen($messageText) > 100
                ? substr($messageText, 0, 100) . '...'
                : $messageText;

            if (empty(trim($messageText)) && $hasFiles) {
                $body = '📎 Attachment';
            }

            $data = [
                //'message_id' => (string) $deoId, // You'll need to pass the actual message ID
                'message_id' => (string) $messageId,  // ✅ CORRECT
                'sender_id' => (string) $clientId,
                'sender_name' => $sender->name,
                'receiver_id' => (string) $deoId,
                'message_preview' => $body,
                'timestamp' => now()->toISOString(),
                'type' => 'chat_message',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ];

            \Log::info('Prepared notification data', [
                'title' => $title,
                'body' => $body,
                'data' => $data
            ]);

            // METHOD 1: Send to specific receiver (like web controller)
            $result = $this->firebaseService->sendToUser($deoId, $title, $body, $data);
            
            \Log::info('Direct notification result', ['result' => $result]);

            // METHOD 2: Notify related support staff (like web controller)
            $this->notifyRelatedSupportStaff($clientId, $title, $body, $data);
        // } catch (\Exception $e) {
        //     \Log::error('Failed to send message notification: ' . $e->getMessage(), [
        //         'client_id' => $client->id,
        //         'deo_id' => $deoId,
        //         'trace' => $e->getTraceAsString()
        //     ]);
        // }
    }

    private function notifyRelatedSupportStaff(int $clientUserId, string $title, string $body, array $data)
    {
        try {
            // Use the SAME logic as your working web controller
            $clientId = $this->resolveClientIdFromUserId($clientUserId);

            if (!$clientId) {
                \Log::warning('Could not resolve client ID from user ID', ['user_id' => $clientUserId]);
                return;
            }

            $supportUserIds = [];

            // Get DEO - SAME as web controller
            $deo = DB::table('clients_data_entry_operators')
                ->where('client_id', $clientId)
                ->value('data_entry_operator_id');
            if ($deo) $supportUserIds[] = $deo;

            // Get Supervisor - SAME as web controller
            $supervisor = DB::table('clients_supervisors')
                ->where('client_id', $clientId)
                ->value('supervisor_id');
            if ($supervisor) $supportUserIds[] = $supervisor;

            // Get Manager - SAME as web controller
            $manager = DB::table('clients_managers')
                ->where('client_id', $clientId)
                ->value('manager_id');
            if ($manager) $supportUserIds[] = $manager;

            // Remove duplicates and the original client
            $supportUserIds = array_unique($supportUserIds);
            $supportUserIds = array_filter($supportUserIds, fn($id) => $id !== $clientUserId);

            \Log::info('Support staff to notify', ['user_ids' => $supportUserIds]);

            // Send to all support staff - SAME as web controller
            if (!empty($supportUserIds)) {
                $results = $this->firebaseService->sendToUsers($supportUserIds, $title, $body, $data);
                \Log::info("Notified support staff", [
                    'user_ids' => $supportUserIds,
                    'results' => $results
                ]);
            } else {
                \Log::warning('No support staff found to notify');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to notify support staff: ' . $e->getMessage());
        }
    }

    /**
     * Resolve client ID from user ID - FROM WEB CONTROLLER
     */
    private function resolveClientIdFromUserId(?int $userId): ?int
    {
        return $userId
            ? DB::table('users')->where('id', $userId)->value('id')
            : null;
    }

    private function getConversationActors(int $clientId, int $deoId): array
    {
        \Log::info('Getting conversation actors', ['client_id' => $clientId, 'deo_id' => $deoId]);

        // Get supervisors for this DEO
        $supervisorIds = DB::table('data_entry_operators_supervisors')
            ->where('data_entry_operator_id', $deoId)
            ->pluck('supervisor_id')
            ->map(fn($v) => (int) $v)
            ->all();

        \Log::info('Supervisors found', ['supervisor_ids' => $supervisorIds]);

        // Get managers for this client
        $managerIds = DB::table('clients_managers')
            ->where('client_id', $clientId)
            ->pluck('manager_id')
            ->map(fn($v) => (int) $v)
            ->all();

        \Log::info('Managers found', ['manager_ids' => $managerIds]);

        $allActors = array_values(array_unique(array_filter([
            $clientId,
            $deoId,
            ...$supervisorIds,
            ...$managerIds,
        ], fn($v) => $v)));

        \Log::info('All actors combined', ['actors' => $allActors]);

        return $allActors;
    }

    /**
     * Create a preview of the message for the notification
     */
    private function getMessagePreview(string $messageText, bool $hasFiles): string
    {
        if (!empty(trim($messageText))) {
            $text = trim($messageText);
            return strlen($text) > 100
                ? substr($text, 0, 100) . '...'
                : $text;
        }

        if ($hasFiles) {
            return '📎 Attachment';
        }

        return 'New message';
    }

    /**
     * Format message for API response
     */
    private function formatMessageResponse(Message $message, int $clientId): array
    {
        return [
            'id' => (int) $message->id,
            'from' => [
                'id'   => (int) $message->sender->id,
                'name' => $message->sender->name,
                'type' => $message->sender->type,
            ],
            'to' => [
                'id'   => (int) $message->receiver->id,
                'name' => $message->receiver->name,
                'type' => $message->receiver->type,
            ],
            'description' => $message->description,
            'attachments' => $message->relationLoaded('attachments')
                ? $message->attachments->map(fn($a) => [
                    'original_name' => $a->original_name,
                    'file_name'     => $a->file_name,
                    'mime'          => $a->mime,
                    'size'          => (int) $a->size,
                    'url'           => $a->url,
                ])->values()
                : [],
            'created_at' => optional($message->created_at)->toIso8601String(),
            'from_me'    => $message->sender_id === $clientId,
        ];
    }

    /**
     * Legacy method for compatibility (if needed)
     */
    private function messageResponse($message)
    {
        return [
            'description' => $message->description,
            'attachments' => $message->attachments->map(function ($attachment) {
                $isImage = str_starts_with($attachment->mime, 'image');
                $iconHtml = $isImage ?
                    '<iconify-icon icon="heroicons:photo" class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>' :
                    '<iconify-icon icon="heroicons:document" class="text-lg"></iconify-icon>';

                return [
                    'name' => $attachment->original_name,
                    'file_icon' => $iconHtml,
                    'url' => $attachment->url
                ];
            }),
            'status' => $message->sender_id == auth()->id() ? "sent" : "received",
            'timestamp' => $message->created_at->format('Y-m-d H:i:s')
        ];
    }

    // Add to MessagesController
    public function debugFirebaseFile()
    {
        try {
            $configPath = config('firebase.credentials.file');
            $envPath = env('FIREBASE_CREDENTIALS');
            
            \Log::info('Firebase Debug Info:', [
                'config_path' => $configPath,
                'env_path' => $envPath,
                'storage_path' => storage_path(),
                'base_path' => base_path(),
            ]);
            
            // Check if file exists
            if (!file_exists($configPath)) {
                return response()->json([
                    'ok' => false,
                    'error' => 'File does not exist: ' . $configPath,
                    'files_in_storage_app' => scandir(storage_path('app')),
                    'files_in_firebase_dir' => file_exists(storage_path('app/firebase')) 
                        ? scandir(storage_path('app/firebase')) 
                        : 'firebase directory does not exist'
                ]);
            }
            
            // Check file permissions
            $fileInfo = [
                'exists' => true,
                'size' => filesize($configPath),
                'permissions' => substr(sprintf('%o', fileperms($configPath)), -4),
                'readable' => is_readable($configPath),
                'writable' => is_writable($configPath),
            ];
            
            // Check JSON validity
            $content = file_get_contents($configPath);
            $json = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Invalid JSON: ' . json_last_error_msg(),
                    'file_info' => $fileInfo
                ]);
            }
            
            // Check required fields
            $required = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email'];
            $missing = array_diff($required, array_keys($json));
            
            if (!empty($missing)) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Missing required fields: ' . implode(', ', $missing),
                    'file_info' => $fileInfo,
                    'available_fields' => array_keys($json)
                ]);
            }
            
            return response()->json([
                'ok' => true,
                'file_info' => $fileInfo,
                'project_id' => $json['project_id'],
                'client_email' => $json['client_email'],
                'private_key_starts' => substr($json['private_key'], 0, 50) . '...'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
