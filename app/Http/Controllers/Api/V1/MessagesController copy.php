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

class MessagesController extends BaseApiController
{
    // staff types visible to client in listing
    private array $visibleStaffTypes = ['data_entry_operator', 'manager', 'supervisor'];

    // only DEO allowed as receiver on store()
    private string $sendableStaffType = 'data_entry_operator';

    private function getClient(): \App\Models\User
    {

        //$u = $request->user();
        $u = auth()->user();
        $role = $u->role  ?? $u->type ?? null;

        if (!in_array($role, [0, 'client', 'Client'], true)) {
            abort(403, 'Only clients can access this endpoint.');
        }
        return $u;
    }

    // public function index(Request $request)
    // {

    //     //$client = $this->getClient($request);
    //     $client = $this->getClient();

    //     $clientId = (int) $client->id;

    //     $participantId = (int) $request->integer('participant_id') ?: null;
    //     $limit = (int) max(1, min((int) $request->integer('limit') ?: 50, 200));
    //     $orderInPage = strtolower($request->get('order', 'asc')) === 'desc' ? 'desc' : 'asc';
    //     $includeAttachments = (bool) $request->boolean('with_attachments');

    //     $with = ['sender:id,name,type', 'receiver:id,name,type'];
    //     if ($includeAttachments) {
    //         $with[] = 'attachments:id,message_id,original_name,file_name,mime,size,url';
    //     }

    //     // $q = Message::query()
    //     //     ->with(['attachments', 'sender:id,name,type', 'receiver:id,name,type'])
    //     //     ->where(function ($w) use ($clientId) {
    //     //         $w->where('sender_id', $clientId)->orWhere('receiver_id', $clientId);
    //     //     })
    //     //     // ensure the other side is one of the allowed staff types
    //     //     ->where(function ($w) use ($clientId) {
    //     //         $w->whereHas('sender', function ($qq) use ($clientId) {
    //     //             $qq->where('id', '!=', $clientId)->whereIn('type', $this->visibleStaffTypes);
    //     //         })->orWhereHas('receiver', function ($qq) use ($clientId) {
    //     //             $qq->where('id', '!=', $clientId)->whereIn('type', $this->visibleStaffTypes);
    //     //         });
    //     //     })
    //     //     ->orderBy('created_at')->orderBy('id');

    //     $q = Message::query()
    //         ->with(['attachments', 'sender:id,name,type', 'receiver:id,name,type'])
    //         ->where(function ($w) use ($clientId) {
    //             $w->where('sender_id', $clientId)
    //                 ->orWhere('receiver_id', $clientId);
    //         })
    //         ->where(function ($w) use ($clientId) {
    //             $w->whereHas('sender', function ($qq) use ($clientId) {
    //                 $qq->where('id', '!=', $clientId)
    //                     ->whereIn('type', $this->visibleStaffTypes);
    //             })->orWhereHas('receiver', function ($qq) use ($clientId) {
    //                 $qq->where('id', '!=', $clientId)
    //                     ->whereIn('type', $this->visibleStaffTypes);
    //             });
    //         })
    //         ->orderBy('created_at')
    //         ->orderBy('id');

    //     if ($participantId) {
    //         // participant must be one of the visible staff types
    //         $ok = User::query()
    //             ->where('id', $participantId)
    //             ->whereIn('type', $this->visibleStaffTypes)
    //             ->exists();

    //         if (!$ok) {
    //             return response()->json(['ok' => false, 'error' => 'Invalid participant'], 422);
    //         }

    //         $q->where(function ($w) use ($clientId, $participantId) {
    //             $w->where(function ($x) use ($clientId, $participantId) {
    //                 $x->where('sender_id', $clientId)->where('receiver_id', $participantId);
    //             })->orWhere(function ($x) use ($clientId, $participantId) {
    //                 $x->where('sender_id', $participantId)->where('receiver_id', $clientId);
    //             });
    //         });
    //     }

    //     $p = $q->cursorPaginate($limit);

    //     $items = $p->getCollection();
    //     if ($orderInPage === 'desc') {
    //         $items = $items->reverse()->values();
    //     }

