<div class="container mx-auto" data-controller="document" data-document-url-value="{{ route('admin.documents.index') }}">
    <div class="flex justify-between items-center mb-6">
        <h6 class="font-semibold mb-0 dark:text-white">
            @lang('admin.documents.table.title')
        </h6>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-12">
        <div class="col-span-12">
            <div class="card !border-0 rounded-lg overflow-hidden">
                <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 p-4 sm:p-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row gap-3 w-full">
                            <!-- Admin-Documents filter -->
                            <form action="{{ route('admin.documents.index') }}" method="GET" class="w-full" id="document-filter-form">
                                <div class="flex flex-col md:flex-row md:flex-wrap gap-3 w-full">
                                    <div class="w-full md:w-[calc(50%-0.375rem)] lg:w-[250px]">
                                        <select name="client_id" class="custom-select-arrow bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 !rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full cursor-pointer">
                                            <option value="">@lang('dropdowns.client')</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-full md:w-[calc(50%-0.375rem)] lg:w-[250px]">
                                        <select name="status" class="custom-select-arrow bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 !rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full cursor-pointer">
                                            <option value="">@lang('dropdowns.status')</option>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>@lang("admin.documents.statuses.$status")</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div data-controller="flatpickr" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto items-center justify-between sm:justify-center">
                                        <div class="relative w-full sm:w-[240px]">
                                            <input type="text" name="start_date" value="{{ request('start_date') }}" data-flatpickr-target="start"
                                                   class="bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full"
                                                   placeholder="@lang('search.start_date')">
                                            <span class="absolute end-0 top-1/2 -translate-y-1/2 me-3 line-height-1 pointer-events-none">
                                                <iconify-icon icon="solar:calendar-linear" class="icon text-lg mt-[5px]"></iconify-icon>
                                            </span>
                                        </div>

                                        <span class="text-neutral-600 dark:text-neutral-300 text-sm sm:text-base mx-0 sm:mx-2s whitespace-nowrap">
                                            @lang('search.to')
                                        </span>

                                        <div class="relative w-full sm:w-[240px]">
                                            <input type="text" name="end_date" value="{{ request('end_date') }}" data-flatpickr-target="end"
                                                   class="bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full"
                                                   placeholder="@lang('search.end_date')">
                                            <span class="absolute end-0 top-1/2 -translate-y-1/2 me-3 line-height-1 pointer-events-none">
                                                <iconify-icon icon="solar:calendar-linear" class="icon text-lg mt-[5px]"></iconify-icon>
                                            </span>
                                        </div>
                                    </div>

                                    <button type="submit" class="w-full sm:w-auto items-center btn bg-primary-600 hover:bg-primary-700 text-white h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
                                        @lang('search.search')
                                    </button>

                                    @if(request()->has('client_id') || request()->has('status') || request()->has('start_date') || request()->has('end_date'))
                                        <a href="{{ route('admin.documents.index') }}"
                                           class="w-full sm:w-auto items-center btn border border-danger-600 bg-hover-danger-200 !text-danger-500 h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
                                            @lang('search.reset')
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Admin-Documents table -->
                    <div class="card-body">
                        <div>
                            @include('admin.documents.document_table', ['client_documents' => $clientDocuments])
                        </div>
                        @if($clientDocuments->count())
                            {{ $clientDocuments->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>