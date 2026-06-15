<tr id="client-<?php echo e($client->id); ?>" class="hover:bg-gray-50 dark:hover:bg-gray-700" data-entry-operator-id="<?php echo e($client->id); ?>">
  <td class="px-4 py-3"><?php echo e($client->name); ?></td>
  <td class="px-4 py-3"><?php echo e($client->email); ?></td>
  <td><?php echo e($client->supervisors->isNotEmpty() ? $client->supervisors->pluck('name')->join(', ') : '-'); ?></td>
  <td><?php echo e($client->data_entry_operators->isNotEmpty() ? $client->data_entry_operators->pluck('name')->join(', ') : '-'); ?></td>
  <td><?php echo e($client->company_id ?: '-'); ?></td>
  <td>
    <div class="flex justify-center space-x-2">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign_users', 'App\Http\Controllers\Managers\ClientsController')): ?>
        <button type="button"
                class="bg-indigo-100 dark:bg-indigo-600/25 hover:bg-indigo-200 text-indigo-600 dark:text-indigo-400 font-medium w-10 h-10 flex justify-center items-center rounded-full cursor-pointer"
                data-client-id="<?php echo e($client->id); ?>"
                data-supervisor-id="<?php echo e($client->supervisors->first()->id ?? null); ?>"
                data-data-entry-operator-id="<?php echo e($client->data_entry_operators->first()->id ?? null); ?>"
                data-action="click->manager--client--allocate-supervisor-deo#setSupervisor"
                title="<?php echo e(__('managers.client.index.table_headers.actions')); ?>">
          <iconify-icon icon="heroicons-outline:user-add" class="text-lg"></iconify-icon>
        </button>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('set_company_id', 'App\Http\Controllers\Managers\ClientsController')): ?>
        <button type="button"
                class="bg-green-100 dark:bg-green-600/25 hover:bg-green-200 text-green-600 dark:text-green-400 font-medium w-10 h-10 flex justify-center items-center rounded-full cursor-pointer"
                data-client-id="<?php echo e($client->id); ?>"
                data-company-id="<?php echo e($client->company_id); ?>"
                data-action="click->manager--client--set-company-id#showModal"
                title="Set Company ID">
          <iconify-icon icon="heroicons-outline:identification" class="text-2xl"></iconify-icon>
        </button>
      <?php endif; ?>
    </div>
  </td>
</tr><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\clients\_client.blade.php ENDPATH**/ ?>