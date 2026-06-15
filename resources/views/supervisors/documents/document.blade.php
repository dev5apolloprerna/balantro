<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 text-center" id="document-{{ $document->id }}">
  @can('bulk_update', App\Http\Controllers\Supervisors\DocumentsController::class)
    <td class="p-0 w-[40px]">
      <div class="flex items-center pl-2">
        <input
          type="checkbox"
          class="document-checkbox form-check-input rounded border bg-white dark:bg-neutral-600 m-0"
          value="{{ $document->id }}"
          data-document-bulk-update-target="checkbox">
      </div>
    </td>
  @endcan
  <td class="px-4 py-3">
    @if($document->file)
      <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300 mr-4">
          <iconify-icon icon="heroicons-outline:document" class="text-xl"></iconify-icon>
        </div>
        <div>
          <div class="font-medium break-all max-w-xl text-neutral-900 dark:text-white">
            {{ $document->file->filename }}
          </div>
          <div class="text-sm text-neutral-500 dark:text-neutral-400">
            {{ formatBytes($document->file->size) }}
          </div>
        </div>
      </div>
    @else
      <span class="text-gray-400">{{ __("admin.documents.table.no_file_attach") }}</span>
    @endif
  </td>
  <td>
    <div>
      {{ $document->created_at->format('d M, Y') }}, {{ $document->created_at->format('h:i A') }}
    </div>
  </td>
  <td class="px-4 py-3">{{ $document->user->name }}</td>
  <td class="px-4 py-3 relative">
    <div class="inline-flex group">
      <span class="{{ document_status_classes($document->status) }} px-2 py-1 rounded-full text-sm font-semibold cursor-default">
        {{ ucfirst($document->status) }}
        @if($document->status == 'rejected' && $document->rejection_reason)
          <div class="absolute hidden group-hover:block top-full left-1/2 transform -translate-x-1/2 mt-2 z-[9999] min-w-[200px] max-w-[300px]">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
              <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700">
                <div class="font-medium text-lg text-gray-800 dark:text-white font-bold">{{ __("admin.documents.table.reason") }}</div>
              </div>
              <div class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap overflow-hidden text-ellipsis">
                {{ $document->rejection_reason }}
              </div>
            </div>
          </div>
        @endif
      </span>
    </div>
  </td>
  <td class="px-4 py-3">
    <div class="flex items-center justify-center gap-2 flex-wrap sm:flex-nowrap whitespace-nowrap">
      @if($document->file)
        @can('doc_activities', App\Http\Controllers\Supervisors\DocumentsController::class)
          <a href="{{ route('supervisors.documents.activities', $document) }}"
             title="{{ __("admin.documents.table.activities_btn_title") }}"
             class="bg-pink-200 dark:bg-pink-400/25 hover:bg-pink-300 dark:hover:bg-pink-400/40 text-pink-700 dark:!text-pink-300 font-medium w-8 h-8 flex justify-center items-center rounded-full cursor-pointer">
            <iconify-icon icon="heroicons-outline:list-bullet" class="menu-icon"></iconify-icon>
          </a>
        @endcan
        @can('download', App\Http\Controllers\Supervisors\DocumentsController::class)
          <a href="{{ route('supervisors.documents.download', $document) }}"
             class="download-item-btn bg-success-600 hover:bg-success-700 text-white font-medium w-10 h-10 flex justify-center items-center rounded-full">
            <iconify-icon icon="solar:download-linear" class="menu-icon text-xl"></iconify-icon>
          </a>
        @endcan
        @can('verify', App\Http\Controllers\Supervisors\DocumentsController::class)
          <button
            class="verify-item-btn bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 font-sm px-4 py-2 rounded-full transition-colors whitespace-nowrap min-w-[8.5rem] text-center"
            data-action="click->document#verify"
            data-document-id="{{ $document->id }}"
            data-document-status="{{ $document->status }}" 
            data-document-rejection-reason="{{ $document->rejection_reason }}"
            data-document-comments="{{ $document->documentComments->map(function($c) { 
                return [
                  'type' => $c->comment_type,
                  'description' => $c->description,
                  'created_at' => $c->created_at->format('d M, Y, h:i A')
                ];
              })->toJson() }}">
            {{ __("admin.documents.table.verify_document") }}
          </button>
        @endcan
      @endif
    </div>
  </td>
</tr>