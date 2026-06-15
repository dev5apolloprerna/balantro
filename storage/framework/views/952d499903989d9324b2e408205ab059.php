<div class="flex items-center gap-2 bg-white dark:bg-neutral-700 rounded-lg p-2 border border-neutral-200 dark:border-neutral-600">
  <?php if(str_starts_with($document->file->mime_type, 'image/')): ?>
    <iconify-icon icon="heroicons:photo" class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
  <?php else: ?>
    <iconify-icon icon="heroicons:document" class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
  <?php endif; ?>
  <div class="flex-1 min-w-0">
    <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate max-w-[120px]"><?php echo e($document->file->file_name); ?></div>
    <div class="text-xs text-neutral-500 dark:text-neutral-400">
      <?php echo e(formatBytes($document->file->size)); ?>

    </div>
  </div>
  <div class="ml-2">
    <a href="<?php echo e(route('file.download', $document->file->id)); ?>" 
        class="w-9 h-9 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
        title="<?php echo e(__('chat.messages.message.download')); ?>">
      <iconify-icon icon="heroicons:arrow-down-tray" class="text-[1.1rem] p-0.5"></iconify-icon>
    </a>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\messages\_document.blade.php ENDPATH**/ ?>