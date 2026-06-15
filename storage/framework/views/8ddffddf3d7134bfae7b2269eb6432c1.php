<div id="document-verification-modal" 
     tabindex="-1" 
     class="hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full overflow-x-hidden overflow-y-auto md:inset-0 h-screen bg-black/50 dark:bg-white/30" 
     data-document-target="modal">
  <div class="relative p-4 w-full max-w-lg max-h-full">
    <div class="relative bg-white dark:bg-dark-2 rounded-2xl shadow-lg">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 dark:border-gray-600 rounded-t">
        <span class="text-xl font-semibold text-gray-900 dark:text-white"><?php echo e(__('common.document_verification.modal_title')); ?></span>
        <button type="button"
                class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white w-8 h-8 inline-flex justify-center items-center rounded-lg cursor-pointer"
                data-action="click->document#cancelVerification">
          <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
          <span class="sr-only"><?php echo e(__('common.document_verification.close_btn')); ?></span>
        </button>
      </div>
      <!-- Modal Body -->
      <div class="p-4 md:p-5 space-y-4">
        <!-- Verification Form -->
        <form id="verification-form" data-document-target="form" data-action="submit->document#submitVerification">
          <?php echo csrf_field(); ?>
          <div class="mb-4">
            <label for="verification-status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              <?php echo e(__('common.document_verification.status_label')); ?>

            </label>
            <select name="status" id="verification-status"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    data-document-target="statusField">
              <?php $__currentLoopData = $availableStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($status); ?>"><?php echo e($label); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="mt-1 text-sm !text-danger-500 hidden" data-document-target="statusError"></p>
          </div>
          
          <div class="mb-6 hidden" data-document-target="rejectionReasonContainer">
            <label for="rejection-reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              <?php echo e(__('common.document_verification.rejection_reason_label')); ?>

            </label>
            <textarea name="rejection_reason" id="rejection-reason" 
                      placeholder="<?php echo e(__('common.document_verification.rejection_reason_placeholder')); ?>"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                      data-document-target="rejectionReasonField"></textarea>
            <p class="mt-1 text-sm !text-danger-500 hidden" data-document-target="rejectionReasonError"></p>
          </div>
          
          <div class="mb-6 hidden" data-document-target="descriptionContainer">
            <label for="document-comment-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              <?php echo e(__('common.document_verification.description_label')); ?>

            </label>
            <textarea name="document_comment_description" id="document-comment-description"
                      placeholder="<?php echo e(__('common.document_verification.description_placeholder')); ?>"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                      data-document-target="descriptionField"></textarea>
            <p class="mt-1 text-sm !text-danger-500 hidden" data-document-target="descriptionError"></p>
            <input type="hidden" name="comment_type" value="" data-document-target="commentTypeField">
          </div>
          
          <div class="mb-4">
            <p class="text-sm !text-danger-500 hidden" data-document-target="formError"></p>
          </div>

          <!-- Previous Comments Section -->
          <section class="mt-6 hidden" data-document-target="previousCommentsSection">
            <!-- JS will insert content here -->
          </section>
      </div>
      <!-- Modal Footer -->
      <div class="flex items-center justify-end gap-4 p-4 md:p-5 border-t border-gray-200 dark:border-gray-600 rounded-b">
        <button type="button" 
                class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-600 bg-hover-danger-200 !text-danger-600 text-center"
                data-action="click->document#cancelVerification">
          <?php echo e(__('common.document_verification.cancel_button')); ?>

        </button>
        <button type="submit" 
                class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 bg-primary-600 hover:bg-primary-700 text-white text-center"
                data-document-target="submitButton">
          <?php echo e(__('common.document_verification.submit_button')); ?>

        </button>
      </div>
      </form>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\shared\_document_verification_modal.blade.php ENDPATH**/ ?>