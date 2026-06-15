<tr id="document-{{ $document->id }}">
  <td>
    <div class="flex items-center">
      <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300 mr-4">
        <iconify-icon icon="heroicons-outline:document" class="text-xl"></iconify-icon>
      </div>
      <div>
        <div class="font-medium break-all max-w-xl">
          {{ $document->file->filename }}
        </div>
        <div class="text-sm text-neutral-500 dark:text-neutral-400">
          {{ number_to_human_size($document->file->byte_size) }}
        </div>
      </div>
    </div>
  </td>
  <td>
    <div>
      {{ $document->created_at->format('d M, Y') }}, {{ $document->created_at->format('h:i A') }}
    </div>
  </td>
  <td>
    <span>
      {{ client_document_status($document) }}
    </span>
  </td>
  <td class="px-4 py-3 text-center">
    <div class="flex items-center justify-center gap-2 flex-wrap sm:flex-nowrap whitespace-nowrap">
      <!-- Edit button - only shown when rejected -->
      @can('update', 'App\Http\Controllers\DocumentsController')
        @if($document->rejected?)
          <button type="button"
                  class="bg-green-100 dark:bg-success-600/25 hover:bg-green-200 !text-green-600 dark:!text-green-500 font-medium w-8 h-8 flex justify-center items-center rounded-full cursor-pointer"
                  data-action="click->document-upload#openEditModal"
                  data-document-id="{{ $document->id }}"
                  data-document-filename="{{ $document->file->filename }}"
                  title="{{ __('client.documents.index.edit_button') }}">
            <iconify-icon icon="lucide:edit"></iconify-icon>
          </button>
        @endif
      @endcan
      
      <!-- Delete button - enabled only when status is uploaded -->
      @can('destroy', 'App\Http\Controllers\DocumentsController')
        <form method="POST" action="{{ route('documents.destroy', ['document' => $document, 'page' => request()->page]) }}" data-turbo="true">
          @csrf
          @method('DELETE')
          <button type="submit"
                  class="remove-item-btn font-medium w-8 h-8 flex justify-center items-center rounded-full transition-all duration-200 {{ $document->status == 'uploaded' ? 'bg-danger-100 dark:bg-danger-600/25 hover:bg-danger-200 !text-danger-600 dark:!text-danger-500 cursor-pointer' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed' }}"
                  data-action="confirm-delete#confirm"
                  @disabled($document->status != 'uploaded')
                  title="{{ __('client.documents.index.delete_button') }}">
            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon" />
          </button>
        </form>
      @endcan
    </div>
  </td>
</tr>