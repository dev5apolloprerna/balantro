<div class="space-y-6">
    <!-- 🔍 Filter Form -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <form action="{{ route('clients.index') }}" method="GET"
            class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-[1fr,200px,200px,200px,auto,auto] w-full">

            <!-- Search -->
            <div class="relative">
                <label for="q" class="sr-only">{{ __('Search by name or email...') }}</label>
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

            <!-- Manager -->
            @if ($user->role === \App\Models\User::ROLES['super_admin'] || $user->role === \App\Models\User::ROLES['supervisor'])
                <div class="relative">
                    <select name="manager_id"
                        class="h-11 w-full cursor-pointer rounded-lg border border-neutral-300 bg-white px-4 text-[16px] text-neutral-900 outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100">
                        <option value="">{{ __('Select Manager') }}</option>
                        @foreach ($managers as $manager)
                            <option value="{{ $manager->id }}" @selected(request('manager_id') == $manager->id)>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Supervisor -->
            @if ($user->role === \App\Models\User::ROLES['super_admin'] || $user->role === \App\Models\User::ROLES['manager'])
                <div class="relative">
                    <select name="supervisor_id"
                        class="h-11 w-full cursor-pointer rounded-lg border border-neutral-300 bg-white px-4 text-[16px] text-neutral-900 outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100">
                        <option value="">{{ __('Select Supervisor') }}</option>
                        @foreach ($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}" @selected(request('supervisor_id') == $supervisor->id)>
                                {{ $supervisor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- DEO -->
            <div class="relative">
                <select name="data_entry_operator_id"
                    class="h-11 w-full cursor-pointer rounded-lg border border-neutral-300 bg-white px-4 text-[16px] text-neutral-900 outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100">
                    <option value="">{{ __('Select Data Entry Operator') }}</option>
                    @foreach ($data_entry_operators as $deo)
                        <option value="{{ $deo->id }}" @selected(request('data_entry_operator_id') == $deo->id)>
                            {{ $deo->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div class="relative">
                <select name="status"
                    class="h-11 w-full cursor-pointer rounded-lg border border-neutral-300 bg-white px-4 text-[16px] text-neutral-900 outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100">
                    <option value="">{{ __('Select Status') }}</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>

            <!-- Search / Reset Buttons -->
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 mt-1 sm:mt-0">
                <button type="submit"
                    class="inline-flex flex-1 sm:flex-none items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-[16px] font-semibold text-white transition hover:bg-primary-700 cursor-pointer">
                    {{ __('Search') }}
                </button>

                @if (request()->hasAny(['query', 'manager_id', 'supervisor_id', 'data_entry_operator_id']))
                    <a href="{{ route('clients.index') }}"
                        class="inline-flex flex-1 sm:flex-none items-center justify-center rounded-lg border border-danger-600 bg-danger-50 px-4 py-2 text-[16px] font-semibold text-danger-600 transition hover:bg-danger-100 cursor-pointer dark:bg-danger-600/20 dark:hover:bg-danger-600/30">
                        {{ __('Reset') }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 🧾 Table Section -->
    <div
        class="overflow-hidden rounded-xl border border-neutral-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y text-sm">
                <thead class="bg-neutral-50/80 dark:bg-neutral-800/60">
                    <tr class="text-left font-semibold text-neutral-700 dark:text-neutral-200">
                        <th class="min-w-[160px] px-2 py-1">{{ __('Name') }}</th>
                        <th class="min-w-[180px] px-2 py-1">{{ __('Email') }}</th>
                        <th class="min-w-[180px] px-2 py-1">{{ __('Managers') }}</th>
                        <th class="min-w-[180px] px-2 py-1">{{ __('Supervisors') }}</th>
                        <th class="min-w-[200px] px-2 py-1">{{ __('Data Entry Operators') }}</th>
                        <th class="min-w-[160px] px-2 py-1">{{ __('Groups') }}</th>
                        <th class="min-w-[100px] px-2 py-1">{{ __('Status') }}</th>
                        <th class="min-w-[120px] px-2 py-1 text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if ($clients->count())
                        @foreach ($clients as $client)
                            @include('admin.clients.client_row', ['client' => $client])
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-neutral-500 dark:text-neutral-400">
                                <div class="flex flex-col items-center justify-center">
                                    <x-icon name="heroicons-outline-document-magnifying-glass"
                                        class="mb-3 h-10 w-10 text-neutral-400" />
                                    <!-- <p class="mb-1 text-lg font-medium">
                                        {{ __('admin.clients.table.no_clients_title') }}</p>
                                    <p class="text-sm">{{ __('admin.clients.table.no_clients_description') }}</p> -->
                                    <p class="text-base font-medium">No managers found</p>
                                    <p class="text-sm">Click “New Client” to create one.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>