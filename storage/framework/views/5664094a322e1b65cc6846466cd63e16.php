<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('admin.data_entry_operators.data_entry_operator_list', [
        'data_entry_operators' => $dataEntryOperators,
        'managers' => $managers,
        'supervisors' => $supervisors,
        'groups' => $groups,
        'permissions' => $permissions,
        'mgrSupMap' => $mgrSupMap,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\data_entry_operators\index.blade.php ENDPATH**/ ?>