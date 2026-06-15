<tr id="deo_<?php echo e($deo->id); ?>" class="hover:bg-gray-50 dark:hover:bg-gray-700" data-entry-operator-id="<?php echo e($deo->id); ?>">
  <td class="px-4 py-3"><?php echo e($deo->name); ?></td>
  <td class="px-4 py-3"><?php echo e($deo->email); ?></td>
  <td><?php echo e($deo->supervisors->isNotEmpty() ? $deo->supervisors->pluck('name')->join(', ') : '-'); ?></td>
  <td>
    <div class="flex justify-center space-x-2">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign_supervisor', 'App\Http\Controllers\Managers\DataEntryOperatorsController')): ?>
        <button type="button"
                class="bg-indigo-100 dark:bg-indigo-600/25 hover:bg-indigo-200 text-indigo-600 dark:text-indigo-400 font-medium w-10 h-10 flex justify-center items-center rounded-full cursor-pointer"
                data-deo-id="<?php echo e($deo->id); ?>"
                data-action="click->manager--data-entry-operator--allocate-supervisor#setSupervisor"
                title="<?php echo e(__('managers.data_entry_operators.index.table_headers.actions')); ?>">
          <iconify-icon icon="heroicons-outline:user-add" class="text-lg"></iconify-icon>
        </button>
      <?php else: ?>
        -
      <?php endif; ?>
    </div>
  </td>
</tr><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\data_entry_operators\_data_entry_operator.blade.php ENDPATH**/ ?>