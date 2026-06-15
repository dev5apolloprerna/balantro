<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('admin.supervisors.supervisor_list', [
        'supervisors' => $supervisors, // 👈 use supervisors (not managers)
        'managers' => $managers,
        'groups' => $groups, // 👈 pass groups
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\supervisors\index.blade.php ENDPATH**/ ?>