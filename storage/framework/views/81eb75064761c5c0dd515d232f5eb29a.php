<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white"><?php echo app('translator')->get('group.permissions.assign_title', ['group_name' => $group->name]); ?></h6>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="<?php echo e(route('groups.assign_permissions', $group->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="permission_ids[]" value="<?php echo e($permission->id); ?>"
                                <?php if($assignedPermissions->contains($permission->id)): ?> checked <?php endif; ?>
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label class="ml-3 block text-sm font-medium text-gray-700">
                                <?php echo e($permission->name); ?>

                                <span class="text-gray-500 text-xs ml-1">
                                    (<?php echo app('translator')->get('group.permissions.action_on_subject', ['action' => $permission->action, 'subject' => $permission->subject]); ?>)
                                </span>
                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="<?php echo e(route('admin.groups.show', $group->id)); ?>"
                        class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-600 bg-hover-danger-200 !text-danger-600 text-center">
                        <?php echo app('translator')->get('group.permissions.cancel_button'); ?>
                    </a>
                    <button type="submit"
                        class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 bg-primary-600 hover:bg-primary-700 text-white text-center">
                        <?php echo app('translator')->get('group.permissions.save_button'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\groups\assign_permissions.blade.php ENDPATH**/ ?>