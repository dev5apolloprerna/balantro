<div class="container mx-auto px-4">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h6 class="text-lg font-semibold text-gray-800 dark:text-white">
            {{ __('Data Entry Operator') }}
        </h6>


        @if(auth()->user()->can('data_entry_operators.create') || $user->role === \App\Models\User::ROLES['super_admin'])
                <button type="button"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    data-open-add-user>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor">
                        <path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Add Data Entry Operator') }}
                </button>
            @endif
    </div>

    <!-- Filter + Table Section -->
    <div class="space-y-3">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="col-span-12">
                <div class="shadow rounded-2xl overflow-hidden">
                    <div class="">
                        <!-- Filter Form -->
                        <div class="space-y-6">
                            <form action="{{ route('data_entry_operators.index') }}" method="GET"
                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-[1fr,220px,220px,auto,auto] gap-3">

                                <!-- Search Box -->
                                <div class="relative">
                                    <input id="q" type="text" name="query" value="{{ request('query') }}"
                                        placeholder="{{ __('Search by name or email...') }}"
                                        class="block w-full rounded-lg border border-slate-300 bg-white pl-10 pr-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
                                    <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM14 8a6 6 0 11-12 0 6 6 0 0112 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>

                                <!-- Manager Dropdown -->
                                @if ($user->role === \App\Models\User::ROLES['super_admin'] || $user->role === \App\Models\User::ROLES['supervisor'])
                                    <div>
                                        <select id="manager_id" name="manager_id"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                                            <option value="">{{ __('Select Manager') }}</option>
                                            @foreach ($managers as $manager)
                                                <option value="{{ $manager->id }}" @selected(request('manager_id') == $manager->id)>
                                                    {{ $manager->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <!-- Supervisor Dropdown -->
                                @if ($user->role === \App\Models\User::ROLES['manager'])
                                    <div>
                                        <select id="supervisor_id" name="supervisor_id"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                                            <option value="">{{ __('Select Supervisor') }}</option>
                                            @foreach ($supervisors as $supervisor)
                                                <option value="{{ $supervisor->id }}" @selected(request('supervisor_id') == $supervisor->id)>
                                                    {{ $supervisor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <!-- Search & Reset Buttons -->
                                <div class="flex flex-wrap gap-3 sm:col-span-2 md:col-span-3 lg:col-span-auto">
                                    <button type="submit"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        {{ __('Search') }}
                                    </button>

                                    @if (request('query') || request('manager_id') || request('supervisor_id'))
                                        <a href="{{ route('data_entry_operators.index') }}"
                                            class="flex-1 sm:flex-none inline-flex items-center justify-center rounded-lg border border-rose-300 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-100 focus:outline-none focus:ring-2 focus:ring-rose-400">
                                            {{ __('Reset') }}
                                        </a>
                                    @endif
                                </div>
                            </form>

                            <!-- Table -->
                            <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-xl">
                                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
                                    <thead class="bg-neutral-50/80 dark:bg-neutral-800/60">
                                        <tr class="text-left font-semibold text-neutral-700 dark:text-neutral-200">
                                            <th class="px-2 sm:px-2 py-1">Name</th>
                                            <th class="px-2 sm:px-2 py-1">Email</th>
                                            <th class="px-2 sm:px-2 py-1">Managers</th>
                                            <th class="px-2 sm:px-2 py-1">Supervisors</th>
                                            <th class="px-2 sm:px-2 py-1">Groups</th>
                                            @if ($user->role === \App\Models\User::ROLES['super_admin'] || $user->role === \App\Models\User::ROLES['manager'])
                                                <th class="px-2 sm:px-3 py-1 text-right">Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                        @include('admin.data_entry_operators.data_entry_operator_row', [
                                            'data_entry_operators' => $data_entry_operators,
                                        ])
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if ($data_entry_operators && $data_entry_operators->count())
                            <div class="border-t border-slate-200 p-4 dark:border-slate-700">
                                @include('shared.pagination', ['resources' => $data_entry_operators])
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('admin.data_entry_operators._modals', [
        'managers' => $managers,
        'supervisors' => $supervisors,
        'groups' => $groups ?? collect(),
    ])

    <script>
        window.MGR_SUPS = @json($mgrSupMap ?? []);
    </script>
</div>
