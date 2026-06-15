<div id="groups-modal"
     data-groups-modal-target="modal"
     class="hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full overflow-x-hidden overflow-y-auto md:inset-0 h-screen bg-black/50 dark:bg-white/30"
     tabindex="-1"
     aria-hidden="true">
  <div class="relative p-4 w-full max-w-4xl max-h-full">
    <div class="relative bg-white dark:bg-dark-2 rounded-2xl shadow-lg">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 dark:border-gray-600 rounded-t">
        <h6 class="font-semibold text-gray-900 dark:text-white"><?php echo e(__('admin.assign_groups.modal.title')); ?></h6>
        <button type="button"
                class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white w-8 h-8 inline-flex justify-center items-center rounded-lg cursor-pointer"
                data-action="groups-modal#close">
          <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
          <span class="sr-only"><?php echo e(__('admin.assign_groups.modal.close_icon')); ?></span>
        </button>
      </div>
      <!-- Modal Body -->
      <form method="POST" action="#" 
            class="p-4 md:p-5 space-y-4"
            data-action="submit->groups-modal#save"
            data-groups-modal-target="form">
        <?php echo csrf_field(); ?>
        <div data-groups-modal-target="content">
          <!-- Group checkboxes will be dynamically inserted here -->
        </div>
        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-4 pt-4 mt-4 border-t border-gray-200 dark:border-gray-600 rounded-b">
          <button type="button"
                  class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-600 bg-hover-danger-200 !text-danger-600 text-center"
                  data-action="click->groups-modal#close">
            <?php echo e(__('admin.assign_groups.modal.close_btn')); ?>

          </button>
          <button type="submit"
                  class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 bg-primary-600 hover:bg-primary-700 text-white text-center">
            <?php echo e(__('admin.assign_groups.modal.save_btn')); ?>

          </button>
        </div>
      </form>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\shared\groups_modal.blade.php ENDPATH**/ ?>