<div id="assign-deo-modal" data-supervisors--assign-deo-target="assignDeoModal"
     class="hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full overflow-x-hidden overflow-y-auto md:inset-0 h-screen bg-black/50 dark:bg-white/30" tabindex="-1" aria-hidden="true">
  <div class="relative p-4 w-full max-w-2xl max-h-full">
    <div class="relative bg-white dark:bg-dark-2 rounded-2xl shadow-lg">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 dark:border-gray-600 rounded-t">
        <h6 class="font-semibold text-gray-900 dark:text-white"><?php echo e(__('supervisors.clients.modal.title')); ?></h6>
        <button type="button" class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white w-8 h-8 inline-flex justify-center items-center rounded-lg cursor-pointer"
                data-action="click->supervisors--assign-deo#hideModal">
          <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
          <span class="sr-only"><?php echo e(__('supervisors.clients.modal.close_btn')); ?></span>
        </button>
      </div>
      <!-- Modal Body -->
      <div class="p-4 md:p-5 space-y-4">
        <div class="mb-3">
          <label class="block text-sm font-semibold text-neutral-600 dark:text-neutral-200 mb-2"><?php echo e(__('supervisors.clients.modal.deo_lable')); ?></label>
          <select name="deos" 
              data-supervisors--assign-deo-target="assignDeoModalChosenDeo"
              class="w-full bg-white dark:bg-neutral-800 border border-gray-300 dark:border-gray-600 text-lg text-gray-900 dark:text-white rounded-xl focus:ring-blue-500 focus:border-blue-500 py-2 px-4 cursor-pointer custom-select-arrow">
            <option value=""><?php echo e(__('supervisors.clients.modal.select_deo_placeholder')); ?></option>
            <?php $__currentLoopData = auth()->user()->dataEntryOperators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($deo->id); ?>"><?php echo e($deo->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
      <!-- Modal Footer -->
      <div class="flex items-center gap-4 p-4 md:p-5 border-t border-gray-200 dark:border-gray-600 rounded-b justify-end">
        <button type="button" class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-600 bg-hover-danger-200 !text-danger-600 text-center"
                data-action="click->supervisors--assign-deo#hideModal">
          <?php echo e(__('admin.clients.modal.close_btn')); ?>

        </button>
        <button type="button" class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 bg-primary-600 hover:bg-primary-700 text-white text-center"
                data-action="click->supervisors--assign-deo#allocate">
          <?php echo e(__('supervisors.clients.modal.assign_btn')); ?>

        </button>
      </div>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\supervisors\clients\assign_deo_modal.blade.php ENDPATH**/ ?>