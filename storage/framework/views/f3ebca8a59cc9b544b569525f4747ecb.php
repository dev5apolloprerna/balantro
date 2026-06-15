<div class="dashboard-main-body">
    
    <?php if(($active_tab ?? 'financial') === 'financial'): ?>
        <?php echo $__env->make('client_dashboard.financial_dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('client_dashboard.document_count', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\client_dashboard\index.blade.php ENDPATH**/ ?>