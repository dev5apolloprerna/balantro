<div class="table-responsive">
    <table class="table bordered-table mb-0">
        <thead>
            <tr>
                <th scope="col">@lang('admin.documents.table.document')</th>
                <th scope="col">@lang('admin.documents.table.upload_date')</th>
                <th scope="col">@lang('admin.documents.table.client')</th>
                <th scope="col">@lang('admin.documents.table.status')</th>
                <th scope="col">@lang('admin.documents.table.actions')</th>
            </tr>
        </thead>
        <tbody>
            @if($clientDocuments->count())
                @foreach($clientDocuments as $document)
                    @include('admin.documents.document_row', ['document' => $document])
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center py-12 text-neutral-500 dark:text-neutral-400">
                        <div class="flex flex-col items-center justify-center">
                            <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl text-neutral-400 mb-3"></iconify-icon>
                            <p class="text-lg font-medium mb-1">
                                @lang('admin.documents.table.no_documents_title')
                            </p>
                            <p class="text-sm">
                                @lang('admin.documents.table.no_documents_description')
                            </p>
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>