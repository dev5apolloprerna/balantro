<?php $__env->startSection('title', 'Document Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="mt-1 border-b border-gray-200 dark:border-gray-700 pb-1">
    <div class="flex flex-wrap lg:flex-nowrap items-center justify-between gap-4">
        <!-- Left : Client Name -->
        <div class="flex items-center gap-3 shrink-0">
            <div
                class="h-10 w-10 rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 text-white flex items-center justify-center font-bold">
                <?php echo e(strtoupper(substr($user->name ?? '',0,1))); ?>

            </div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                <?php echo e(strtoupper($user->name ?? '')); ?>

            </h1>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-2 flex-1">
            <?php echo $__env->make('admin.clients.reports.tabmanu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <!-- Right : FY + Back -->
        <div class="flex items-center gap-3 shrink-0">
            <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                <?php echo e($labelFY ?? ''); ?>

            </span>
            <a href="<?php echo e(url()->previous()); ?>" title="Go Back"
                class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                hover:border-[#f472b6] hover:shadow-[0_0_15px_#f472b6] hover:scale-105 hover:-translate-y-1">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>
</div>

<div class="dashboard-main-body">
    <div class="mb-4 mt-2">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Documents Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Uploaded Card -->
            <a href="<?php echo e(route('documents.index', ['client_id' => $user->id, 'status' => 'uploaded'])); ?>" type="submit" class="group block w-full text-left focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl card-hover color-0">
                <div class="relative  rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                    <div class="p-4 pl-6">
                        <div class="flex items-start justify-between">
                            <div class="pr-3 flex-1">
                                <div
                                    class="text-[12px] uppercase tracking-wide text-gray-500 dark:text-gray-400 truncate">
                                    Uploaded Documents
                                </div>
                                <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-gray-900 dark:text-white tabular-nums" style="font-size: 1rem !important;">
                                    <?php echo e($uploaded_count); ?>

                                </div>
                            </div>
                            <div class="shrink-0">
                                <i class="fas fa-file-upload text-xl"></i>
                            </div>

                        </div>
                    </div>
                </div>
            </a>
            <!-- Completed Card -->
            <a href="<?php echo e(route('documents.index', ['client_id' => $user->id, 'status' => 'processing'])); ?>" class="block">
               <div class="relative  rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                    <div class="p-4 pl-6">
                        <div class="flex items-start justify-between">
                            <div class="pr-3 flex-1">
                                <div
                                    class="text-[12px] uppercase tracking-wide text-gray-500 dark:text-gray-400 truncate">
                                    Accounting in-progress
                                </div>
                                <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-gray-900 dark:text-white tabular-nums" style="font-size: 1rem !important;">
                                    <?php echo e($in_progress_count); ?>

                                </div>
                            </div>
                            <div class="shrink-0">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <!-- Accepted Card -->
            <a href="<?php echo e(route('documents.index', ['client_id' => $user->id, 'status' => 'approved'])); ?>" class="block">
                <div class="relative  rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                    <div class="p-4 pl-6">
                        <div class="flex items-start justify-between">
                            <div class="pr-3 flex-1">
                                <div
                                    class="text-[12px] uppercase tracking-wide text-gray-500 dark:text-gray-400 truncate">
                                    Accounting complete
                                </div>
                                <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-gray-900 dark:text-white tabular-nums" style="font-size: 1rem !important;">
                                    <?php echo e($completed_count); ?>

                                </div>
                            </div>
                            <div class="shrink-0">
                                <i class="fas fa-file-invoice text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <!-- Rejected Card -->
            <a href="<?php echo e(route('documents.index', ['client_id' => $user->id, 'status' => 'rejected'])); ?>" class="block">
                <div class="relative  rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                    <div class="p-4 pl-6">
                        <div class="flex items-start justify-between">
                            <div class="pr-3 flex-1">
                                <div
                                    class="text-[12px] uppercase tracking-wide text-gray-500 dark:text-gray-400 truncate">
                                    Rejected
                                </div>
                                <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-gray-900 dark:text-white tabular-nums" style="font-size: 1rem !important;">
                                    <?php echo e($rejected_count); ?>

                                </div>
                            </div>
                            <div class="shrink-0">
                                <i class="fas fa-times-circle text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>


    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/clients/reports/documentDashboard.blade.php ENDPATH**/ ?>