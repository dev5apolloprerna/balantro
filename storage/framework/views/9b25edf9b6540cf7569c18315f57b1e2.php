<tr class="[&>td]:px-4 [&>td]:py-3" id="<?php echo e('document-' . $document->id); ?>">
  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bulk_update', 'DataEntryOperators\\DocumentsController')): ?>
    <td class="p-0 w-[40px]">
      <div class="flex items-center pl-2">
        <input
          type="checkbox"
          class="document-checkbox form-check-input rounded border bg-white dark:bg-neutral-600 m-0"
          value="<?php echo e($document->id); ?>"
          data-document-bulk-update-target="checkbox">
      </div>
    </td>
  <?php endif; ?>
  <td>
    <div class="flex items-center">
      <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300 mr-4">
        <iconify-icon icon="heroicons-outline:document" class="text-xl"></iconify-icon>
      </div>
      <div>
        <div class="font-medium break-all max-w-xl text-neutral-900 dark:text-white">
          <?php echo e($document->file->filename); ?>

        </div>
        <div class="text-neutral-500 dark:text-neutral-400">
          <?php echo e(Illuminate\Support\Str::fileSize($document->file->size)); ?>

        </div>
      </div>
    </div>
  </td>
  <td>
    <div>
      <?php echo e($document->created_at->format('d M, Y')); ?>, <?php echo e($document->created_at->format('h:i A')); ?>

    </div>
  </td>
  <td><?php echo e($document->user->name); ?></td>
  <td class="text-center relative">
    <div class="inline-flex group">
      <span class="<?php echo e(document_status_classes($document->status)); ?> px-2 py-1 rounded-full font-medium text-sm font-semibold cursor-default">
        <?php echo e(ucfirst($document->status)); ?>

        <?php if($document->status == 'rejected' && $document->rejection_reason): ?>
          <div class="absolute hidden group-hover:block top-full left-1/2 transform -translate-x-1/2 mt-2 z-[9999] min-w-[200px] max-w-[300px] text-left">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
              <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left">
                <div class="font-medium text-lg text-gray-800 dark:text-white font-bold"><?php echo e(__('admin.documents.table.reason')); ?></div>
              </div>
              <div class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300 whitespace-normal overflow-hidden">
                <?php echo e($document->rejection_reason); ?>

              </div>
            </div>
          </div>
        <?php endif; ?>
      </span>
    </div>
  </td>
  <td class="px-4 py-3 text-center">
    <div class="flex items-center justify-center gap-2 flex-wrap sm:flex-nowrap whitespace-nowrap">
      <?php if($document->file): ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('download', 'DataEntryOperators\\DocumentsController')): ?>
          <form action="<?php echo e(route('data_entry_operators.documents.download', $document)); ?>" method="GET" class="inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="download-item-btn bg-success-600 hover:bg-success-700 text-white font-medium w-10 h-10 flex justify-center items-center rounded-full cursor-pointer">
              <iconify-icon icon="solar:download-linear" class="menu-icon text-xl"></iconify-icon>
            </button>
          </form>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('verify', 'DataEntryOperators\\DocumentsController')): ?>
          <a href="#"
              class="verify-item-btn bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 font-sm px-4 py-2 rounded-full transition-colors whitespace-nowrap min-w-[8.5rem] text-center"
              data-action="click->document#verify" 
              data-document-id="<?php echo e($document->id); ?>"
              data-document-status="<?php echo e($document->status); ?>" 
              data-document-rejection-reason="<?php echo e($document->rejection_reason); ?>"
              data-document-comments="<?php echo e($document->documentComments->map(function($c) { 
                  return [
                      'type' => $c->comment_type,
                      'description' => $c->description,
                      'created_at' => $c->created_at->format('d M, Y, h:i A')
                  ];
              })->toJson()); ?>">
            <?php echo e(__('admin.documents.table.verify_document')); ?>

          </a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </td>
</tr><?php /**PATH D:\xampp\htdocs\balantro\resources\views\data_entry_operators\documents\_document.blade.php ENDPATH**/ ?>