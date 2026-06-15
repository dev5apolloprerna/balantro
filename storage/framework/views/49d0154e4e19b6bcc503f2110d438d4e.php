<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 text-center">
  <td class="px-4 py-3">
    <?php if($document->file): ?>
      <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300 mr-4">
          <iconify-icon icon="heroicons-outline:document" class="text-xl"></iconify-icon>
        </div>
        <div>
          <div class="font-medium break-all max-w-xl text-neutral-900 dark:text-white">
            <?php echo e($document->file->original_name); ?>

          </div>
          <div class="text-neutral-500 dark:text-neutral-400">
            <?php echo e(formatFileSize($document->file->size)); ?>

          </div>
        </div>
      </div>
    <?php else: ?>
      <span class="text-gray-400"><?php echo e(__('admin.documents.table.no_file_attach')); ?></span>
    <?php endif; ?>
  </td>
  <td>
    <div>
      <?php echo e($document->created_at->format('d M, Y')); ?>, <?php echo e($document->created_at->format('h:i A')); ?>

    </div>
  </td>
  <td class="px-4 py-3"><?php echo e($document->user->name); ?></td>
  <td class="px-4 py-3 relative">
    <div class="inline-flex group">
      <span class="<?php echo e(documentStatusClasses($document->status)); ?> px-2 py-1 rounded-full text-sm font-semibold cursor-default">
        <?php echo e(ucfirst($document->status)); ?>

        <?php if($document->status == 'rejected' && $document->rejection_reason): ?>
          <div class="absolute hidden group-hover:block top-full left-1/2 transform -translate-x-1/2 mt-2 z-[9999] min-w-[200px] max-w-[300px]">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
              <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700">
                <div class="font-medium text-lg text-gray-800 dark:text-white font-bold"><?php echo e(__('admin.documents.table.reason')); ?></div>
              </div>
              <div class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap overflow-hidden text-ellipsis">
                <?php echo e($document->rejection_reason); ?>

              </div>
            </div>
          </div>
        <?php endif; ?>
      </span>
    </div>
  </td>
  <td>
    <div class="flex justify-center space-x-2">
      <?php if(auth()->user()->can('download', App\Http\Controllers\Managers\DocumentsController::class) && $document->file): ?>
        <form action="<?php echo e(route('managers.documents.download', $document)); ?>" method="GET">
          <button type="submit" class="download-item-btn bg-success-600 hover:bg-success-700 text-white font-medium w-10 h-10 flex justify-center items-center rounded-full enabled:cursor-pointer disabled:cursor-not-allowed">
            <iconify-icon icon="solar:download-linear" class="menu-icon text-xl"></iconify-icon>
          </button>
        </form>
      <?php else: ?>
        <?php echo e("-"); ?>

      <?php endif; ?>
    </div>
  </td>
</tr><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\documents\_document.blade.php ENDPATH**/ ?>