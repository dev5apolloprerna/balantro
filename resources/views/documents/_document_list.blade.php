<div class="container mx-auto px-4 py-8">
  <div class="flex justify-between items-center mb-6">
    <h6 class="font-semibold mb-0 dark:text-white">
      {{ __('client.documents.index.title') }}
    </h6>
    @can('create', 'App\Http\Controllers\DocumentsController')
      <button type="button"
              data-action="click->document-upload#openModal"
              class="flex items-center gap-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 cursor-pointer">
        <iconify-icon icon="fa6-regular:square-plus" class="text-lg"></iconify-icon>
        <span>{{ __('client.documents.index.add_button') }}</span>
      </button>
    @endcan
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-12">
    <div class="col-span-12">
      <div class="card !border-0">
        @include('documents.filters')
        <div class="card-body">
          <div id="client-table">
            @include('documents.document_table', ['client_documents' => $client_documents])
          </div>
          @if($client_documents && $client_documents->count())
            @include('shared.pagination', ['resources' => $client_documents])
          @endif
        </div>
      </div>
    </div>
  </div>
</div>