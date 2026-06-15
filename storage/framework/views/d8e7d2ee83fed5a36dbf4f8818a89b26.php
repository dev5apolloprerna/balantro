<div id="confirm-delete-modal"
     data-confirm-delete-target="modal"
     tabindex="-1"
     aria-hidden="true"
     class="hidden fixed inset-0 z-50 flex justify-center items-center w-full overflow-x-hidden overflow-y-auto bg-black/50 dark:bg-white/30">
  <div class="relative p-4 w-full max-w-2xl max-h-full">
    <div class="relative bg-white dark:bg-dark-2 rounded-2xl shadow-lg">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 dark:border-gray-600 rounded-t">
        <span class="text-xl font-semibold text-gray-900 dark:text-white">
          <?php echo e(__('common.confirm_delete.modal_title')); ?>

        </span>
        <button type="button"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer"
                data-action="confirm-delete#cancel">
          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
          <span class="sr-only">Close modal</span>
        </button>
      </div>
      <!-- Modal Body -->
      <div class="p-4 md:p-5 space-y-4">
        <p class="text-neutral-600 dark:text-neutral-300">
          <?php echo e(__('common.confirm_delete.confirm_message', ['resource_name' => ucfirst($resource_name)])); ?>

        </p>
      </div>
      <!-- Modal Footer -->
      <div class="flex items-center justify-end gap-4 p-4 md:p-5 border-t border-gray-200 dark:border-gray-600 rounded-b">
        <button type="button"
                data-action="confirm-delete#cancel"
                class="bg-gray-500 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded cursor-pointer">
          <?php echo e(__('common.confirm_delete.cancel_button')); ?>

        </button>
        <button type="button"
                data-action="confirm-delete#proceed"
                class="bg-red-500 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded cursor-pointer">
          <?php echo e(__('common.confirm_delete.confirm_button')); ?>

        </button>
      </div>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\shared\confirm_delete_modal.blade.php ENDPATH**/ ?>