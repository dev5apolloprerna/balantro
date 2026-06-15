<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white">New Permission</h6>
            <a href="<?php echo e(route('admin.permissions.index')); ?>" class="text-indigo-600 hover:text-indigo-900">Back to Permissions</a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <?php echo $__env->make('admin.permissions.form', ['permission' => $permission], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\permissions\new.blade.php ENDPATH**/ ?>