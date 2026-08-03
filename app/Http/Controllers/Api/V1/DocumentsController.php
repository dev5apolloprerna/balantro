<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentsController extends BaseApiController
{
    public function store(Request $request)
    {
        try {
            $uploadedFiles = $this->uploadedDocumentFiles($request);
            $request->validate([
                'files' => $request->hasFile('files') ? ['required', 'array'] : ['sometimes', 'array'],
                'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,heic,heif,pdf,xls,xlsx', 'max:30720'],
                'document.file' => $request->hasFile('document.file') ? ['required', 'file', 'mimes:jpg,jpeg,png,heic,heif,pdf,xls,xlsx', 'max:30720'] : ['sometimes', 'file'],
                'file' => $request->hasFile('file') ? ['required', 'file', 'mimes:jpg,jpeg,png,heic,heif,pdf,xls,xlsx', 'max:30720'] : ['sometimes', 'file'],
            ]);
        } catch (ValidationException $e) {
            return $this->error($e->validator->errors()->first(), 422, $e->errors());
        }

        if (empty($uploadedFiles)) {
            return $this->error(['Please attach at least one document file.'], 422);
        }

        $user = auth()->user();
        $data = $request->input('document', $request->all());
        unset($data['file'], $data['files']);

        $userDocumentDir = 'documents/' . $user->id;
        $publicDir = public_path($userDocumentDir);

        if (!File::isDirectory($publicDir)) {
            File::makeDirectory($publicDir, 0755, true, true);
        }

        $documents = [];
        $movedPaths = [];

        DB::beginTransaction();
        try {
            foreach ($uploadedFiles as $uploaded) {
                $originalName = $uploaded->getClientOriginalName();
                $ext = $uploaded->getClientOriginalExtension();
                $size = $uploaded->getSize();
                $mime = $uploaded->getMimeType();
                $name = 'document_' . now()->format('Ymd_Hisv') . '_' . Str::random(8) . ($ext ? ".{$ext}" : '');

                $uploaded->move($publicDir, $name);
                $relativePath = $userDocumentDir . '/' . $name;
                $movedPaths[] = public_path($relativePath);

                if (empty($size)) {
                    $size = File::size($publicDir . DIRECTORY_SEPARATOR . $name);
                }

                $documentData = $data;
                $documentData['status'] = $documentData['status'] ?? Document::STATUS_UPLOADED;
                $documentData['file'] = $relativePath;

                $document = $user->documents()->create($documentData);
                $document->files()->create([
                    'path' => $document->file,
                    'original_name' => $originalName,
                    'size' => $size,
                    'mime_type' => $mime,
                ]);

                $documents[] = $document->fresh('files');
                \App\Jobs\DocumentActivityNotificationJob::dispatch($document->id, auth()->id(), 'create')->afterCommit();
            }
             DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            foreach ($movedPaths as $movedPath) {
                if (File::exists($movedPath)) {
                    File::delete($movedPath);
                }
            }

            return $this->error(['Unable to create document'], 617, $e->getMessage());
        }

        $response = count($documents) === 1
            ? $this->documentResponse($documents[0])
            : collect($documents)->map(fn ($document) => $this->documentResponse($document))->values();

        return $this->success(
            __("response_message.document.create_success"),
            $response
        );
    }
    
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $pageLimit = max(1, (int) $request->input('page_limit', 20));
            $page = max(1, (int) $request->input('page', 1));

            // Define valid statuses for client - include both 'processing' and 'in_progress'
            $validStatuses = ['uploaded', 'accepted', 'processing', 'in_progress', 'approved', 'rejected'];

            // Define what "processing/in_progress" means for clients
            $clientPendingStatuses = [
                'accepted',
                'data_entry_in_progress',
                'data_entry_completed',
                'query_raised',
                'query_resolved',
            ];

            // Start building query
            $documents = $user->documents()->latest();

            // Search query (q parameter)
            if ($request->filled('q')) {
                $q = trim((string) $request->input('q'));
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';

                // Subquery for latest file to search in file names
                $latestFileSub = DB::table('files as f')
                    ->select([
                        'f.attachable_id',
                        'f.original_name',
                        DB::raw('ROW_NUMBER() OVER (PARTITION BY f.attachable_id ORDER BY f.created_at DESC) AS rn'),
                    ])
                    ->where('f.attachable_type', Document::class);

                $documents->leftJoinSub($latestFileSub, 'df', function ($join) {
                    $join->on('df.attachable_id', '=', 'documents.id')
                        ->where('df.rn', '=', 1);
                });

                $documents->where(function ($query) use ($like) {
                    $query->where('documents.title', 'like', $like)
                        ->orWhere('documents.reference_no', 'like', $like)
                        ->orWhere('documents.notes', 'like', $like)
                        ->orWhere('df.original_name', 'like', $like);
                });
            }

            // Status filtering (with client-specific logic)
            if ($request->filled('status') && $request->input('status') !== 'all') {
                $requestedStatus = $request->input('status');

                // Validate status
                if (in_array($requestedStatus, $validStatuses, true)) {
                    if ($requestedStatus === 'uploaded') {
                        // When client selects "uploaded", show all relevant statuses
                        $documents->whereIn('status', [
                            'uploaded',
                            'accepted',
                            'data_entry_in_progress',
                            'data_entry_completed',
                            'query_raised',
                            'query_resolved',
                            'approved',
                            'rejected'
                        ]);
                    } elseif ($requestedStatus === 'processing' || $requestedStatus === 'in_progress') {
                        // Map both "processing" and "in_progress" to the pending internal statuses
                        $documents->whereIn('status', $clientPendingStatuses);
                    } else {
                        // Exact status match for other statuses
                        $documents->where('status', $requestedStatus);
                    }
                }
            }

            // Date filtering (treats boundaries as whole days) - matches desktop logic
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->input('start_date'))->startOfDay();
                $end = Carbon::parse($request->input('end_date'))->endOfDay();
                $documents->whereBetween('documents.created_at', [$start, $end]);
            } else {
                if ($request->filled('start_date')) {
                    $documents->where('documents.created_at', '>=', Carbon::parse($request->input('start_date'))->startOfDay());
                }
                if ($request->filled('end_date')) {
                    $documents->where('documents.created_at', '<=', Carbon::parse($request->input('end_date'))->endOfDay());
                }
            }

            // Add file information to response
            $documents->with(['files' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(1);
            }]);

            $paginated = $documents->paginate($pageLimit, ['*'], 'page', $page);
            $rows = Cache::remember("api_dashboard:{$user->id}:document_summary", now()->addMinutes(5), function () use ($user) {
                return DB::select('EXEC dbo.usp_GetClientDocumentSummary ?', [$user->id]);
            });
            $row = $rows[0] ?? (object) [];
            // Enhanced document response with file info
            $responseData = [
                'documents' => $paginated->getCollection()->map(function ($doc) {
                    $latestFile = $doc->files->first();
                    $filePath = $latestFile?->path ?: $doc->file;

                    //$fileUrl = $filePath ? asset($filePath) : null;
                    $fileUrl = $filePath ? $this->fileUrl($doc, $filePath) : null;

                    return [
                        'id' => $doc->id,
                        'title' => $doc->title,
                        'reference_no' => $doc->reference_no,
                        'status' => $doc->status,
                        'notes' => $doc->notes,
                        'created_at' => $doc->created_at?->toISOString(),
                        'updated_at' => $doc->updated_at?->toISOString(),
                        'file_url' => $fileUrl,
                        'file' => $filePath ? [
                            'path' => $filePath,
                            'url' => $fileUrl,
                            'original_name' => $latestFile?->original_name ?: basename($filePath),
                            'size' => $latestFile?->size,
                            'created_at' => $latestFile?->created_at?->toISOString() ?: $doc->created_at?->toISOString(),
                        ] : null
                    ];
                }),
                'current_page' => $paginated->currentPage(),
                'total_pages' => $paginated->lastPage(),
                'total_count' => $paginated->total(),
                'filters' => [
                    'status' => $request->input('status', 'all'),
                    'q' => $request->input('q', ''),
                    'start_date' => $request->input('start_date'),
                    'end_date' => $request->input('end_date'),
                ],
                'document_summary' => [
                    'uploaded_count'    => (int) ($row->uploaded_count    ?? 0),
                    'in_progress_count' => (int) ($row->in_progress_count ?? 0),
                    'completed_count'   => (int) ($row->completed_count   ?? 0),
                    'rejected_count'    => (int) ($row->rejected_count    ?? 0),
                    'accepted_count'    => (int) ($row->accepted_count    ?? 0),
                ],
            ];

            return $this->success(__("response_message.document.index_success"), $responseData);
        } catch (\Exception $e) {
            return $this->error(__("response_message.document.index_error"), 500, $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            // Get the document ID from the request data
            $data = $request->all();
            $documentData = $request->input('document', []);
            $id = $documentData['id'] ?? $data['document']['id'] ?? null;

            if (!$id) {
                return $this->error(__("response_message.document.id_required"), 400);
            }

            $document = Document::findOrFail($id);

            if ($document->user_id != auth()->id()) {
                return $this->error(__("response_message.document.update_unauthorized"), 401);
            }

            $updateData = [
                'status' => $documentData['status'] ?? $document->status,
                'rejection_reason' => $documentData['rejection_reason'] ?? $document->rejection_reason,
                'title' => $documentData['title'] ?? $document->title,
                'reference_no' => $documentData['reference_no'] ?? $document->reference_no,
                'notes' => $documentData['notes'] ?? $document->notes,
            ];

            // ✅ Handle file upload if provided
            if ($request->hasFile('document.file')) {
                $file = $request->file('document.file');
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $filename = 'document_' . time() . '.' . $file->getClientOriginalExtension();
                $destination = public_path('documents');

                // Create folder if not exists
                if (!file_exists($destination)) {
                    mkdir($destination, 0777, true);
                }

                $file->move($destination, $filename);
                $filePath = 'documents/' . $filename;
                $updateData['file'] = $filePath;
                $newFilePayload = [
                    'path' => $filePath,
                    'original_name' => $originalName,
                    'size' => $fileSize,
                ];

                // Optional: Delete old file if exists
                if ($document->file && file_exists(public_path($document->file))) {
                    unlink(public_path($document->file));
                }
            }

            if ($document->update($updateData)) {
                if (isset($newFilePayload)) {
                    $document->files()->create($newFilePayload);
                }
                \App\Jobs\DocumentActivityNotificationJob::dispatch($document->id, auth()->id(), 'update');
                return $this->success(__("response_message.document.update_success"), $this->documentResponse($document));
            } else {
                return $this->error($document->errors()->all(), 617);
            }
        } catch (\Exception $e) {
            return $this->error(__("response_message.document.update_error"), 500, $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = $request->all();
            $id   = $data['document']['id'] ?? null;

            if (!$id) {
                return $this->error(__("response_message.document.id_required"), 400);
            }

            $document = Document::findOrFail($id);

            if ($document->user_id != auth()->id()) {
                return $this->error(__("response_message.document.delete_unauthorized"), 401);
            }

            // 🔗 figure out the stored path (relative like "documents/xyz.pdf" or absolute)
            $storedPath = $document->file ?? $document->file_path ?? null;

            if ($storedPath) {
                // If it's a relative path (e.g., "documents/xyz.pdf"), make it absolute under public/
                $absolutePath = Str::startsWith($storedPath, ['/', '\\', public_path()])
                    ? $storedPath
                    : public_path(ltrim($storedPath, '/'));

                // ✅ delete if it exists and is a file
                if (is_file($absolutePath)) {
                    @unlink($absolutePath);
                }
            }

            // fire notification job
            \App\Jobs\DocumentActivityNotificationJob::dispatch($document->id, auth()->id(), 'delete');

            // delete the DB row
            $document->delete();

            return $this->success(__("response_message.document.delete_success"));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error(__("response_message.document.not_found"), 404);
        } catch (\Exception $e) {
            \Log::error('Document deletion failed', [
                'document_id' => $id ?? 'unknown',
                'user_id'     => auth()->id(),
                'error'       => $e->getMessage()
            ]);

            return $this->error(__("response_message.document.delete_error"), 500, $e->getMessage());
        }
    }

    /**
     * Serve a document through Laravel so IIS does not reject direct requests to
     * the physical public/documents directory.
     */
    public function file(Document $document, string $filename): BinaryFileResponse
    {
        $latestFile = $document->files()->latest('created_at')->first();
        $relativePath = $latestFile?->path ?: $document->file;

        abort_unless($relativePath && hash_equals(basename($relativePath), $filename), 404);

        $documentsRoot = realpath(public_path('documents'));
        $absolutePath = realpath(public_path(ltrim($relativePath, '/\\')));

        abort_unless(
            $documentsRoot
                && $absolutePath
                && is_file($absolutePath)
                && ($absolutePath === $documentsRoot || Str::startsWith($absolutePath, $documentsRoot . DIRECTORY_SEPARATOR)),
            404
        );

        return response()->file($absolutePath);
    }

    /**
     * Return uploaded API document files using the same inputs as the web uploader.
     */
    private function uploadedDocumentFiles(Request $request): array
    {
        if ($request->hasFile('files')) {
            return array_values((array) $request->file('files', []));
        }

        if ($request->hasFile('document.file')) {
            return [$request->file('document.file')];
        }

        if ($request->hasFile('file')) {
            return [$request->file('file')];
        }

        return [];
    }

    /*private function documentResponse(Document $document)
    {
        return [
            'id' => $document->id,
            'status' => $document->status,
            'rejection_reason' => $document->rejection_reason,
            'file_url' => $document->file ? asset(''.$document->file) : null,
            'created_at' => $document->created_at->toDateTimeString(),
            'updated_at' => $document->updated_at->toDateTimeString()
        ];
    }*/

    

    private function documentResponse(Document $document)
    {
        return [
            'id' => $document->id,
            'user_id' => $document->user_id,
            'status' => $document->status,
            'rejection_reason' => $document->rejection_reason,
            'message_id' => $document->message_id,
            // 'file_url' => $document->file ? asset($document->file) : null,
            'file_url' => $document->file ? $this->fileUrl($document, $document->file) : null,
            'file_path' => $document->file,
            'created_at' => $document->created_at->toDateTimeString(),
            'updated_at' => $document->updated_at->toDateTimeString()
        ];
    }

    private function fileUrl(Document $document, string $path): string
    {
        return URL::temporarySignedRoute('api.media.document', now()->addMinutes(30), [
            'document' => $document->getKey(),
            'filename' => basename($path),
        ]);
    }
}
