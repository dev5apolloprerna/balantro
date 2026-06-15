<?php $__env->startSection('content'); ?>
    <div class="lg:col-span-3">
        <div class="shadow p-3">
            <div class="mb-3">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Change Password</h2>
                <p class="text-gray-600 dark:text-gray-400">Update your account password below.</p>
            </div>
        
            <!-- Flash messages -->
            <?php if(session('success')): ?>
                <div
                    class="mb-4 p-4 text-green-800 bg-green-100 rounded-lg border border-green-300 dark:bg-green-900 dark:text-green-200">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div
                    class="mb-4 p-4 text-red-800 bg-red-100 rounded-lg border border-red-300 dark:bg-red-900 dark:text-red-200">
                    <ul class="list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('profile.update_password')); ?>" class="space-y-2">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password"
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Current Password</label>
                        <input type="password" name="current_password" id="current_password"
                            placeholder="Enter your current password"
                            class="w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            required>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">New
                            Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter new password"
                            class="w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            required>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Confirm New
                            Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="Re-enter new password"
                            class="w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            required>
                    </div>
                </div>
                <!-- Buttons <?php echo e(url()->previous()); ?> -->
                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    
                    <button type="submit"
                        class="rounded-md border border-gray-700 text-black dark:text-white  px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee]
                                hover:shadow-[0_0_15px_#22d3ee]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">Change
                        Password</button>
                    <a href="<?php echo e(route('home')); ?>"
                        class="rounded-md border border-gray-700 text-black dark:text-white px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">Cancel</a>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\profiles\change-password.blade.php ENDPATH**/ ?>