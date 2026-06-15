<!-- Mobile Card View -->
<div class="lg:hidden space-y-4">
  <?php if($client_documents && $client_documents->count()): ?>
    <?php $__currentLoopData = $client_documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php echo $__env->make('documents.mobile_card', ['document' => $document], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  <?php else: ?>
    <div class="text-center text-neutral-500 dark:text-neutral-400 py-12">
      <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl mb-3"></iconify-icon>
      <p class="text-lg font-medium mb-1"><?php echo e(__('client.documents.index.no_documents_title')); ?></p>
    </div>
  <?php endif; ?>
</div>

<!-- Desktop Table View -->
<div class="table-responsive hidden lg:block">
  <table class="table bordered-table mb-0">
    <thead>
      <tr>
        <th scope="col"><?php echo e(__('client.documents.index.table.document')); ?></th>
        <th scope="col"><?php echo e(__('client.documents.index.table.upload_date')); ?></th>
        <th scope="col"><?php echo e(__('client.documents.index.table.status')); ?></th>
        <th scope="col" class="flex justify-center"><?php echo e(__('client.documents.index.table.actions')); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if($client_documents && $client_documents->count()): ?>
        <?php $__currentLoopData = $client_documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php echo $__env->make('documents.document_row', ['document' => $document], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php else: ?>
        <tr>
          <td colspan="8" class="text-center py-12 text-neutral-500 dark:text-neutral-400">
            <div class="flex flex-col items-center justify-center">
              <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl text-neutral-400 mb-3"></iconify-icon>
              <p class="text-lg font-medium mb-1">
                <?php echo e(__('admin.documents.table.no_documents_title')); ?>

              </p>
            </div>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\documents\_document_table.blade.php ENDPATH**/ ?>