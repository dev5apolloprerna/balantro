<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DocumentsController extends BaseApiController
{
    public function store(Request $request)
    {
        $data = $request->all();
        $filePath = null;

        // ✅ Handle file upload
        if ($request->hasFile('document.file')) {
            $file = $request->file('document.file');
            //$filename = time() . '_' . $file->getClientOriginalName();
            $filename = 'document_' . time() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('documents');

            // Create folder if not exists
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);
            $filePath = 'documents/' . $filename;
        }

        // Add file path to data
        if ($filePath) {
            $data['file'] = $filePath;
        }

        $document = auth()->user()->documents()->create($data);
        if ($document) {
            \App\Jobs\DocumentActivityNotificationJob::dispatch($document->id, auth()->id(), 'create');
            return $this->success(
                __("response_message.document.create_success"),
                $this->documentResponse($document)
                
            );
        } else {
            return $this->error(['Unable to create document'], 617);
        }
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

                    return [
                        'id' => $doc->id,
                        'title' => $doc->title,
                        'reference_no' => $doc->reference_no,
                        'status' => $doc->status,
                        'notes' => $doc->notes,
                        'created_at' => $doc->created_at?->toISOString(),
                        'updated_at' => $doc->updated_at?->toISOString(),
                        'file' => $latestFile ? [
                            'path' => $latestFile->path,
                            'original_name' => $latestFile->original_name,
                            'size' => $latestFile->size,
                            'created_at' => $latestFile->created_at?->toISOString(),
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
            $id = $data['document']['id'] ?? null;

            if (!$id) {
                return $this->error(__("response_message.document.id_required"), 400);
            }

            $document = Document::findOrFail($id);

            if ($document->user_id != auth()->id()) {
                return $this->error(__("response_message.document.update_unauthorized"), 401);
            }

            $updateData = [
                'status' => $data['document']['status'] ?? $document->status,
                'rejection_reason' => $data['document']['rejection_reason'] ?? $document->rejection_reason,
            ];

            // ✅ Handle file upload if provided
            if ($request->hasFile('document.file')) {
                $file = $request->file('document.file');
                $filename = 'document_' . time() . '.' . $file->getClientOriginalExtension();
                $destination = public_path('documents');

                // Create folder if not exists
                if (!file_exists($destination)) {
                    mkdir($destination, 0777, true);
                }

                $file->move($destination, $filename);
                $filePath = 'documents/' . $filename;
                $updateData['file'] = $filePath;

                // Optional: Delete old file if exists
                if ($document->file && file_exists(public_path($document->file))) {
                    unlink(public_path($document->file));
                }
            }

            if ($document->update($updateData)) {
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
            'file_url' => $document->file ? asset($document->file) : null,
            'file_path' => $document->file,
            'created_at' => $document->created_at->toDateTimeString(),
            'updated_at' => $document->updated_at->toDateTimeString()
        ];
    }
}
