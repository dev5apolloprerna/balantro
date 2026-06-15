<div class="<?php echo e($paginationClass ?? ''); ?>">
    <nav class="isolate inline-flex flex-wrap justify-center sm:justify-end gap-2 md:gap-1 rounded-md" aria-label="Pagination">
        <?php if (! ($paginator->onFirstPage())): ?>
            <?php echo $__env->make('pagination._first_page', ['url' => $paginator->url(1), 'remote' => $remote ?? false], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>

        <?php if (! ($paginator->onFirstPage())): ?>
            <?php echo $__env->make('pagination._prev_page', ['url' => $paginator->previousPageUrl(), 'remote' => $remote ?? false], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>

        <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(is_string($element)): ?>
                <?php echo $__env->make('pagination._gap', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>

            <?php if(is_array($element)): ?>
                <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $paginator->currentPage()): ?>
                        <?php echo $__env->make('pagination._page', ['page' => $page, 'url' => $url, 'remote' => $remote ?? false], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php else: ?>
                        <?php echo $__env->make('pagination._page', ['page' => $page, 'url' => $url, 'remote' => $remote ?? false], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if (! ($paginator->currentPage() == $paginator->lastPage())): ?>
            <?php echo $__env->make('pagination._next_page', ['url' => $paginator->nextPageUrl(), 'remote' => $remote ?? false], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>

        <?php if (! ($paginator->currentPage() == $paginator->lastPage())): ?>
            <?php echo $__env->make('pagination._last_page', ['url' => $paginator->url($paginator->lastPage()), 'remote' => $remote ?? false], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    </nav>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\kaminari\tailwind\_paginator.blade.php ENDPATH**/ ?>