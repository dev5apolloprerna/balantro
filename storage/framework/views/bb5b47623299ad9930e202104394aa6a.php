<tr>
    <td>
        <div class="flex items-center">
            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300 mr-4">
                <iconify-icon icon="heroicons-outline:document" class="text-xl"></iconify-icon>
            </div>
            <div>
                <div class="font-medium break-all max-w-xl">
                    <?php echo e($document->file_name); ?>

                </div>
                <div class="text-sm text-neutral-500 dark:text-neutral-400">
                    <?php echo e(\Illuminate\Support\Str::fileSize($document->file_size)); ?>

                </div>
            </div>
        </div>
    </td>
    <td>
        <div>
            <?php echo e($document->created_at->format('d M, Y')); ?>, <?php echo e($document->created_at->format('h:i A')); ?>

        </div>
    </td>
    <td><?php echo e($document->user->name); ?></td>
    <td class="relative">
        <div class="inline-flex group">
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e(document_status_classes($document->status)); ?>">
                <?php echo e(ucfirst($document->status)); ?>

                <?php if($document->status == 'rejected' && $document->rejection_reason): ?>
                    <div class="absolute hidden group-hover:block top-full left-1/2 transform -translate-x-1/2 mt-2 z-[9999] min-w-[200px] max-w-[300px]">
                        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700">
                                <div class="font-medium text-lg text-gray-800 dark:text-white font-bold"><?php echo app('translator')->get('admin.documents.table.reason'); ?></div>
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
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('download', $document)): ?>
            <div class="flex space-x-2">
                <a href="<?php echo e(route('admin.documents.download', $document)); ?>"
                   class="download-item-btn bg-success-600 hover:bg-success-700 text-white font-medium w-10 h-10 flex justify-center items-center rounded-full cursor-pointer">
                    <iconify-icon icon="solar:download-linear" class="menu-icon text-xl"></iconify-icon>
                </a>
            </div>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\documents\document_row.blade.php ENDPATH**/ ?>