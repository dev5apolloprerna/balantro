<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str; // For Str::plural()

trait DocumentsConcern
{
    /**
     * Renders a Turbo Stream response for bulk update.
     *
     * @param array $successIds
     * @param \Illuminate\Database\Eloquent\Collection $documents
     * @return \Illuminate\Http\Response
     */
    protected function renderBulkUpdateResponse(array $successIds, $documents): Response
    {
        $userRole = Auth::user()->role;
        $targetId = "{$userRole}_doc_listing";
        $partialPath = Str::plural($userRole) . '.documents.document'; // e.g., 'clients.documents.document'

        if (!empty($successIds)) {
            Session::flash('notice', __('common.flash.bulk_update.success', ['count' => count($successIds)]));
        } else {
            Session::flash('alert', __('common.flash.bulk_update.invalid_transition'));
        }

        $flashMessagesHtml = view('shared.flash_messages')->render();

        $documentItemsHtml = '';
        foreach ($documents as $document) {
            // Ensure the partial path exists, e.g., resources/views/clients/documents/document.blade.php
            $documentItemsHtml .= view($partialPath, ['document' => $document])->render();
        }

        $turboStreamContent = <<<HTML
<turbo-stream action="update" target="{$targetId}">
  <template>
    {$documentItemsHtml}
  </template>
</turbo-stream>
<turbo-stream action="update" target="flashMessages">
  <template>
    {$flashMessagesHtml}
  </template>
</turbo-stream>
HTML;

        return response($turboStreamContent, 200)
            ->header('Content-Type', 'text/vnd.turbo-stream.html');
    }

    /**
     * Handles the response for updating a comment.
     *
     * @param bool $success
     * @param \App\Models\Document|null $document (assuming a Document model)
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function handleUpdateCommentResponse(bool $success, $document = null): Response|\Illuminate\Http\JsonResponse
    {
        $namespacePrefix = 'client'; // This would need to be dynamically determined in a real app

        if ($success) {
            Session::flash('notice', __("{$namespacePrefix}.documents.controller.verify.success"));
            $flashMessagesHtml = view('shared.flash_messages')->render();

            $userRole = Auth::user()->role;
            $partialPath = Str::plural($userRole) . '.documents.document';
            $documentHtml = view($partialPath, ['document' => $document])->render();

            $turboStreamContent = <<<HTML
<turbo-stream action="replace" target="document_{$document->id}"> <!-- Assuming document has an ID for target -->
  <template>
    {$documentHtml}
  </template>
</turbo-stream>
<turbo-stream action="update" target="flashMessages">
  <template>
    {$flashMessagesHtml}
  </template>
</turbo-stream>
HTML;

            return response($turboStreamContent, 200)
                ->header('Content-Type', 'text/vnd.turbo-stream.html');
        } else {
            // Assuming $document has errors property if it's an Eloquent model
            // You might need to adjust how errors are retrieved based on your Document model's validation
            $errors = $document->errors()->fullMessages()->join(', ') ?? 'Unknown error.';
            return response()->json([
                'status' => 'error',
                'error' => __("{$namespacePrefix}.documents.controller.verify.error", ['errors' => $errors])
            ], 422);
        }
    }

    /**
     * Renders a response for document creation.
     *
     * @param int $successCount
     * @param array $errorMessages
     * @param int $targetPage
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function renderCreateResponse(int $successCount, array $errorMessages, int $targetPage): \Illuminate\Http\RedirectResponse
    {
        // Rails' `redirect_to` inside `format.turbo_stream` typically means a full page redirect.
        // Laravel's standard redirect is used here.
        if ($successCount > 0) {
            return redirect()->route('documents.index', ['page' => $targetPage])
                ->with('success', __('client.documents.flash.create.success', ['count' => $successCount]));
        } else {
            $currentPage = request('page');
            return redirect()->route('documents.index', ['page' => $currentPage])
                ->with('error', __('client.documents.flash.create.error', [
                    'count' => count($errorMessages),
                    'details' => implode('; ', $errorMessages)
                ]));
        }
    }

    /**
     * Renders a success response for document update.
     *
     * @param \App\Models\Document $document (assuming a Document model)
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    protected function renderUpdateSuccess($document): Response|\Illuminate\Http\RedirectResponse
    {
        // Check if it's a Turbo Stream request by checking the Accept header
        if (request()->header('Accept') === 'text/vnd.turbo-stream.html') {
            $flashMessagesHtml = view('shared.flash_messages')->render();
            // Ensure this partial exists, e.g., resources/views/documents/document_row.blade.php
            $documentRowHtml = view('documents.document_row', ['document' => $document])->render();

            $turboStreamContent = <<<HTML
<turbo-stream action="replace" target="document_row_{$document->id}"> <!-- Assuming target ID for document row -->
  <template>
    {$documentRowHtml}
  </template>
</turbo-stream>
<turbo-stream action="update" target="flashMessages">
  <template>
    {$flashMessagesHtml}
  </template>
</turbo-stream>
HTML;

            return response($turboStreamContent, 200)
                ->header('Content-Type', 'text/vnd.turbo-stream.html');
        } else {
            // Fallback for HTML request
            return redirect()->route('documents.index')
                ->with('success', Session::get('notice')); // Retrieve flash message set earlier
        }
    }

    /**
     * Renders an error response.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    protected function renderErrorResponse(): Response|\Illuminate\Http\RedirectResponse
    {
        // Check if it's a Turbo Stream request
        if (request()->header('Accept') === 'text/vnd.turbo-stream.html') {
            $flashMessagesHtml = view('shared.flash_messages')->render();

            $turboStreamContent = <<<HTML
<turbo-stream action="update" target="flashMessages">
  <template>
    {$flashMessagesHtml}
  </template>
</turbo-stream>
HTML;

            return response($turboStreamContent, 200)
                ->header('Content-Type', 'text/vnd.turbo-stream.html');
        } else {
            // Fallback for HTML request
            return redirect()->route('documents.index')
                ->with('error', Session::get('alert')); // Retrieve flash message set earlier
        }
    }
}
