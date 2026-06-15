<div class="mb-8">
    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Users Overview</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">


        <!-- Accepted Card -->
        <a href="{{ route('data_entry_operators.index') }}" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-teal-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $data_entry_operators }}</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Data Entry Operator
                        </p>
                    </div>
                    <div class="p-3 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M5 8a4 4 0 1 1 7.796 1.263l-2.533 2.534A4 4 0 0 1 5 8Zm4.06 5H7a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h2.172a2.999 2.999 0 0 1-.114-1.588l.674-3.372a3 3 0 0 1 .82-1.533L9.06 13Zm9.032-5a2.907 2.907 0 0 0-2.056.852L9.967 14.92a1 1 0 0 0-.273.51l-.675 3.373a1 1 0 0 0 1.177 1.177l3.372-.675a1 1 0 0 0 .511-.273l6.07-6.07a2.91 2.91 0 0 0-.944-4.742A2.907 2.907 0 0 0 18.092 8Z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </a>
        <!-- In-progress Card -->
        <a href="{{ route('clients.index') }}" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $clients->count() }}</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Clients</p>
                    </div>
                    <div
                        class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                        {{-- <i class="fas fa-spinner text-xl"></i> --}}
                        <svg class="w-6 h-6 text-gray-800 dark:text-white text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-width="2"
                                d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
