<?php if(Route::currentRouteName() != 'login'): ?>
  <a href="<?php echo e(route('login')); ?>">Log in</a><br />
<?php endif; ?>

<?php if(Route::has('register') && Route::currentRouteName() != 'register'): ?>
  <a href="<?php echo e(route('register')); ?>">Sign up</a><br />
<?php endif; ?>

<?php if(Route::has('password.request') && 
     Route::currentRouteName() != 'password.request' && 
     Route::currentRouteName() != 'register'): ?>
  <a href="<?php echo e(route('password.request')); ?>">Forgot your password?</a><br />
<?php endif; ?>

<?php if(Route::has('verification.notice') && 
     Route::currentRouteName() != 'verification.notice'): ?>
  <a href="<?php echo e(route('verification.notice')); ?>">Didn't receive verification instructions?</a><br />
<?php endif; ?>

<?php if(Route::has('password.confirm') && 
     Route::currentRouteName() != 'password.confirm'): ?>
  <a href="<?php echo e(route('password.confirm')); ?>">Didn't receive unlock instructions?</a><br />
<?php endif; ?>

<?php $__currentLoopData = config('auth.oauth_providers', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <a href="<?php echo e(route('oauth.login', ['provider' => $provider])); ?>" 
     class="btn btn-<?php echo e($provider); ?>">
    Sign in with <?php echo e(ucfirst($provider)); ?>

  </a><br />
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\users\shared\_links.blade.php ENDPATH**/ ?>