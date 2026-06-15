@include('client_dashboard.topmenu')
<div class="container py-3">
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

            <a href="{{ route('documents.index', ['status' => 'accepted']) }}" class="block">
                <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $accepted_count }}</p>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Accepted Documents
                            </p>
                        </div>
                        <div
                            class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>
                </div>
            </a>
            <!-- Completed Card -->
            <a href="{{ route('documents.index', ['status' => 'processing']) }}" class="block">
                <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $in_progress_count }}</p>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Accounting in-progress
                            </p>
                        </div>
                        <div
                            class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Accepted Card -->
            <a href="{{ route('documents.index', ['status' => 'approved']) }}" class="block">
                <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-teal-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $completed_count }}</p>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Accounting complete
                            </p>
                        </div>
                        <div class="p-3 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400">
                            <i class="fas fa-file-invoice text-xl"></i>
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
</div>
