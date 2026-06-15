<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $__env->yieldContent('title', 'Balantro - Manager'); ?></title>
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

  <body class="dark:bg-neutral-800 bg-neutral-100 dark:text-white">
    <?php if(auth()->guard()->check()): ?>
      <?php echo $__env->make('sidebars.manager_sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

      <!-- Main Content -->
      <main class="dashboard-main">
        <!-- Top Navigation -->
        <?php echo $__env->make('navigations.manager_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

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
      </main>
    <?php else: ?>
      <!-- Public Layout -->
      <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
          <h2 class="text-center text-3xl font-extrabold text-gray-900">
            <?php echo $__env->yieldContent('header', 'Welcome to Balantro'); ?>
          </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
          <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <?php echo $__env->yieldContent('content'); ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php echo $__env->make('shared.common', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  </body>
</html><?php /**PATH D:\xampp\htdocs\balantro\resources\views\layouts\manager.blade.php ENDPATH**/ ?>