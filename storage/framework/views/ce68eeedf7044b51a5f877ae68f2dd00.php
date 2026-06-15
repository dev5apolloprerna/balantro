<!-- meta tags and other links -->
<div class="dashboard-main-body">
    
    <?php echo $__env->make('super_admin_dashboard.dashboard.document_count', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('super_admin_dashboard.dashboard.user_count', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\super_admin_dashboard\index.blade.php ENDPATH**/ ?>