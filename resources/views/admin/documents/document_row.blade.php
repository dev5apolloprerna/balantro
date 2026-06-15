<tr>
    <td>
        <div class="flex items-center">
            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300 mr-4">
                <iconify-icon icon="heroicons-outline:document" class="text-xl"></iconify-icon>
            </div>
            <div>
                <div class="font-medium break-all max-w-xl">
                    {{ $document->file_name }}
                </div>
                <div class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ \Illuminate\Support\Str::fileSize($document->file_size) }}
                </div>
            </div>
        </div>
    </td>
    <td>
        <div>
            {{ $document->created_at->format('d M, Y') }}, {{ $document->created_at->format('h:i A') }}
        </div>
    </td>
    <td>{{ $document->user->name }}</td>
    <td class="relative">
        <div class="inline-flex group">
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ document_status_classes($document->status) }}">
                {{ ucfirst($document->status) }}
                @if($document->status == 'rejected' && $document->rejection_reason)
                    <div class="absolute hidden group-hover:block top-full left-1/2 transform -translate-x-1/2 mt-2 z-[9999] min-w-[200px] max-w-[300px]">
                        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700">
                                <div class="font-medium text-lg text-gray-800 dark:text-white font-bold">@lang('admin.documents.table.reason')</div>
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
    <td>
        @can('download', $document)
            <div class="flex space-x-2">
                <a href="{{ route('admin.documents.download', $document) }}"
                   class="download-item-btn bg-success-600 hover:bg-success-700 text-white font-medium w-10 h-10 flex justify-center items-center rounded-full cursor-pointer">
                    <iconify-icon icon="solar:download-linear" class="menu-icon text-xl"></iconify-icon>
                </a>
            </div>
        @else
            -
        @endcan
    </td>
</tr>