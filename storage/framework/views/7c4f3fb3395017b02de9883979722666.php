<?php $__env->startSection('field_validation_only', true); ?>

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


        <form method="POST" action="<?php echo e(route('profile.update_password')); ?>" class="space-y-2">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Current Password -->
                <div>
                    <label for="current_password"
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Current Password</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password"
                            placeholder="Enter your current password" autocomplete="current-password"
                            class="w-full px-2 py-1 pe-12 rounded-lg border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php else: ?> border-gray-300 dark:border-gray-600 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            aria-describedby="current_password_error" <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> aria-invalid="true" <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            required>
                        <button type="button" class="password-visibility-toggle absolute inset-y-0 end-1 flex w-10 items-center justify-center text-gray-500 dark:text-gray-300" data-target="current_password" data-name="current password" aria-label="Show current password" aria-pressed="false">
                            <svg class="eye-open h-5 w-5 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="eye-closed hidden h-5 w-5 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.585 10.587a2 2 0 002.828 2.828M9.363 5.365A9.466 9.466 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.486 9.486 0 01-2.264 3.592M6.61 6.611A9.53 9.53 0 002.458 12C3.732 16.057 7.522 19 12 19a9.45 9.45 0 004.057-.908" />
                            </svg>
                        </button>
                    </div>
                    <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p id="current_password_error" class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">New
                        Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Enter new password"
                            required minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}"
                            title="Use at least 8 characters, including a letter, a number, and a symbol."
                            autocomplete="new-password"
                            class="w-full px-2 py-1 pe-12 rounded-lg border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php else: ?> border-gray-300 dark:border-gray-600 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            aria-describedby="password_error password_guidance" <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> aria-invalid="true" <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>>
                        <button type="button" class="password-visibility-toggle absolute inset-y-0 end-1 flex w-10 items-center justify-center text-gray-500 dark:text-gray-300" data-target="password" data-name="new password" aria-label="Show new password" aria-pressed="false">
                            <svg class="eye-open h-5 w-5 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="eye-closed hidden h-5 w-5 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.585 10.587a2 2 0 002.828 2.828M9.363 5.365A9.466 9.466 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.486 9.486 0 01-2.264 3.592M6.61 6.611A9.53 9.53 0 002.458 12C3.732 16.057 7.522 19 12 19a9.45 9.45 0 004.057-.908" />
                            </svg>
                        </button>
                    </div>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p id="password_error" class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p id="password_guidance" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Use at least 8 characters. Mix letters, numbers, and symbols.</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation"
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Confirm New
                        Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="Re-enter new password" autocomplete="new-password" minlength="8"
                            class="w-full px-2 py-1 pe-12 rounded-lg border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php else: ?> border-gray-300 dark:border-gray-600 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            aria-describedby="password_confirmation_error" <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> aria-invalid="true" <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> required>
                        <button type="button" class="password-visibility-toggle absolute inset-y-0 end-1 flex w-10 items-center justify-center text-gray-500 dark:text-gray-300" data-target="password_confirmation" data-name="password confirmation" aria-label="Show password confirmation" aria-pressed="false">
                            <svg class="eye-open h-5 w-5 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="eye-closed hidden h-5 w-5 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.585 10.587a2 2 0 002.828 2.828M9.363 5.365A9.466 9.466 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.486 9.486 0 01-2.264 3.592M6.61 6.611A9.53 9.53 0 002.458 12C3.732 16.057 7.522 19 12 19a9.45 9.45 0 004.057-.908" />
                            </svg>
                        </button>
                    </div>
                    <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p id="password_confirmation_error" class="mt-1 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.password-visibility-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const input = document.getElementById(toggle.dataset.target);
                if (!input) return;

                const showPassword = input.type === 'password';
                input.type = showPassword ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', showPassword ? 'true' : 'false');
                toggle.setAttribute('aria-label', `${showPassword ? 'Hide' : 'Show'} ${toggle.dataset.name}`);
                toggle.querySelector('.eye-open').classList.toggle('hidden', showPassword);
                toggle.querySelector('.eye-closed').classList.toggle('hidden', !showPassword);
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/profiles/change-password.blade.php ENDPATH**/ ?>