<?php if($message->sender == auth()->user() || $message->sender == $selectedClient): ?>
  <div class="flex <?php echo e($message->sender == auth()->user() ? 'justify-end' : 'justify-start'); ?>" id="message-<?php echo e($message->id); ?>">
    <div class="flex max-w-[80%] gap-3">
      <?php if (! ($message->sender == auth()->user())): ?>
        <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center flex-shrink-0" style="width: 32px; height: 32px;">
          <?php echo e(strtoupper(substr($message->sender->name, 0, 1))); ?>

        </div>
      <?php endif; ?>
      
      <div class="flex flex-col <?php echo e($message->sender == auth()->user() ? 'items-end' : 'items-start'); ?>">
        <?php if($message->documents->count() > 0): ?>
          <div class="grid gap-2 mb-2">
            <?php $__currentLoopData = $message->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="flex items-center gap-2 bg-white dark:bg-neutral-700 rounded-lg p-2 border border-neutral-200 dark:border-neutral-600">
                <?php if(str_starts_with($document->file->mime_type, 'image/')): ?>
                  <div class="relative">
                    <iconify-icon icon="heroicons:photo" class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                  </div>
                <?php else: ?>
                  <div class="relative">
                    <iconify-icon icon="heroicons:document" class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                  </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate max-w-[120px]"><?php echo e($document->file->file_name); ?></div>
                  <div class="text-xs text-neutral-500 dark:text-neutral-400">
                    <?php echo e(formatBytes($document->file->size)); ?>

                  </div>
                </div>
                <div class="ml-2">
                  <a href="<?php echo e(route('file.download', $document->file->id)); ?>" 
                      class="w-9 h-9 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                      title="<?php echo e(__('chat.messages.message.download')); ?>">
                    <iconify-icon icon="heroicons:arrow-down-tray" class="text-[1.1rem] p-0.5"></iconify-icon>
                  </a>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        <?php endif; ?>
        
        <?php if($message->description): ?>
          <div class="px-4 py-3 rounded-2xl <?php echo e($message->sender == auth()->user() ? 'bg-primary-500 text-white rounded-br-none' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200 rounded-bl-none'); ?>">
            <p class="mb-0 break-all overflow-wrap-anywhere"><?php echo e($message->description); ?></p>
          </div>
        <?php endif; ?>

        <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
          <?php echo e($message->created_at->format('h:i A')); ?>

        </div>
      </div>

      <?php if($message->sender == auth()->user()): ?>
        <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center flex-shrink-0" style="width: 32px; height: 32px;">
          <?php echo e(strtoupper(substr($message->sender->name, 0, 1))); ?>

        </div>
      <?php endif; ?>
    </div>
  </div>
<?php else: ?>
  <div class="flex justify-end" id="message-<?php echo e($message->id); ?>">
    <div class="flex max-w-[80%] gap-3">
      <div class="flex flex-col items-end">
        <?php if($message->documents->count() > 0): ?>
          <div class="grid gap-2 mb-2">
            <?php $__currentLoopData = $message->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="flex items-center gap-2 bg-white dark:bg-neutral-700 rounded-lg p-2 border border-neutral-200 dark:border-neutral-600">
                <?php if(str_starts_with($document->file->mime_type, 'image/')): ?>
                  <img src="<?php echo e(Storage::url($document->file->path)); ?>" class="w-12 h-12 object-cover rounded">
                <?php else: ?>
                  <div class="relative">
                    <iconify-icon icon="heroicons:document" class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                  </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate max-w-[120px]"><?php echo e($document->file->file_name); ?></div>
                  <div class="text-xs text-neutral-500 dark:text-neutral-400">
                    <?php echo e(formatBytes($document->file->size)); ?>

                  </div>
                </div>
                <div class="ml-2">
                  <a href="<?php echo e(route('file.download', $document->file->id)); ?>" 
                      class="w-9 h-9 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                      title="<?php echo e(__('chat.messages.message.download')); ?>">
                    <iconify-icon icon="heroicons:arrow-down-tray" class="text-[1.1rem] p-0.5"></iconify-icon>
                  </a>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        <?php endif; ?>
        
        <?php if($message->description): ?>
          <div class="px-4 py-3 rounded-2xl bg-primary-500 text-white rounded-br-none">
            <p class="mb-0 break-all overflow-wrap-anywhere"><?php echo e($message->description); ?></p>
          </div>
        <?php endif; ?>

        <div class="flex items-center gap-1 mt-1">
          <div class="text-xs text-neutral-500 dark:text-neutral-400">
            <?php echo e($message->created_at->format('h:i A')); ?>

          </div>
        </div>
      </div>
      
      <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center flex-shrink-0" style="width: 32px; height: 32px;">
        <?php echo e(strtoupper(substr($message->sender->name, 0, 1))); ?>

      </div>
    </div>
  </div>
<?php endif; ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\messages\_message.blade.php ENDPATH**/ ?>