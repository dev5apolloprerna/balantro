<div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 p-4 sm:p-6">
  <div class="flex flex-col gap-3">
    <div class="flex flex-col sm:flex-row gap-3 w-full">
      <form method="GET" action="{{ route('documents.index') }}" class="w-full" data-turbo-frame="client-documents-list">
        <div class="flex flex-col md:flex-row md:flex-wrap gap-3 w-full">
          <div class="w-full md:w-[calc(50%-0.375rem)] lg:w-[250px]">
            <!-- Status Filter -->
            <select name="status" class="custom-select-arrow bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 !rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full cursor-pointer">
              <option value="">{{ __('dropdowns.status') }}</option>
              @foreach(client_status_filter_options() as $value => $label)
                <option value="{{ $value }}" {{ request()->status == $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <div data-controller="flatpickr" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto items-center justify-between sm:justify-center">
            <div class="relative w-full sm:w-[240px]">
              <input type="text" name="start_date" id="start_date_input"
                     value="{{ request()->start_date }}"
                     data-flatpickr-target="start"
                     class="bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full"
                     placeholder="{{ __('search.start_date') }}">
              <span class="absolute end-0 top-1/2 -translate-y-1/2 me-3 line-height-1 pointer-events-none">
                <iconify-icon icon="solar:calendar-linear" class="icon text-lg mt-[5px]"></iconify-icon>
              </span>
            </div>

            <span class="text-neutral-600 dark:text-neutral-300 text-sm sm:text-base mx-0 sm:mx-2 whitespace-nowrap">
              {{ __('search.to') }}
            </span>

            <div class="relative w-full sm:w-[240px]">
              <input type="text" name="end_date" id="end_date_input"
                     value="{{ request()->end_date }}"
                     data-flatpickr-target="end"
                     class="bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full"
                     placeholder="{{ __('search.end_date') }}">
              <span class="absolute end-0 top-1/2 -translate-y-1/2 me-3 line-height-1 pointer-events-none">
                <iconify-icon icon="solar:calendar-linear" class="icon text-lg mt-[5px]"></iconify-icon>
              </span>
            </div>
          </div>

          <button type="submit" class="w-full sm:w-auto items-center btn bg-primary-600 hover:bg-primary-700 text-white h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
            {{ __('search.search') }}
          </button>

          @if(request()->status || request()->start_date || request()->end_date)
            <a href="{{ route('documents.index') }}" class="w-full sm:w-auto items-center btn border border-danger-600 bg-hover-danger-200 !text-danger-500 h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
              {{ __('search.reset') }}
            </a>
          @endif
        </div>
      </form>
    </div>
  </div>
</div>