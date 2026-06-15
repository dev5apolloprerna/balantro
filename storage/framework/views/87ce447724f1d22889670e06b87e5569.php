<div class="flex <?php echo e($sender ? 'justify-end' : 'justify-start'); ?>" id="message-<?php echo e($message->id); ?>">
    <div class="flex max-w-[80%] gap-3">
        <?php if(!$sender): ?>
            <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center flex-shrink-0" style="width: 32px; height: 32px;">
                <?php echo e($message->sender->is_client ? strtoupper(substr($message->sender->name, 0, 1)) : 'B'); ?>

            </div>
        <?php endif; ?>

        <div class="flex flex-col <?php echo e($sender ? 'items-end' : 'items-start'); ?>">
            <?php if($message->documents->isNotEmpty()): ?>
                <div class="grid gap-2 mb-2">
                    <?php $__currentLoopData = $message->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center gap-2 bg-white dark:bg-neutral-700 rounded-lg p-2 border border-neutral-200 dark:border-neutral-600">
                            <?php if(str_starts_with($document->mime_type, 'image/')): ?>
                                <iconify-icon icon="heroicons:photo" class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                            <?php else: ?>
                                <iconify-icon icon="heroicons:document" class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                            <?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate max-w-[45px]"><?php echo e($document->file_name); ?></div>
                                <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                    <?php echo e(formatBytes($document->size)); ?>

                                </div>
                            </div>
                            <div class="ml-2">
                                <a href="<?php echo e(route('file.download', $document)); ?>" 
                                    class="w-9 h-9 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                                    title="<?php echo app('translator')->get('chat.messages.message.download'); ?>">
                                    <iconify-icon icon="heroicons:arrow-down-tray" class="text-[1.1rem] p-0.5"></iconify-icon>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <?php if($message->description): ?>
                <div class="px-4 py-3 rounded-2xl <?php echo e($sender ? 'bg-primary-500 text-white rounded-br-none' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200 rounded-bl-none'); ?>">
                    <p class="mb-0 break-all overflow-wrap-anywhere"><?php echo e($message->description); ?></p>
                </div>
            <?php endif; ?>

            <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                <?php echo e($message->created_at->format(config('chat.time.formats.short'))); ?>

            </div>
        </div>

        <?php if($sender): ?>
            <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center flex-shrink-0" style="width: 32px; height: 32px;">
                <?php echo e(strtoupper(substr($message->sender->name, 0, 1))); ?>

            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\client\_messages\message_block_for_chat.blade.php ENDPATH**/ ?>