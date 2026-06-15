<div id="client-edit-modal"
     data-client-edit-target="modal"
     class="hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full overflow-x-hidden overflow-y-auto md:inset-0 h-screen bg-black/50 dark:bg-white/30">
  <div class="bg-white dark:bg-dark-2 rounded-2xl max-w-[40rem] w-full shadow-lg mx-4 md:mx-0 max-h-[90vh] md:max-h-full overflow-y-auto">
    <!-- Modal Header -->
    <div class="py-4 px-6 border-b border-neutral-200 dark:border-neutral-600 flex items-center justify-between">
      <h6 class="font-semibold text-gray-900 dark:text-white"><?php echo e(__('common.edit.edit')); ?></h6>
      <button type="button" data-action="client-edit#cancel" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
      </button>
    </div>
    <!-- Modal Body -->
    <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 120px)">
      <form method="POST" action="" data-client-edit-target="form" data-action="submit->client-edit#submit">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="grid grid-cols-1 mb-4">
          <label for="business_type" class="block text-sm font-medium font-semibold text-neutral-600 dark:text-neutral-200 mb-2">
            <?php echo e(__('common.edit.business_type_label')); ?>

          </label>
          <select name="business_type" id="business_type" class="dark:bg-gray-700 dark:text-white cursor-pointer !rounded-lg custom-select-arrow"
                data-client-edit-target="businessTypeField">
            <option value=""><?php echo e(__('common.edit.business_type_prompt')); ?></option>
            <option value="individual"><?php echo e(__('common.edit.business_types.individual')); ?></option>
            <option value="partnership_firm"><?php echo e(__('common.edit.business_types.partnership_firm')); ?></option>
            <option value="llp"><?php echo e(__('common.edit.business_types.llp')); ?></option>
            <option value="pvt_ltd"><?php echo e(__('common.edit.business_types.pvt_ltd')); ?></option>
            <option value="trust"><?php echo e(__('common.edit.business_types.trust')); ?></option>
          </select>
          <p class="mt-1 text-sm !text-red-400 hidden" data-client-edit-target="businessTypeError"></p>
        </div>
        <!-- Rest of the form fields... -->
        <div class="flex justify-end space-x-2">
          <button type="button" data-action="client-edit#cancel"
                class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-600 bg-hover-danger-200 !text-danger-600 text-center">
            <?php echo e(__('common.edit.cancel')); ?>

          </button>
          <button type="submit" class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 bg-primary-600 hover:bg-primary-700 text-white text-center"
                data-client-edit-target="submitButton">
            <?php echo e(__('common.edit.save_button')); ?>

          </button>
        </div>
      </form>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\shared\client_edit_modal.blade.php ENDPATH**/ ?>