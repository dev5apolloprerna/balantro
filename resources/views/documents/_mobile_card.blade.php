<div class="bg-white dark:bg-gray-800 border border-neutral-200 dark:border-gray-700 shadow-sm rounded-xl p-4">
  <!-- Header with icon, filename, size, date -->
  <div class="flex items-center mb-2">
    <!-- Document Icon -->
    <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300 mr-4">
      <iconify-icon icon="heroicons-outline:document" class="text-xl"></iconify-icon>
    </div>
    <div>
      <!-- File Name -->
      <div class="font-medium break-all">
        {{ $document->file->filename }}
      </div>
      <!-- File Size -->
      <div class="text-sm text-neutral-500 dark:text-neutral-400">
        {{ number_to_human_size($document->file->byte_size) }}
      </div>
      <!-- Upload Date -->
      <div class="text-sm text-neutral-500 dark:text-neutral-400">
        {{ $document->created_at->format('d M, Y – h:i A') }}
      </div>
    </div>
  </div>
  <!-- Bottom Row: Status (left), Actions (right) -->
  <div class="flex justify-between items-center">
    <div class="text-sm text-neutral-600 dark:text-neutral-300">
      {{ client_document_status($document) }}
    </div>
    <div class="flex gap-2">
      @can('update', 'App\Http\Controllers\DocumentsController')
        @if($document->rejected?)
          <button type="button"
                  class="bg-green-100 dark:bg-success-600/25 hover:bg-green-200 !text-green-600 dark:!text-green-500 font-medium w-8 h-8 flex justify-center items-center rounded-full"
                  data-action="click->document-upload#openEditModal"
                  data-document-id="{{ $document->id }}"
                  data-document-filename="{{ $document->file->filename }}"
                  title="{{ __('client.documents.index.edit_button') }}">
            <iconify-icon icon="lucide:edit" />
          </button>
        @endif
      @endcan
      
      @can('destroy', 'App\Http\Controllers\DocumentsController')
        <form method="POST" action="{{ route('documents.destroy', ['document' => $document, 'page' => request()->page]) }}" data-turbo="true">
          @csrf
          @method('DELETE')
          <button type="submit"
                  class="remove-item-btn font-medium w-8 h-8 flex justify-center items-center rounded-full transition-all duration-200 {{ $document->status == 'uploaded' ? 'bg-danger-100 dark:bg-danger-600/25 hover:bg-danger-200 !text-danger-600 dark:!text-danger-500 cursor-pointer' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed' }}"
                  data-action="confirm-delete#confirm"
                  @disabled($document->status != 'uploaded')
                  title="{{ __('client.documents.index.delete_button') }}">
            <iconify-icon icon="fluent:delete-24-regular" />
          </button>
        </form>
      @endcan
    </div>
  </div>
</div>