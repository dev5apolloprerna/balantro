<div class="dashboard-main-body">

    <?php echo $__env->make('shared.breadcrumb', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('supervisor_dashboard.dashboard.document_count', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('supervisor_dashboard.dashboard.user_count', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\supervisor_dashboard\index.blade.php ENDPATH**/ ?>