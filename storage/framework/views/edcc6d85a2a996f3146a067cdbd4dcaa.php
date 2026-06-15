<div data-is-edit="true">
  <form method="POST" action="<?php echo e(route('documents.update', $document)); ?>" enctype="multipart/form-data" 
        id="document-edit-form" class="flex flex-col gap-4" 
        data-turbo="true" 
        data-action="submit->document-upload#closeModal"
        data-document-upload-target="form">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PATCH'); ?>
    
    <!-- Add this hidden field -->
    <input type="hidden" name="status" value="uploaded">
    
    <!-- Drop Zone Area -->
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
    <input type="file" name="file" id="file-edit-name" hidden 
        accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx, .csv, .txt, .rtf, .heic"
        data-action="change->document-upload#handleFileSelect" data-document-upload-target="fileInput">
    <!-- Current file info -->
    <div class="file-info" data-document-upload-target="uploadedList">
        <li class="uploaded-image-name-list text-primary-600 font-semibold flex items-center gap-2 justify-between mb-2">
        <div class="flex items-center gap-2 flex-1 min-w-0">
            <iconify-icon icon="ph:link-break-light" class="text-xl text-secondary-light"></iconify-icon>
            <span class="truncate block whitespace-nowrap overflow-hidden text-ellipsis" title="<?php echo e($document->file->filename); ?>"><?php echo e($document->file->filename); ?></span>
        </div>
        <span class="text-neutral-500 text-sm ml-2"><?php echo e(number_to_human_size($document->file->byte_size)); ?></span>
        </li>
    </div>
    <!-- Submit Button -->
    <div class="flex justify-end">
        <button type="submit" 
            class="bg-blue-600 hover:bg-blue-700 
                    dark:bg-blue-500 dark:hover:bg-blue-700 
                    text-white dark:text-white 
                    font-semibold py-2 px-4 rounded whitespace-nowrap 
                    disabled:bg-gray-300 dark:disabled:bg-gray-700 disabled:text-gray-500 dark:disabled:text-gray-400
                    enabled:cursor-pointer
                    disabled:cursor-not-allowed" 
            id="submit-btn" 
            data-document-upload-target="submitBtn"
            disabled>
            <?php echo e(__('client.documents.form.upload_button')); ?>

        </button>
    </div>
  </form>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\documents\_edit_form.blade.php ENDPATH**/ ?>