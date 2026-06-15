<!-- Mobile Card View -->
<div class="lg:hidden space-y-4">
  @if($client_documents && $client_documents->count())
    @foreach($client_documents as $document)
      @include('documents.mobile_card', ['document' => $document])
    @endforeach
  @else
    <div class="text-center text-neutral-500 dark:text-neutral-400 py-12">
      <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl mb-3"></iconify-icon>
      <p class="text-lg font-medium mb-1">{{ __('client.documents.index.no_documents_title') }}</p>
    </div>
  @endif
</div>

<!-- Desktop Table View -->
<div class="table-responsive hidden lg:block">
  <table class="table bordered-table mb-0">
    <thead>
      <tr>
        <th scope="col">{{ __('client.documents.index.table.document') }}</th>
        <th scope="col">{{ __('client.documents.index.table.upload_date') }}</th>
        <th scope="col">{{ __('client.documents.index.table.status') }}</th>
        <th scope="col" class="flex justify-center">{{ __('client.documents.index.table.actions') }}</th>
      </tr>
    </thead>
    <tbody>
      @if($client_documents && $client_documents->count())
        @foreach($client_documents as $document)
          @include('documents.document_row', ['document' => $document])
        @endforeach
      @else
        <tr>
          <td colspan="8" class="text-center py-12 text-neutral-500 dark:text-neutral-400">
            <div class="flex flex-col items-center justify-center">
              <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl text-neutral-400 mb-3"></iconify-icon>
              <p class="text-lg font-medium mb-1">
                {{ __('admin.documents.table.no_documents_title') }}
              </p>
            </div>
          </td>
        </tr>
      @endif
    </tbody>
  </table>
</div>