    //     $messages = $items->map(function (Message $m) use ($clientId) {
    //         return [
    //             'id' => (int) $m->id,
    //             'from' => [
    //                 'id'   => (int) $m->sender->id,
    //                 'name' => $m->sender->name,
    //                 'type' => $m->sender->type,
    //             ],
    //             'to' => [
    //                 'id'   => (int) $m->receiver->id,
    //                 'name' => $m->receiver->name,
    //                 'type' => $m->receiver->type,
    //             ],
    //             'description' => $m->description,
    //             'attachments' => $m->relationLoaded('attachments')
    //                 ? $m->attachments->map(fn($a) => [
    //                     'original_name' => $a->original_name,
    //                     'file_name'     => $a->file_name,
    //                     'mime'          => $a->mime,
    //                     'size'          => (int) $a->size,
    //                     'url'           => $a->url,
    //                 ])->values()
    //                 : [],
    //             'created_at' => optional($m->created_at)->toIso8601String(),
    //             'from_me'    => $m->sender_id === $clientId,
    //         ];
    //     });

    //     return response()->json([
    //         'ok' => true,
    //         'messages' => $messages,
    //         'next_cursor' => $p->nextCursor()?->encode(),
    //         'prev_cursor' => $p->previousCursor()?->encode(),
    //     ]);
    // }

    public function index(Request $request)
    {

        //$client = $this->getClient($request);
        $client = $this->getClient();

        $clientId = (int) $client->id;
        $deoId = (int) DB::table('clients_data_entry_operators')
            ->where('client_id', $clientId)
            ->value('data_entry_operator_id');

        $selected_user = User::query()
            ->where('id', $deoId)
            ->whereIn('type', ['data_entry_operator', 'DataEntryOperator', 'deo']) // adapt to your enum
            ->firstOrFail(['id', 'name', 'type']);
        $allowed = [$clientId, $deoId];

        // $participantId = (int) $request->integer('participant_id') ?: null;
        // $limit = (int) max(1, min((int) $request->integer('limit') ?: 50, 200));
        // $orderInPage = strtolower($request->get('order', 'asc')) === 'desc' ? 'desc' : 'asc';
        // $includeAttachments = (bool) $request->boolean('with_attachments');

        // $with = ['sender:id,name,type', 'receiver:id,name,type'];
        // if ($includeAttachments) {
        //     $with[] = 'attachments:id,message_id,original_name,file_name,mime,size,url';
        // }

        // $q = Message::query()
        //     ->with($with)
        //     ->where(function ($w) use ($clientId) {
        //         $w->where('sender_id', $clientId)
        //             ->orWhere('receiver_id', $clientId);
        //     })
        //     ->where(function ($w) use ($clientId) {
        //         $w->whereHas('sender', function ($qq) use ($clientId) {
        //             $qq->where('id', '!=', $clientId)
        //                 ->whereIn('type', $this->visibleStaffTypes);
        //         })->orWhereHas('receiver', function ($qq) use ($clientId) {
        //             $qq->where('id', '!=', $clientId)
        //                 ->whereIn('type', $this->visibleStaffTypes);
        //         });
        //     })
        //     ->orderBy('created_at')
        //     ->orderBy('id');
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

        // if ($participantId) {
        //     // participant must be one of the visible staff types
        //     $ok = User::query()
        //         ->where('id', $participantId)
        //         ->whereIn('type', $this->visibleStaffTypes)
        //         ->exists();

        //     if (!$ok) {
        //         return response()->json(['ok' => false, 'error' => 'Invalid participant'], 422);
        //     }

        //     $q->where(function ($w) use ($clientId, $participantId) {
        //         $w->where(function ($x) use ($clientId, $participantId) {
        //             $x->where('sender_id', $clientId)->where('receiver_id', $participantId);
        //         })->orWhere(function ($x) use ($clientId, $participantId) {
        //             $x->where('sender_id', $participantId)->where('receiver_id', $clientId);
        //         });
        //     });
        // }

        // $p = $q->cursorPaginate($limit);

        // $items = $p->getCollection();
        // if ($orderInPage === 'desc') {
        //     $items = $items->reverse()->values();
        // }

        $messages = $messages->map(function (Message $m) use ($clientId) {
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
            'messages' => $messages
        ]);
    }

