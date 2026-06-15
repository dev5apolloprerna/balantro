<div data-controller="document-upload confirm-delete" 
     data-document-upload-upload-title-value="{{ __('client.documents.modal.upload_title') }}"
     data-document-upload-edit-title-value="{{ __('client.documents.modal.edit_title') }}"
     data-document-upload-current-page-value="{{ request()->page ?? 1 }}">
    @include('documents.document_list', ['client_documents' => $clientDocuments])

  <!-- MOVE modals OUTSIDE of turbo_frame -->
  @include('documents.upload_modal')
  @include('shared.confirm_delete_modal', ['resourceName' => 'document'])
</div>