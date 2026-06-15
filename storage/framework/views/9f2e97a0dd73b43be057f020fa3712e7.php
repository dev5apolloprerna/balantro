<div data-controller="document-upload confirm-delete" 
     data-document-upload-upload-title-value="<?php echo e(__('client.documents.modal.upload_title')); ?>"
     data-document-upload-edit-title-value="<?php echo e(__('client.documents.modal.edit_title')); ?>"
     data-document-upload-current-page-value="<?php echo e(request()->page ?? 1); ?>">
    <?php echo $__env->make('documents.document_list', ['client_documents' => $clientDocuments], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <!-- MOVE modals OUTSIDE of turbo_frame -->
  <?php echo $__env->make('documents.upload_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php echo $__env->make('shared.confirm_delete_modal', ['resourceName' => 'document'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\documents\index.blade.php ENDPATH**/ ?>