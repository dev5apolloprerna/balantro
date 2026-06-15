<tr id="document-<?php echo e($document->id); ?>">
  <td>
    <div class="flex items-center">
      <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300 mr-4">
        <iconify-icon icon="heroicons-outline:document" class="text-xl"></iconify-icon>
      </div>
      <div>
        <div class="font-medium break-all max-w-xl">
          <?php echo e($document->file->filename); ?>

        </div>
        <div class="text-sm text-neutral-500 dark:text-neutral-400">
          <?php echo e(number_to_human_size($document->file->byte_size)); ?>

        </div>
      </div>
    </div>
  </td>
  <td>
    <div>
      <?php echo e($document->created_at->format('d M, Y')); ?>, <?php echo e($document->created_at->format('h:i A')); ?>

    </div>
  </td>
  <td>
    <span>
      <?php echo e(client_document_status($document)); ?>

    </span>
  </td>
  <td class="px-4 py-3 text-center">
    <div class="flex items-center justify-center gap-2 flex-wrap sm:flex-nowrap whitespace-nowrap">
      <!-- Edit button - only shown when rejected -->
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', 'App\Http\Controllers\DocumentsController')): ?>
        <?php if($document->rejected?): ?>
          <button type="button"
                  class="bg-green-100 dark:bg-success-600/25 hover:bg-green-200 !text-green-600 dark:!text-green-500 font-medium w-8 h-8 flex justify-center items-center rounded-full cursor-pointer"
                  data-action="click->document-upload#openEditModal"
                  data-document-id="<?php echo e($document->id); ?>"
                  data-document-filename="<?php echo e($document->file->filename); ?>"
                  title="<?php echo e(__('client.documents.index.edit_button')); ?>">
            <iconify-icon icon="lucide:edit"></iconify-icon>
          </button>
        <?php endif; ?>
      <?php endif; ?>
      
      <!-- Delete button - enabled only when status is uploaded -->
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('destroy', 'App\Http\Controllers\DocumentsController')): ?>
        <form method="POST" action="<?php echo e(route('documents.destroy', ['document' => $document, 'page' => request()->page])); ?>" data-turbo="true">
          <?php echo csrf_field(); ?>
          <?php echo method_field('DELETE'); ?>
          <button type="submit"
                  class="remove-item-btn font-medium w-8 h-8 flex justify-center items-center rounded-full transition-all duration-200 <?php echo e($document->status == 'uploaded' ? 'bg-danger-100 dark:bg-danger-600/25 hover:bg-danger-200 !text-danger-600 dark:!text-danger-500 cursor-pointer' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'); ?>"
                  data-action="confirm-delete#confirm"
                  <?php if($document->status != 'uploaded'): echo 'disabled'; endif; ?>
                  title="<?php echo e(__('client.documents.index.delete_button')); ?>">
            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon" />
          </button>
        </form>
      <?php endif; ?>
    </div>
  </td>
</tr><?php /**PATH D:\xampp\htdocs\balantro\resources\views\documents\_document_row.blade.php ENDPATH**/ ?>