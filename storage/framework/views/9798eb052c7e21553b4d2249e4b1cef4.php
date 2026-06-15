<div class="relative p-4 w-full max-w-2xl max-h-full">
  <div class="relative bg-white dark:bg-dark-2 rounded-2xl shadow-lg">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 dark:border-gray-600 rounded-t">
      <h6 class="font-semibold text-gray-900 dark:text-white"><?php echo e(__('managers.clients.index.table_headers.company_id')); ?></h6>
      <button type="button"
              class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white w-8 h-8 inline-flex justify-center items-center rounded-lg cursor-pointer"
              data-action="click->manager--client--set-company-id#hideModal">
        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
      </button>
    </div>
    <!-- Modal Body -->
    <div class="p-4 md:p-5 space-y-4">
      <div class="mb-3">
          <label for="company-id" class="block text-sm font-medium font-semibold text-neutral-600 dark:text-neutral-200 mb-2">
            <?php echo e(__('managers.clients.index.table_headers.company_id')); ?>

          </label>
          <input type="text" id="company-id" name="company_id" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-500 rounded-lg focus:outline-none focus:ring-0 focus:ring-blue-500 focus:border-blue-500 dark:text-white placeholder-gray-500"
                 placeholder="<?php echo e(__('managers.clients.placeholder')); ?>"
                 data-manager--client--set-company-id-target="companyIdInput">
          <p data-manager--client--set-company-id-target="companyIdError"
            class="hidden mt-1 text-sm !text-danger-600 dark:text-danger-500">
          </p>
        </div>
    </div>
    <!-- Modal Footer -->
    <div class="flex justify-end gap-4 p-4 md:p-5 border-t border-gray-200 dark:border-gray-600 rounded-b">
      <button type="button"
              class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-600 bg-hover-danger-200 !text-danger-600 text-center"
              data-action="click->manager--client--set-company-id#hideModal">
        Cancel
      </button>
      <button type="button"
              class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 !bg-primary-600 hover:bg-primary-700 text-white text-center"
              data-action="click->manager--client--set-company-id#saveCompanyId"
              data-manager--client--set-company-id-target="submitButton">
        Save
      </button>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\clients\_set_company_id_modal.blade.php ENDPATH**/ ?>