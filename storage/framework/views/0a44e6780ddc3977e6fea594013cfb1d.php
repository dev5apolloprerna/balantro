<div class="table-responsive">
    <table class="table bordered-table mb-0">
        <thead>
            <tr>
                <th scope="col"><?php echo app('translator')->get('admin.documents.table.document'); ?></th>
                <th scope="col"><?php echo app('translator')->get('admin.documents.table.upload_date'); ?></th>
                <th scope="col"><?php echo app('translator')->get('admin.documents.table.client'); ?></th>
                <th scope="col"><?php echo app('translator')->get('admin.documents.table.status'); ?></th>
                <th scope="col"><?php echo app('translator')->get('admin.documents.table.actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if($clientDocuments->count()): ?>
                <?php $__currentLoopData = $clientDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('admin.documents.document_row', ['document' => $document], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center py-12 text-neutral-500 dark:text-neutral-400">
                        <div class="flex flex-col items-center justify-center">
                            <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl text-neutral-400 mb-3"></iconify-icon>
                            <p class="text-lg font-medium mb-1">
                                <?php echo app('translator')->get('admin.documents.table.no_documents_title'); ?>
                            </p>
                            <p class="text-sm">
                                <?php echo app('translator')->get('admin.documents.table.no_documents_description'); ?>
                            </p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\documents\document_table.blade.php ENDPATH**/ ?>