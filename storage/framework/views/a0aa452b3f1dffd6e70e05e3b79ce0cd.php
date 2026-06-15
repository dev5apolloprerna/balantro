<div class="relative p-4 w-full max-w-2xl max-h-full">
  <div class="relative bg-white dark:bg-dark-2 rounded-2xl shadow-lg">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 dark:border-gray-600 rounded-t">
      <h6 class="font-semibold text-gray-900 dark:text-white"><?php echo e(__('managers.data_entry_operators.assign_modal.title')); ?></h6>
      <button type="button"
              class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white w-8 h-8 inline-flex justify-center items-center rounded-lg cursor-pointer"
              data-action="click->manager--data-entry-operator--allocate-supervisor#hideModal">
        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
        <span class="sr-only"><?php echo e(__('managers.data_entry_operators.assign_modal.close_btn')); ?></span>
      </button>
    </div>
    <!-- Modal Body -->
    <div class="p-4 md:p-5 space-y-4">
      <div class="mb-3">
        <label class="block text-sm font-semibold text-neutral-600 dark:text-neutral-200 mb-2"><?php echo e(__('managers.data_entry_operators.supervisor_label')); ?></label>
        <select name="supervisor"
                data-manager--data-entry-operator--allocate-supervisor-target="chosenSupervisor"
                class="w-full text-base !rounded-lg text-gray-700 dark:text-gray-200 border border-primary-600 px-5 py-4 custom-select-arrow bg-white dark:bg-neutral-800">
          <?php $__currentLoopData = $managerSupervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($supervisor->id); ?>" <?php echo e(in_array($supervisor->id, $selectedSupervisorIds) ? 'selected' : ''); ?>>
              <?php echo e($supervisor->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>
    <!-- Modal Footer -->
    <div class="flex justify-end gap-4 p-4 md:p-5 border-t border-gray-200 dark:border-gray-600 rounded-b">
      <button type="button"
              class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-600 bg-hover-danger-200 !text-danger-600 text-center"
              data-action="click->manager--data-entry-operator--allocate-supervisor#hideModal">
        <?php echo e(__('managers.data_entry_operators.assign_modal.cancel_btn')); ?>

      </button>
      <button type="button"
              class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 bg-primary-600 hover:bg-primary-700 text-white text-center"
              data-action="click->manager--data-entry-operator--allocate-supervisor#allocate">
        <?php echo e(__('managers.data_entry_operators.assign_modal.assign_btn')); ?>

      </button>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\data_entry_operators\_assign_supervisor_modal.blade.php ENDPATH**/ ?>