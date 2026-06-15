<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white">Assign Groups to <?php echo e($user->email); ?></h6>
            <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="text-indigo-600 hover:text-indigo-900">Back to User</a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="<?php echo e(route('admin.users.assign_groups', $user)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="group_ids[]" value="<?php echo e($group->id); ?>" 
                                   <?php if($assignedGroups->contains($group->id)): ?> checked <?php endif; ?>
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label class="ml-3 block text-sm font-medium text-gray-700">
                                <?php echo e($group->name); ?>

                                <span class="text-gray-500 text-xs ml-1">
                                    (<?php echo e(trans_choice(':count permission|:count permissions', $group->permissions_count)); ?>)
                                </span>
                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Groups
                    </button>
                </div>
            </form>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\users\assign_groups.blade.php ENDPATH**/ ?>