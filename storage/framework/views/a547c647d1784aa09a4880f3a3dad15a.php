<a href="<?php echo e($url); ?>" 
   <?php if($remote): ?> data-remote="true" <?php endif; ?>
   <?php if($page === $paginator->currentPage() + 1): ?> rel="next" 
   <?php elseif($page === $paginator->currentPage() - 1): ?> rel="prev" <?php endif; ?>
   class="inline-flex items-center px-3 md:px-4 py-1.5 text-xs md:text-sm font-medium border border-gray-300 dark:border-gray-600 rounded-md <?php echo e($page === $paginator->currentPage() ? 'bg-blue-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700'); ?> transition">
    <?php echo e($page); ?>

</a><?php /**PATH D:\xampp\htdocs\balantro\resources\views\kaminari\tailwind\_page.blade.php ENDPATH**/ ?>