<?php $__env->startSection('content'); ?>
    <!-- resources/views/emails/password_otp.blade.php -->
    <p>Hi <?php echo e($name); ?>,</p>
    <p>Your one-time password (OTP) to reset your account password is:</p>
    <h2 style="letter-spacing:2px"><?php echo e($otp); ?></h2>
    <p>This OTP will expire in <?php echo e($expiryMinutes); ?> minutes.</p>
    <p>If you didn’t request this, you can ignore this email.</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mailer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\emails\password_otp.blade.php ENDPATH**/ ?>