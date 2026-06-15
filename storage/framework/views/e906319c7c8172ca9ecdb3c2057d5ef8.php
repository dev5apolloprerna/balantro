<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h6 class="font-semibold mb-0 dark:text-white">User Details</h6>
            <div class="space-x-3">
                <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="text-indigo-600 hover:text-indigo-900">Back to Users</a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e($user->email); ?></dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="mt-1 text-sm text-gray-900"><?php echo e($user->role); ?></dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Groups</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?php echo e($user->groups->pluck('name')->implode(', ')); ?>

                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Direct Permissions</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?php if($user->permissions->count()): ?>
                            <ul>
                                <?php $__currentLoopData = $user->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <?php echo e($permission->name); ?> (<?php echo e($permission->action); ?> on <?php echo e($permission->subject); ?>)
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <span class="text-gray-400">No direct permissions</span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Group Permissions</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?php if($user->groups->count()): ?>
                            <?php $__currentLoopData = $user->groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mb-2">
                                    <span class="font-semibold"><?php echo e($group->name); ?>:</span>
                                    <?php if($group->permissions->count()): ?>
                                        <ul class="ml-4">
                                            <?php $__currentLoopData = $group->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>
                                                    <?php echo e($permission->name); ?> (<?php echo e($permission->action); ?> on <?php echo e($permission->subject); ?>)
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    <?php else: ?>
                                        <span class="text-gray-400">No permissions</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <span class="text-gray-400">No group permissions</span>
                        <?php endif; ?>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\users\show.blade.php ENDPATH**/ ?>