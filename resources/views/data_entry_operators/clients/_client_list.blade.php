<div class="container mx-auto">
  <div class="flex justify-between items-center mb-6">
    <h6 class="font-semibold mb-0 dark:text-white">{{ __("index.title") }}</h6>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-12">
    <div class="col-span-12">
      <div class="card !border-0 rounded-lg overflow-hidden">
        <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 p-4 sm:p-6">
          <div class="flex flex-col gap-3">
            <div class="flex flex-col sm:flex-row gap-3 w-full">
              <form action="{{ route('data_entry_operators.clients.index') }}" method="GET" class="w-full">
                @csrf
                <div class="flex flex-col md:flex-row md:flex-wrap gap-3 w-full">
                  <div class="relative w-full md:w-[calc(50%-0.375rem)] lg:w-auto">
                    <input type="text" name="query" value="{{ request('query') }}"
                           class="bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 pl-10 pr-4 rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full"
                           placeholder="{{ __('search.placeholder') }}">
                    <iconify-icon icon="ion:search-outline" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-neutral-800 dark:text-neutral-100 text-lg"></iconify-icon>
                  </div>

                  <div class="w-full md:w-[calc(50%-0.375rem)] lg:w-[250px]">
                    <select name="supervisor_id" class="custom-select-arrow bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 !rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full cursor-pointer">
                      <option value="">{{ __('dropdowns.supervisor') }}</option>
                      @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" {{ request('supervisor_id') == $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="w-full md:w-[calc(50%-0.375rem)] lg:w-[250px]">
                    <select name="manager_id" class="custom-select-arrow bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 !rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full cursor-pointer">
                      <option value="">{{ __('dropdowns.manager') }}</option>
                      @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" {{ request('manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <button type="submit" class="w-full sm:w-auto items-center btn bg-primary-600 hover:bg-primary-700 text-white h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
                    {{ __('search.search') }}
                  </button>

                  @if(request('query') || request('supervisor_id') || request('manager_id'))
                    <a href="{{ route('data_entry_operators.clients.index') }}" class="w-full sm:w-auto items-center btn border border-danger-600 bg-hover-danger-200 !text-danger-500 h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
                      {{ __('search.reset') }}
                    </a>
                  @endif
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table bordered-table mb-0">
              <thead>
                <tr>
                  <th scope="col">{{ __("table.headers.name") }}</th>
                  <th scope="col">{{ __("table.headers.email") }}</th>
                  <th scope="col">{{ __("table.headers.supervisors") }}</th>
                  <th scope="col">{{ __("table.headers.managers") }}</th>
                </tr>
              </thead>
              <tbody>
                @if($clients && $clients->count())
                  @foreach($clients as $client)
                    @include('data_entry_operators.clients.client_row', ['client' => $client])
                  @endforeach
                @else
                  <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                      <div class="flex flex-col items-center justify-center">
                        <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl text-neutral-400 mb-3"></iconify-icon>
                        <p class="text-lg font-medium mb-1">{{ __("index.no_clients_found.title") }}</p>
                        <p class="text-sm">{{ __("index.no_clients_found.description") }}</p>
                      </div>
                    </td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>
          @if($clients && $clients->count())
            @include('shared.pagination', ['resources' => $clients])
          @endif
        </div>
      </div>
    </div>
  </div>
</div>