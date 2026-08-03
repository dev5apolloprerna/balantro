

<?php $__env->startSection('title', 'Verify Email'); ?>

<?php $__env->startSection('content'); ?>
<section class="bg-white dark:bg-black flex min-h-[calc(100vh-64px)] items-center justify-center px-4">
    <div class="w-full max-w-md rounded-2xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-gray-800 dark:bg-gray-950">
        <div class="text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-cyan-100 text-cyan-600 dark:bg-cyan-950 dark:text-cyan-300">
                <iconify-icon icon="mage:email" class="text-3xl"></iconify-icon>
            </div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Verify your email</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Enter the 6-digit code sent to <strong><?php echo e($email); ?></strong>.</p>
        </div>

        <?php if(session('status')): ?>
            <div class="mt-5 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('registration.otp.verify')); ?>" class="mt-6">
            <?php echo csrf_field(); ?>
            <label for="otp" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Verification code</label>
            <input id="otp" name="otp" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" required autofocus
                class="mt-2 h-14 w-full rounded-lg border border-neutral-300 bg-gray-50 text-center text-2xl tracking-[0.5em] text-gray-900 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <?php $__errorArgs = ['otp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-2 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <button type="submit" class="mt-6 h-12 w-full rounded-lg border border-cyan-400/50 bg-gradient-to-r from-cyan-500 to-cyan-400 font-medium text-white transition hover:opacity-90">Verify and create account</button>
        </form>

        <form method="POST" action="<?php echo e(route('registration.otp.resend')); ?>" class="mt-4 text-center">
            <?php echo csrf_field(); ?>
            <button type="submit" class="text-sm font-medium text-cyan-600 hover:underline dark:text-cyan-300">Resend code</button>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('auth.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/auth/verify-registration-otp.blade.php ENDPATH**/ ?>