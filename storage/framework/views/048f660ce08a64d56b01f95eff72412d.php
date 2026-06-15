<?php $__env->startSection('content'); ?>
    <!-- Main Content -->

    <!-- Header -->


    <!-- Page Content -->
    <!-- <div class="mb-8">
        <h1 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Welcome
            <?php echo e(auth()->user()->name); ?>!</h1>
    </div> -->

    <!-- Documents Section -->
    <?php if(auth()->user()->role == \App\Models\User::ROLES['super_admin']): ?>
        <?php echo $__env->make('super_admin_dashboard.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif(auth()->user()->role == \App\Models\User::ROLES['supervisor']): ?>
        <?php echo $__env->make('supervisor_dashboard.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif(auth()->user()->role == \App\Models\User::ROLES['manager']): ?>
        <?php echo $__env->make('manager_dashboard.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif(auth()->user()->role == \App\Models\User::ROLES['data_entry_operator']): ?>
        <?php echo $__env->make('data_entry_operator_dashboard.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif(auth()->user()->role == \App\Models\User::ROLES['client']): ?>
        <?php echo $__env->make('client_dashboard.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\home.blade.php ENDPATH**/ ?>