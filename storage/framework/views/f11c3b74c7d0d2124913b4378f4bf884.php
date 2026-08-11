<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('admin.managers.manager_list', ['managers' => $managers], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/managers/index.blade.php ENDPATH**/ ?>