<div class="container mx-auto" data-controller="document" data-document-url-value="<?php echo e(route('managers.documents.index')); ?>">
  <div class="flex justify-between items-center mb-6">
    <h6 class="font-semibold mb-0 dark:text-white"><?php echo e(__('managers.documents.index.title')); ?></h6>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12">
    <div class="col-span-12">
      <div class="card !border-0 rounded-lg overflow-hidden">
        <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 p-4 sm:p-6">
          <div class="flex flex-col gap-3">
            <div class="flex flex-col sm:flex-row gap-3 w-full">
              <form action="<?php echo e(route('managers.documents.index')); ?>" method="GET" class="w-full" id="documents-filter-form">
                <div class="flex flex-col md:flex-row md:flex-wrap gap-3 w-full">
                  <div class="w-full md:w-[calc(50%-0.375rem)] lg:w-[250px]">
                    <select name="client_id" class="custom-select-arrow bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 !rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full cursor-pointer">
                      <option value=""><?php echo e(__('dropdowns.client')); ?></option>
                      <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($client->id); ?>" <?php echo e(request('client_id') == $client->id ? 'selected' : ''); ?>><?php echo e($client->name); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>

                  <div class="w-full md:w-[calc(50%-0.375rem)] lg:w-[250px]">
                    <select name="status" class="custom-select-arrow bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 !rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full cursor-pointer">
                      <option value=""><?php echo e(__('dropdowns.status')); ?></option>
                      <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status); ?>" <?php echo e(request('status') == $status ? 'selected' : ''); ?>>
                          <?php echo e(__('admin.documents.statuses.' . $status)); ?>

                        </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>

                  <div data-controller="flatpickr" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto items-center justify-between sm:justify-center">
                    <div class="relative w-full sm:w-[255px]">
                      <input type="text" name="start_date" value="<?php echo e(request('start_date')); ?>" data-flatpickr-target="start"
                             class="bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full"
                             placeholder="<?php echo e(__('search.start_date')); ?>">
                      <span class="absolute end-0 top-1/2 -translate-y-1/2 me-3 line-height-1 pointer-events-none">
                        <iconify-icon icon="solar:calendar-linear" class="icon text-lg mt-[5px]"></iconify-icon>
                      </span>
                    </div>

                    <span class="text-neutral-600 dark:text-neutral-300 text-sm sm:text-base mx-0 sm:mx-2s whitespace-nowrap">
                      <?php echo e(__('search.to')); ?>

                    </span>

                    <div class="relative w-full sm:w-[240px]">
                      <input type="text" name="end_date" value="<?php echo e(request('end_date')); ?>" data-flatpickr-target="end"
                             class="bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 px-4 rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full"
                             placeholder="<?php echo e(__('search.end_date')); ?>">
                      <span class="absolute end-0 top-1/2 -translate-y-1/2 me-3 line-height-1 pointer-events-none">
                        <iconify-icon icon="solar:calendar-linear" class="icon text-lg mt-[5px]"></iconify-icon>
                      </span>
                    </div>
                  </div>

                  <button type="submit" class="w-full sm:w-auto items-center btn bg-primary-600 hover:bg-primary-700 text-white h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
                    <?php echo e(__('search.search')); ?>

                  </button>

                  <?php if(request('client_id') || request('status') || request('start_date') || request('end_date')): ?>
                    <a href="<?php echo e(route('managers.documents.index')); ?>"
                       class="w-full sm:w-auto items-center btn border border-danger-600 bg-hover-danger-200 !text-danger-500 h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
                      <?php echo e(__('search.reset')); ?>

                    </a>
                  <?php endif; ?>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table bordered-table mb-0">
              <thead>
                <tr>
                  <th scope="col"><?php echo e(__('managers.documents.index.table_headers.document_name')); ?></th>
                  <th scope="col"><?php echo e(__('admin.documents.table.upload_date')); ?></th>
                  <th scope="col"><?php echo e(__('managers.documents.index.table_headers.client_name')); ?></th>
                  <th scope="col"><?php echo e(__('managers.documents.index.table_headers.status')); ?></th>
                  <th scope="col" class="!text-center"><?php echo e(__('managers.documents.index.table_headers.actions')); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if($documents->count()): ?>
                  <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('managers.documents._document', ['document' => $document], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                      <div class="flex flex-col items-center justify-center">
                        <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl text-neutral-400 mb-3" title="<?php echo e(__('managers.documents.index.no_documents_found.icon_title')); ?>"></iconify-icon>
                        <p class="text-lg font-medium mb-1"><?php echo e(__('managers.documents.index.no_documents_found.message')); ?></p>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <?php echo e($documents->links()); ?>

        </div>
      </div>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\documents\index.blade.php ENDPATH**/ ?>