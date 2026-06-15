<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h6 class="font-semibold mb-0 dark:text-white">Permissions</h6>
        <a href="<?php echo e(route('admin.permissions.create')); ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            New Permission
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo e($permission->name); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo e($permission->action); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo e($permission->subject); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?php echo e(route('admin.permissions.show', $permission)); ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Show</a>
                            <a href="<?php echo e(route('admin.permissions.edit', $permission)); ?>" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</a>
                            <form action="<?php echo e(route('admin.permissions.destroy', $permission)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\permissions\index.blade.php ENDPATH**/ ?>