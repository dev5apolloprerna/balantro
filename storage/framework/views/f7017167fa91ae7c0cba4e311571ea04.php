<div id="upload-modal"
     data-document-upload-target="modal"
     tabindex="-1"
     class="hidden fixed inset-0 z-50 flex justify-center items-center w-full overflow-x-hidden overflow-y-auto bg-black/50 dark:bg-white/30">
  <div class="relative p-4 w-full max-w-2xl max-h-full">
    <div class="relative bg-white dark:bg-dark-2 rounded-2xl shadow-lg">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-4 md:p-5 border-b border-neutral-200 dark:border-neutral-600 rounded-t">
        <span class="text-xl font-semibold text-gray-900 dark:text-white" data-document-upload-target="modalTitle"></span>
        <button type="button"
                data-action="click->document-upload#closeModal"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
        </button>
      </div>
      <!-- Modal Body -->
      <div class="p-4 md:p-5 overflow-y-auto max-h-[70vh]" data-document-upload-target="modalBody">
        <!-- Create or Edit Form will be loaded Dynamically -->
      </div>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\documents\_upload_modal.blade.php ENDPATH**/ ?>