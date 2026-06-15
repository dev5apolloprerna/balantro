<div class="table-responsive">
  <table class="table bordered-table mb-0">
    <thead>
      <tr>
        <th scope="col"><?php echo e(__('managers.data_entry_operators.index.table_headers.name')); ?></th>
        <th scope="col"><?php echo e(__('managers.data_entry_operators.index.table_headers.email')); ?></th>
        <th scope="col"><?php echo e(__('managers.data_entry_operators.index.table_headers.supervisor')); ?></th>
        <th scope="col" class="!text-center"><?php echo e(__('managers.data_entry_operators.index.table_headers.actions')); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if($data_entry_operators && $data_entry_operators->count()): ?>
        <?php $__currentLoopData = $data_entry_operators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php echo $__env->make('managers.data_entry_operators.data_entry_operator', ['deo' => $deo], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
            <div class="flex flex-col items-center justify-center">
              <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl text-neutral-400 mb-3" title="<?php echo e(__('managers.data_entry_operators.index.no_operators_found.icon_title')); ?>"></iconify-icon>
              <p class="text-lg font-medium mb-1"><?php echo e(__('no_date_entry_operator')); ?></p>
            </div>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\data_entry_operators\_data_entry_operator_table.blade.php ENDPATH**/ ?>