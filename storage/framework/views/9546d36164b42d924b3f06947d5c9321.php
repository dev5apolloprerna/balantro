

<?php if($data_entry_operators && $data_entry_operators->count()): ?>
    <?php $__currentLoopData = $data_entry_operators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data_entry_operator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('admin.data_entry_operators.data_entry_operator_row', [
            'data_entry_operator' => $data_entry_operator,
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php else: ?>
    <tr>
        <td colspan="6" class="text-center py-12 text-neutral-500 dark:text-neutral-400">
            <div class="flex flex-col items-center justify-center">
                <iconify-icon icon="heroicons-outline:document-magnifying-glass"
                    class="text-4xl text-neutral-400 mb-3"></iconify-icon>
                <!-- <p class="text-lg font-medium mb-1">
                    <?php echo e(__('admin.data_entry_operators.table.no_data_entry_operators_title')); ?></p>
                <p class="text-sm">
                    <?php echo e(__('admin.data_entry_operators.table.no_data_entry_operators_description')); ?></p> -->
                <p class="text-base font-medium">No managers found</p>
                <p class="text-sm">Click “Add Data Entry Operator” to create one.</p>
            </div>
        </td>
    </tr>
<?php endif; ?>

<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\data_entry_operators\data_entry_operator_table.blade.php ENDPATH**/ ?>