<div class="container mx-auto px-4 py-8">
  <div class="flex justify-between items-center mb-6">
    <h6 class="font-semibold mb-0 dark:text-white">
      <?php echo e(__("doc_activities.table.title")); ?>

    </h6>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-12">
    <div class="col-span-12">
      <div class="card !border-0">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table bordered-table mb-0">
              <thead>
                <tr>
                  <th scope="col"><?php echo e(__("doc_activities.table.time_column")); ?></th>
                  <th scope="col"><?php echo e(__("doc_activities.table.event_column")); ?></th>
                  <th scope="col"><?php echo e(__("doc_activities.table.user_column")); ?></th>
                  <th scope="col"><?php echo e(__("doc_activities.table.changes_column")); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if($doc_activities->count() > 0): ?>
                  <?php $__currentLoopData = $doc_activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                      <td>
                        <?php echo e($activity->created_at->format('d M, Y \a\t h:i A')); ?>

                      </td>
                      <td>
                        <?php echo e(ucfirst($activity->event)); ?>

                      </td>
                      <td>
                        <?php if($activity->whodunnit): ?>
                          <?php
                            $user = $users_by_id[$activity->whodunnit] ?? null;
                          ?>
                          <?php echo e($user ? ucfirst($user->name) : "User ID: {$activity->whodunnit}"); ?>

                        <?php else: ?>
                          <?php echo e(__("doc_activities.table.no_user")); ?>

                        <?php endif; ?>
                      </td>
                      <td>
                        <?php
                          $user = $activity->whodunnit ? ($users_by_id[$activity->whodunnit] ?? null) : null;
                        ?>
                        <?php echo formatted_activity_changes($activity, $document, $user); ?>

                      </td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center py-12 text-neutral-500 dark:text-neutral-400">
                      <div class="flex flex-col items-center justify-center">
                        <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl text-neutral-400 mb-3"></iconify-icon>
                        <p class="text-lg font-medium mb-1">
                          <?php echo e(__("doc_activities.table.no_activities_title")); ?>

                        </p>
                        <p class="text-sm">
                          <?php echo e(__("doc_activities.table.no_activities_description")); ?>

                        </p>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\supervisors\documents\doc_activities.blade.php ENDPATH**/ ?>