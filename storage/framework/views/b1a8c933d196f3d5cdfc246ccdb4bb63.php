<div class="mb-8">
    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Users Overview</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- In-progress Card -->
        <a href="<?php echo e(route('clients.index')); ?>" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo e($clients->count()); ?></p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Clients</p>
                    </div>
                    <div
                        class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                        
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
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/data_entry_operator_dashboard/dashboard/user_count.blade.php ENDPATH**/ ?>