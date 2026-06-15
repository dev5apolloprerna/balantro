<div class="container mx-auto px-4 py-8">
  <div class="flex justify-between items-center mb-6">
    <h6 class="font-semibold mb-0 dark:text-white"><?php echo e(__('managers.supervisors.index.title')); ?></h6>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-12">
    <div class="col-span-12">
      <div class="card !border-0 overflow-hidden">
        <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 p-4 sm:p-6">
          <div class="flex flex-col gap-3">
            <div class="flex flex-col sm:flex-row gap-3 w-full">
              <form action="<?php echo e(route('managers.supervisors.index')); ?>" method="GET" class="w-full" data-remote="true">
                <?php echo csrf_field(); ?>
                <div class="flex flex-col sm:flex-row gap-3 w-full">
                  <div class="relative">
                    <input type="text" name="query" value="<?php echo e(request('query')); ?>"
                           class="bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 h-10 pl-10 pr-4 rounded-lg focus:outline-none border border-neutral-300 dark:border-neutral-600 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-[16px] w-full"
                           placeholder="<?php echo e(__('search.placeholder')); ?>">
                    <iconify-icon icon="ion:search-outline" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-neutral-800 dark:text-neutral-100 text-lg"></iconify-icon>
                  </div>

                  <button type="submit" class="w-full sm:w-auto items-center btn bg-primary-600 hover:bg-primary-700 text-white h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
                    <?php echo e(__('search.search')); ?>

                  </button>
                  <?php if(request('query')): ?>
                    <a href="<?php echo e(route('managers.supervisors.index')); ?>" class="w-full sm:w-auto items-center btn border border-danger-600 bg-hover-danger-200 !text-danger-500 h-10 px-4 rounded-lg text-[16px] whitespace-nowrap justify-center cursor-pointer">
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
                  <th scope="col"><?php echo e(__('managers.supervisors.index.table_headers.name')); ?></th>
                  <th scope="col"><?php echo e(__('managers.supervisors.index.table_headers.email')); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if($supervisors->count()): ?>
                  <?php $__currentLoopData = $supervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('managers.supervisors._supervisor', ['supervisor' => $supervisor], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                      <div class="flex flex-col items-center justify-center">
                        <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl text-neutral-400 mb-3" title="<?php echo e(__('managers.supervisors.index.no_supervisor_found.icon_title')); ?>"></iconify-icon>
                        <p class="text-lg font-medium mb-1"><?php echo e(__('managers.supervisors.index.no_supervisor_found.message')); ?></p>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <?php echo $__env->make('shared.pagination', ['resources' => $supervisors], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
      </div>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\supervisors\index.blade.php ENDPATH**/ ?>