    public function store(Request $request)
    {

        $client   = $this->getClient();
        $clientId = (int) $client->id;

        // $request->validate([
        //     // If you want to force at least text or file, change to: 'required_without:attachments'
        //     'description'    => ['nullable', 'string'],
        //     'attachments'    => ['sometimes', 'array'],       // important when sending multiple files
        //     'attachments.*'  => ['file', 'max:20480'],        // 20 MB per file
        // ]);
        $validator = Validator::make($request->all(), [
            'description' => ['nullable', 'string'],
            'attachments.*' => ['file', 'max:20480']
        ]);
        if ($validator->fails()) {
            // return $this->error($validator->errors()->all(), 617);
            return response()->json([
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }
        // 1) Find assigned DEO
        $receiverId = DB::table('clients_data_entry_operators')
            ->where('client_id', $clientId)
            ->value('data_entry_operator_id');

        if (!$receiverId) {
            return response()->json(['ok' => false, 'error' => 'No DEO assigned to this client'], 422);
        }

        // 2) Create message
        $message = Message::create([
            'sender_id'   => $clientId,
            'receiver_id' => $receiverId,
            'description' => (string) ($request->input('description') ?? ''),
        ]);

        // 3) Handle attachments (single or multiple)
        // Always use $request->file('attachments') to get UploadedFile instances
        $files = $request->file('attachments'); // UploadedFile|array|null

        if ($files) {
            if (!is_array($files)) {
                $files = [$files]; // normalize single file
            }

            $dir = public_path('chat/' . $message->id);
            if (!is_dir($dir)) {
                if (!@mkdir($dir, 0775, true) && !is_dir($dir)) {
                    return response()->json(['ok' => false, 'error' => 'Failed to create upload directory'], 500);
                }
            }

            foreach ($files as $file) {
                if (!$file || !$file->isValid()) {
                    // optionally abort(422, 'Invalid upload');
                    continue;
                }

                // ⚠️ capture metadata BEFORE move()
                $originalName = $file->getClientOriginalName();
                $clientMime   = $file->getClientMimeType();
                $tmpSize      = (int) $file->getSize(); // size of the temp upload
                $ext          = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin';

                $fileName = time() . '_' . Str::random(12) . '.' . strtolower($ext);

                // Move to /public/chat/{message_id}/
                $file->move($dir, $fileName);

                // If you prefer size after move (filesystem-level), compute it here:
                $finalPath = $dir . DIRECTORY_SEPARATOR . $fileName;
                $finalSize = is_file($finalPath) ? (int) filesize($finalPath) : $tmpSize;

                $url = asset('chat/' . $message->id . '/' . $fileName);

                MessageAttachment::create([
                    'message_id'    => $message->id,
                    'original_name' => $originalName,
                    'file_name'     => $fileName,
                    'mime'          => $clientMime,
                    'size'          => $finalSize,
                    'url'           => $url,
                ]);
            }
        }

        // 4) Return with relations
        $message->load([
            'sender:id,name,type',
            'receiver:id,name,type',
            'attachments:id,message_id,original_name,file_name,mime,size,url',
        ]);

        return response()->json([
            'ok'      => true,
            'message' => $message,
        ], 201);
    }

    private function messageResponse($message)
    {
        return [
            'description' => $message->description,
            'attachments' => $message->documents->map(function ($doc) {
                $isImage = str_starts_with($doc->file_type, 'image');
                $iconHtml = $isImage ?
                    '<iconify-icon icon="heroicons:photo" class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>' :
                    '<iconify-icon icon="heroicons:document" class="text-lg"></iconify-icon>';

                return [
                    'name' => $doc->file_name,
                    'file_icon' => $iconHtml,
                    'url' => asset('storage/' . $doc->file_path)
                ];
            }),
            'status' => $message->sender_id == auth()->id() ? "sent" : "received",
            'timestamp' => $message->created_at->format('Y-m-d H:i:s')
        ];
    }
}
