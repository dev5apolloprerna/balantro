<div class="mb-8">
    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Users Overview</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Uploaded Card -->
        <a href="<?php echo e(route('managers.index')); ?>" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo e($managers->count()); ?></p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Manager</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                        <i class="fas fa-file-upload text-xl"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Completed Card -->
        <a href="<?php echo e(route('supervisors.index')); ?>" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo e($supervisors->count()); ?></p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Supervisor</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M6 2c-1.10457 0-2 .89543-2 2v4c0 .55228.44772 1 1 1s1-.44772 1-1V4h12v7h-2c-.5523 0-1 .4477-1 1v2h-1c-.5523 0-1 .4477-1 1s.4477 1 1 1h5c.5523 0 1-.4477 1-1V3.85714C20 2.98529 19.3667 2 18.268 2H6Z">
                            </path>
                            <path
                                d="M6 11.5C6 9.567 7.567 8 9.5 8S13 9.567 13 11.5 11.433 15 9.5 15 6 13.433 6 11.5ZM4 20c0-2.2091 1.79086-4 4-4h3c2.2091 0 4 1.7909 4 4 0 1.1046-.8954 2-2 2H6c-1.10457 0-2-.8954-2-2Z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </a>
        <!-- Accepted Card -->
        <a href="<?php echo e(route('data_entry_operators.index')); ?>" class="block">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-teal-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            <?php echo e($data_entry_operators->count()); ?></p>
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
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/super_admin_dashboard/dashboard/user_count.blade.php ENDPATH**/ ?>