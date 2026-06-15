<div class="container mx-auto" data-controller="manager--data-entry-operator--allocate-supervisor">
  <div class="flex justify-between items-center mb-6">
    <h6 class="font-semibold mb-0 dark:text-white">{{ __('managers.data_entry_operators.index.title') }}</h6>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-12">
    <div class="col-span-12">
      <div class="card !border-0 overflow-hidden">
        <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 p-4 sm:p-6">
          <div class="flex flex-col gap-3">
            <div class="flex flex-col sm:flex-row gap-3 w-full">
              <form method="GET" action="{{ route('managers.data_entry_operators.index') }}" class="w-full">
                <div class="flex flex-col sm:flex-row gap-3 w-full">
                  <div class="relative">
                    <input type="text" name="query" value="{{ request()->query }}"
                           class="bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 pl-10 pr-4 rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full"
                           placeholder="{{ __('search.placeholder') }}">
                    <iconify-icon icon="ion:search-outline" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-neutral-800 dark:text-neutral-100 text-lg"></iconify-icon>
                  </div>

                  <select name="supervisor_id"
                          class="custom-select-arrow bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 !rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full sm:w-[250px] cursor-pointer">
                    <option value="">{{ __('dropdowns.supervisor') }}</option>
                    @foreach($supervisors as $supervisor)
                      <option value="{{ $supervisor->id }}" {{ request()->supervisor_id == $supervisor->id ? 'selected' : '' }}>
                        {{ $supervisor->name }}
                      </option>
                    @endforeach
                  </select>

                  <button type="submit" class="w-full sm:w-auto items-center btn bg-primary-600 hover:bg-primary-700 text-white h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
                    {{ __('search.search') }}
                  </button>

                  @if(request()->query || request()->supervisor_id)
                    <a href="{{ route('managers.data_entry_operators.index') }}" class="w-full sm:w-auto items-center btn border border-danger-600 bg-hover-danger-200 !text-danger-500 h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
                      {{ __('search.reset') }}
                    </a>
                  @endif
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div id="data-entry-operators-table">
            @include('managers.data_entry_operators.data_entry_operator_table', ['data_entry_operators' => $data_entry_operators])
          </div>
          @include('shared.pagination', ['resources' => $data_entry_operators])
        </div>
      </div>
    </div>
  </div>
  <!-- For Render assign supervisor modal -->
  <div id="assignSupervisorModal" data-manager--data-entry-operator--allocate-supervisor-target="assignSupervisorModal" class="hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full overflow-x-hidden overflow-y-auto md:inset-0 h-screen bg-black/50 dark:bg-white/30"
       tabindex="-1" aria-hidden="true"></div>
</div>