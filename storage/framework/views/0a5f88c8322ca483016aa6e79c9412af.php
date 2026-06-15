<tr class="hover:bg-gray-50 dark:hover:bg-gray-700" 
    data-entry-operator-id="<?php echo e($client->id); ?>"
    data-client-mobile="<?php echo e($client->profile->mobile_no ?? ''); ?>"
    data-client-pan="<?php echo e($client->profile->pan_no ?? ''); ?>"
    data-client-gst="<?php echo e($client->profile->gst_no ?? ''); ?>"
    data-client-address="<?php echo e($client->profile->address ?? ''); ?>"
    data-client-business-type="<?php echo e($client->profile->business_type ?? ''); ?>"
    data-entry-operator-id="<?php echo e($client->id); ?>">
  <td><?php echo e($client->name); ?></td>
  <td><?php echo e($client->email); ?></td>
  <td>
    <?php if($client->dataEntryOperators->count() > 0): ?>
      <?php echo e($client->dataEntryOperators->pluck('name')->join(', ')); ?>

    <?php else: ?>
      <span class="text-gray-400">-</span>
    <?php endif; ?>
  </td>
  <td>
    <?php if($client->managers->count() > 0): ?>
      <?php echo e($client->managers->first()->name); ?>

    <?php else: ?>
      <span class="text-gray-400">-</span>
    <?php endif; ?>
  </td>
  <td>
    <div class="flex justify-center space-x-2">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign_deos', App\Http\Controllers\Supervisors\ClientsController::class)): ?>
        <button type="button" class="bg-indigo-100 dark:bg-indigo-600/25 dark:hover:bg-indigo-600/50 hover:bg-indigo-200 text-indigo-600 dark:text-indigo-400 font-medium w-10 h-10 flex justify-center items-center rounded-full cursor-pointer"
                data-client-id="<?php echo e($client->id); ?>"
                data-current-deo-id="<?php echo e($client->dataEntryOperators->first()->id ?? null); ?>"
                data-action="click->supervisors--assign-deo#setClient"
                title="<?php echo e(__('supervisors.clients.table.assign_deo_btn')); ?>">
          <iconify-icon icon="heroicons-outline:user-add" class="text-lg"></iconify-icon>
        </button>
      <?php else: ?>
        -
      <?php endif; ?>
    </div>
    <?php if(false): ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', App\Http\Controllers\Supervisors\ClientsController::class)): ?>
        <button type="button"
                class="remove-item-btn bg-green-100 dark:!bg-success-600/25 hover:bg-green-200 text-green-600 dark:!text-green-500 font-medium w-10 h-10 flex justify-center items-center rounded-full enabled:cursor-pointer disabled:cursor-not-allowed"
                data-action="client-edit#edit">
          <iconify-icon icon="fluent:edit-24-regular" class="menu-icon"></iconify-icon>
        </button>
      <?php endif; ?>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('destroy', App\Http\Controllers\Supervisors\ClientsController::class)): ?>
        <form action="<?php echo e(route('supervisors.clients.destroy', $client)); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <?php echo method_field('DELETE'); ?>
          <button type="submit" class="remove-item-btn bg-danger-100 dark:!bg-danger-600/25 hover:bg-danger-200 text-danger-600 dark:!text-danger-500 font-medium w-10 h-10 flex justify-center items-center rounded-full enabled:cursor-pointer disabled:cursor-not-allowed"
                  data-action="confirm-delete#confirm">
            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
          </button>
        </form>
      <?php endif; ?>
    <?php endif; ?>
  </td>
</tr><?php /**PATH D:\xampp\htdocs\balantro\resources\views\supervisors\clients\client.blade.php ENDPATH**/ ?>