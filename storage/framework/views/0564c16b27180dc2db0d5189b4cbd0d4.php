<div class="container mx-auto px-4 py-8">
  <div class="flex justify-between items-center mb-6">
    <h6 class="font-semibold mb-0 dark:text-white">
      <?php echo e(__('client.documents.index.title')); ?>

    </h6>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', 'App\Http\Controllers\DocumentsController')): ?>
      <button type="button"
              data-action="click->document-upload#openModal"
              class="flex items-center gap-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 cursor-pointer">
        <iconify-icon icon="fa6-regular:square-plus" class="text-lg"></iconify-icon>
        <span><?php echo e(__('client.documents.index.add_button')); ?></span>
      </button>
    <?php endif; ?>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-12">
    <div class="col-span-12">
      <div class="card !border-0">
        <?php echo $__env->make('documents.filters', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="card-body">
          <div id="client-table">
            <?php echo $__env->make('documents.document_table', ['client_documents' => $client_documents], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          </div>
          <?php if($client_documents && $client_documents->count()): ?>
            <?php echo $__env->make('shared.pagination', ['resources' => $client_documents], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\documents\_document_list.blade.php ENDPATH**/ ?>