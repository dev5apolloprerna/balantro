<div class="relative p-4 w-full max-w-2xl max-h-full">
  <div class="relative bg-white dark:bg-dark-2 rounded-2xl shadow-lg">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 dark:border-gray-600 rounded-t">
      <h6 class="font-semibold text-gray-900 dark:text-white"><?php echo e(__('managers.clients.assign_modal.title')); ?></h6>
      <button type="button"
              class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white w-8 h-8 inline-flex justify-center items-center rounded-lg cursor-pointer"
              data-action="click->manager--client--allocate-supervisor-deo#hideModal">
        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
        <span class="sr-only"><?php echo e(__('managers.clients.assign_modal.close_btn')); ?></span>
      </button>
    </div>
    <!-- Modal Body -->
    <div class="p-4 md:p-5 space-y-4">
      <div class="mb-3">
        <label class="block text-gray-700 dark:text-gray-300 mb-2"><?php echo e(__('managers.clients.assign_modal.supervisor_label')); ?></label>
        <select name="supervisor" 
                data-manager--client--allocate-supervisor-deo-target="chosenSupervisor"
                data-action="click->manager--client--allocate-supervisor-deo#changeSupervisor"
                class="w-full bg-white dark:bg-neutral-800 border border-gray-300 dark:border-gray-600 text-lg text-gray-900 dark:text-white rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2 px-4 cursor-pointer custom-select-arrow">
          <option value=""><?php echo e(__('Select Supervisor')); ?></option>
          <?php $__currentLoopData = $managerSupervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($supervisor->id); ?>"><?php echo e($supervisor->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="block text-gray-700 dark:text-gray-300 mb-2"><?php echo e(__('managers.clients.assign_modal.data_entry_operator_label')); ?></label>
        <select name="data_entry_operators" 
                disabled
                data-manager--client--allocate-supervisor-deo-target="chosenDeo"
                data-action="click->manager--client--allocate-supervisor-deo#changeDataEntryOperator"
                class="w-full bg-gray-100 dark:bg-neutral-700 border border-gray-300 dark:border-gray-600 text-lg text-gray-500 dark:text-gray-400 rounded-xl py-2 px-4 cursor-not-allowed custom-select-arrow">
          <option value=""><?php echo e(__('Select Data Entry Operator (choose supervisor first)')); ?></option>
        </select>
      </div>
    </div>
    <!-- Modal Footer -->
    <div class="flex justify-end gap-4 p-4 md:p-5 border-t border-gray-200 dark:border-gray-600 rounded-b">
      <button type="button"
              class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-600 bg-hover-danger-200 !text-danger-600 text-center"
              data-action="click->manager--client--allocate-supervisor-deo#hideModal">
        <?php echo e(__('managers.clients.assign_modal.cancel_btn')); ?>

      </button>
      <button type="button"
              class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 !bg-primary-600 hover:bg-primary-700 text-white text-center"
              disabled
              data-action="click->manager--client--allocate-supervisor-deo#allocate"
              data-manager--client--allocate-supervisor-deo-target="assignButton">
        <?php echo e(__('managers.clients.assign_modal.assign_btn')); ?>

      </button>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\clients\_assign_supervisor_deo_modal.blade.php ENDPATH**/ ?>