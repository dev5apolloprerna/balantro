<?php if (! ($paginator->onFirstPage())): ?>
    <a href="<?php echo e($url); ?>" 
       rel="prev"
       <?php if($remote): ?> data-remote="true" <?php endif; ?>
       class="inline-flex items-center px-3 md:px-4 py-1.5 text-xs md:text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition">
        <?php echo __('views.pagination.previous'); ?>

    </a>
<?php endif; ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\kaminari\tailwind\_prev_page.blade.php ENDPATH**/ ?>