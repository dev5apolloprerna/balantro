<!-- <html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="light"> -->
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<!-- Remove the session theme class initially to prevent blink -->

<?php echo $__env->make('auth.includes.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<body class="font-sans antialiased bg-white dark:bg-black text-gray-900 dark:text-gray-100">
    <!-- Remove transition classes from body to prevent initial blink -->
    <div class="min-h-screen">
        <main>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
    <?php echo $__env->make('auth.includes.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>

</html>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/auth/layouts/app.blade.php ENDPATH**/ ?>