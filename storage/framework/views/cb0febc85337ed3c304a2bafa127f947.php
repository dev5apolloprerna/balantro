<div class="grid">
  <div class="card h-full p-0 border-0 overflow-hidden">
    <div class="card-body p-6">
      <form method="POST" action="<?php echo e(route('documents.store')); ?>" id="document-upload-form" enctype="multipart/form-data" class="flex flex-col gap-4" data-turbo="true" data-action="turbo:submit-start->document-upload#showLoader turbo:submit-end->document-upload#hideLoader">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="document[user_id]" value="<?php echo e(auth()->id()); ?>">
        
        <!-- Drop Zone Area (now handles both drag/drop and click to browse) -->
        <div class="border-2 border-dashed border-neutral-400 rounded-xl p-6 text-center 
                    text-neutral-500 hover:text-neutral-900 
                    dark:text-neutral-400 dark:hover:text-white 
                    cursor-pointer hover:bg-neutral-100 
                    dark:hover:bg-neutral-700 transition"
              data-action="click->document-upload#openFileDialog dragover->document-upload#handleDragOver drop->document-upload#handleDrop dragleave->document-upload#handleDragLeave"
              data-document-upload-target="dropZone">
          <p class="mb-2">
            <iconify-icon icon="mdi:cloud-upload-outline" class="text-4xl mb-2"></iconify-icon><br>
            <?php echo e(__('client.documents.form.drag_and_drop_instruction')); ?>

          </p>
        </div>
        
        <!-- Hidden file input -->
        <input type="file" name="document[files][]" id="file-upload-name" multiple hidden
               accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx, .csv, .txt, .rtf, .heic"
               class="form-control w-auto mt-6 form-control-lg"
               data-action="change->document-upload#handleFileSelect" data-document-upload-target="fileInput">
        
        <!-- Centered Submit Button -->
        <div class="flex justify-end">
          <button type="submit" 
                  class="bg-primary-600 hover:bg-primary-700 
                          dark:bg-primary-500 dark:hover:bg-primary-700 
                          text-white dark:text-white 
                          font-semibold py-2 px-4 rounded whitespace-nowrap 
                          disabled:bg-gray-300 dark:disabled:bg-gray-700 disabled:text-gray-500 dark:disabled:text-gray-400
                          enabled:cursor-pointer
                          disabled:cursor-not-allowed" 
                  id="submit-btn" 
                  disabled
                  data-document-upload-target="submitBtn">
            <?php echo e(__('client.documents.form.upload_button')); ?>

          </button>
        </div>
        
        <!-- File list display -->
        <ul class="mb-0" data-document-upload-target="uploadedList"></ul>
      </form>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\documents\_upload_form.blade.php ENDPATH**/ ?>