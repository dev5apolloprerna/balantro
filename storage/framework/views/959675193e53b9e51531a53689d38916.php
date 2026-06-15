<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $__env->yieldContent('title', 'Balantro'); ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <?php echo csrf_field(); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <?php echo $__env->yieldContent('head'); ?>

    <link rel="icon" href="/icon.png" type="image/png">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/icon.png">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
  </head>

  <body class="bg-gray-50">
    <?php if(auth()->guard()->check()): ?>
      <div class="min-h-screen flex">
        <!-- Main Content -->
        <div class="flex-1">
          <!-- Top Navigation -->
          <div class="bg-white shadow">
            <div class="px-4 py-3 flex justify-between items-center">
              <h2 class="text-xl font-semibold text-gray-800">
                <?php echo $__env->yieldContent('header', 'Dashboard'); ?>
              </h2>
              <div class="flex items-center space-x-4">
                <span class="text-gray-600"><?php echo e(auth()->user()->email); ?></span>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="text-gray-600 hover:text-gray-900">Sign out</button>
                </form>
              </div>
            </div>
          </div>

          <!-- Page Content -->
          <div class="dashboard-main-body">
            <div id="flashMessages">
              <?php if(session('notice')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                  <?php echo e(session('notice')); ?>

                </div>
              <?php endif; ?>
              <?php if(session('alert')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                  <?php echo e(session('alert')); ?>

                </div>
              <?php endif; ?>
            </div>
            <?php echo $__env->yieldContent('content'); ?>
          </div>
        </div>
      </div>
    <?php else: ?>
      <?php echo $__env->make('navigations.public_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <?php echo $__env->make('shared.flash_messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <?php echo $__env->yieldContent('content'); ?>
    <?php endif; ?>
    <?php echo $__env->make('shared.common', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  </body>
</html><?php /**PATH D:\xampp\htdocs\balantro\resources\views\layouts\application.blade.php ENDPATH**/ ?>