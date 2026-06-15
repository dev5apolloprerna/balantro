<div class="mb-8">
    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Documents Overview</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Uploaded Card -->
        <a href="{{ route('documents.index', ['status' => 'uploaded']) }}" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $uploaded_count }}</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Uploaded Documents</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                        <i class="fas fa-file-upload text-xl"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Completed Card -->
        <a href="{{ route('documents.index', ['status' => 'approved']) }}" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $completed_count }}</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Accounting Complete</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Accepted Card -->
        <a href="{{ route('documents.index', ['status' => 'accepted']) }}" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-teal-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $accepted_count }}</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Accounting Accepted
                        </p>
                    </div>
                    <div class="p-3 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400">
                        <i class="fas fa-file-invoice text-xl"></i>
                    </div>
                </div>
            </div>
        </a>
        <!-- In-progress Card -->
        <a href="{{ route('documents.index', ['status' => 'data_entry_in_progress']) }}" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $data_entry_in_progress_count }}
                        </p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">In Progress</p>
                    </div>
                    <div
                        class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                        <i class="fas fa-spinner text-xl"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- Second Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
        <!-- Data Entered Card -->
        <a href="{{ route('documents.index', ['status' => 'data_entry_completed']) }}" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $data_entered_count }}</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Data Entered</p>
                    </div>
                    <div
                        class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                        <i class="fas fa-keyboard text-xl"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Query Raised Card -->
        <a href="{{ route('documents.index', ['status' => 'query_raised']) }}" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $query_raised_count }}</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Query Raised</p>
                    </div>
                    <div
                        class="p-3 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400">
                        <i class="fas fa-question-circle text-xl"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Query Resolved Card -->
        <a href="{{ route('documents.index', ['status' => 'query_resolved']) }}" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $query_resolved_count }}</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Query Resolved</p>
                    </div>
                    <div
                        class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                        <i class="fas fa-comments text-xl"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Rejected Card -->
        <a href="{{ route('documents.index', ['status' => 'rejected']) }}" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $rejected_count }}</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Rejected</p>
                    </div>
                    <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                        <i class="fas fa-times-circle text-xl"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
