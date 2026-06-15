<p><?php echo e(__('Hello :email!', ['email' => $user->email])); ?></p>

<p><?php echo e(__('Your account has been locked due to an excessive number of unsuccessful sign in attempts.')); ?></p>

<p><?php echo e(__('Click the link below to unlock your account:')); ?></p>

<p><a href="<?php echo e(route('unlock', ['token' => $token])); ?>"><?php echo e(__('Unlock my account')); ?></a></p><?php /**PATH D:\xampp\htdocs\balantro\resources\views\users\mailer\unlock-instructions.blade.php ENDPATH**/ ?>