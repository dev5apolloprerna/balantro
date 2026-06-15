<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Document;
use App\Models\User;
use App\Jobs\DocumentActivityNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait DocumentVerifiable
{
    use DocumentsConcern;

    /**
     * Initialize the trait.
     */
    public function initializeDocumentVerifiable()
    {
        $this->middleware(function ($request, $next) {
            if (in_array($request->route()->getActionMethod(), ['download', 'verify', 'docActivities'])) {
                $this->setDocument($request);
            }
            return $next($request);
        });
    }

    /**
     * Bulk update documents status
     */
    public function bulkUpdate(Request $request)
    {
        if (!$request->has('document_ids') || !$request->has('status')) {
            return back()->with('alert', __('common.flash.bulk_update.no_selection'));
        }

        return $this->performBulkUpdate($request->document_ids, $request->status);
    }

    /**
     * Get document activities
     */
    public function docActivities()
    {
        $docActivities = $this->document->versions()->orderBy('created_at', 'desc')->get();
        $userIds = $docActivities->pluck('whodunnit')->unique()->filter()->values();
        $usersById = User::whereIn('id', $userIds)->get()->keyBy('id');

        return view('documents.activities', [
            'docActivities' => $docActivities,
            'usersById' => $usersById
        ]);
    }

    /**
     * Download document
     */
    public function download()
    {
        if ($this->document->file) {
            return Storage::download(
                $this->document->file->path,
                $this->document->file->original_name,
                [
                    'Content-Type' => $this->document->file->mime_type
                ]
            );
        }

        return redirect()->route($this->namespacePrefix() . '.documents.index')
            ->with('alert', __($this->namespacePrefix() . '.documents.controller.download.file_not_attached'));
    }

    /**
     * Verify document
     */
    public function verify(Request $request)
    {
        $status = $request->input('document.status');
        $rejectionReason = $request->input('document.rejection_reason');

        $errorMessage = null;

        if ($status === 'rejected' && empty($rejectionReason)) {
            $errorMessage = __($this->namespacePrefix() . '.documents.controller.verify.rejection_reason_required');
        } elseif (!$this->document->validStatusTransition($status)) {
            $errorMessage = __('common.flash.bulk_update.invalid_transition');
        }

        if ($errorMessage) {
            return response()->json([
                'status' => 'error',
                'error' => $errorMessage
            ], 422);
        }

        return $this->updateDocumentWithComment($request);
    }

    /**
     * Perform bulk update
     */
    protected function performBulkUpdate($documentIds, $status)
    {
        $clientIds = Auth::user()->clients->pluck('id');
        $documents = Document::whereIn('user_id', $clientIds)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $successIds = [];
        $failedUpdates = [];

        DB::beginTransaction();
        try {
            Document::whereIn('id', $documentIds)->each(function ($document) use ($status, &$successIds, &$failedUpdates) {
                if ($document->validStatusTransition($status)) {
                    if ($document->update(['status' => $status])) {
                        $successIds[] = $document->id;
                    } else {
                        $failedUpdates[] = [
                            'id' => $document->id,
                            'error' => $document->errors()->all()
                        ];
                    }
                } else {
                    $failedUpdates[] = [
                        'id' => $document->id,
                        'error' => __('common.flash.bulk_update.invalid_transition')
                    ];
                }
            });

            DB::commit();
            return $this->renderBulkUpdateResponse($successIds, $documents);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('alert', __('common.flash.bulk_update.failed', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Update document with comment
     */
    protected function updateDocumentWithComment(Request $request)
    {
        $success = false;

        DB::beginTransaction();
        try {
            if ($this->document->update($this->documentParams($request))) {
                if ($request->has('comment_type') && $request->has('document_comment_description')) {
                    $this->document->documentComments()->create([
                        'document_id' => $this->document->id,
                        'comment_type' => $request->comment_type,
                        'description' => $request->document_comment_description,
                        'commented_by_type' => get_class(Auth::user()),
                        'commented_by_id' => Auth::id()
                    ]);
                }
                $success = true;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
        }

        if ($success) {
            DocumentActivityNotificationJob::dispatch($this->document->id, Auth::id(), 'status_change');
        }

        return $this->handleUpdateCommentResponse($success);
    }

    /**
     * Set the document
     */
    protected function setDocument(Request $request)
    {
        try {
            $this->document = Document::findOrFail($request->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => __($this->namespacePrefix() . '.documents.controller.errors.document_not_found')
                ], 404);
            }

            return redirect()->route($this->namespacePrefix() . '.documents.index')
                ->with('alert', __($this->namespacePrefix() . '.documents.controller.errors.document_not_found'));
        }
    }

    /**
     * Get document params
     */
    protected function documentParams(Request $request)
    {
        return $request->validate([
            'document.status' => 'required',
            'document.rejection_reason' => 'nullable'
        ])['document'];
    }

    /**
     * Get namespace prefix
     */
    protected function namespacePrefix()
    {
        return strtolower(class_basename(get_class($this)));
    }
}