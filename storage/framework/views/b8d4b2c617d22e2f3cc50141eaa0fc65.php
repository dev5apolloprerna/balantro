<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white"><?php echo e($group->name); ?> Group Details</h6>
            <div class="space-x-3">
                <a href="<?php echo e(route('admin.groups.edit', $group->id)); ?>" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                <a href="<?php echo e(route('admin.groups.index')); ?>" class="text-indigo-600 hover:text-indigo-900">Back to Groups</a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e($group->name); ?></dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Users</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?php echo e($group->users->pluck('email')->implode(', ')); ?>

                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Permissions</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <ul>
                            <?php $__currentLoopData = $group->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <?php echo e($permission->name); ?> (<?php echo e($permission->action); ?> on <?php echo e($permission->subject); ?>)
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\groups\show.blade.php ENDPATH**/ ?>