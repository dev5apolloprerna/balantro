<div class="flex flex-col h-[calc(97vh-100px)] bg-white dark:bg-neutral-800 !rounded-full" data-controller='client--message'>
    <div class="flex items-center gap-4 px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 sticky">
        <img src="<?php echo e(asset('images/light-logo.svg')); ?>" alt="light-logo" class="light-logo block dark:hidden">
        <img src="<?php echo e(asset('images/dark-logo.svg')); ?>" alt="dark-logo" class="dark-logo hidden dark:block">
    </div>

    <div class="flex-1 overflow-y-auto p-6 bg-white dark:bg-neutral-800 space-y-4 messageBox-<?php echo e(auth()->id()); ?>" id="messages" data-is-client="true">
        <div id="client_message_blocks">
            <?php if($messages->isNotEmpty()): ?>
                <?php $last_message_date = null ?>
                <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $current_message_date = $message->created_at->toDateString() ?>
                    <?php if($last_message_date != $current_message_date): ?>
                        <div class="flex justify-center my-4">
                            <span class="px-3 py-1 text-xs bg-neutral-200 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-full">
                                <?php if($current_message_date == now()->toDateString()): ?>
                                    <?php echo app('translator')->get('chat.messages.today'); ?>
                                <?php elseif($current_message_date == now()->subDay()->toDateString()): ?>
                                    <?php echo app('translator')->get('chat.messages.yesterday'); ?>
                                <?php else: ?>
                                    <?php echo e($message->created_at->formatLocalized('%B %d, %Y')); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        <?php $last_message_date = $current_message_date ?>
                    <?php endif; ?>

                    <?php echo $__env->make('client.messages.message_block', ['message' => $message], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center h-full text-center py-10 px-6">
                    <div class="w-20 h-20 mx-auto bg-neutral-200 dark:bg-neutral-700 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-neutral-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-neutral-800 dark:text-white mb-2">
                        <?php echo app('translator')->get('chat.messages.no_messages.title'); ?>
                    </h3>
                    <p class="text-neutral-600 dark:text-neutral-300 text-sm">
                        <?php echo app('translator')->get('chat.messages.no_messages.description', ['name' => 'Balantro']); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-4 sticky bottom-0 z-20" id="message_form">
        <form action="<?php echo e(route('client.messages.store')); ?>" 
              method="POST"
              class="flex flex-col gap-2"
              enctype="multipart/form-data"
              data-controller="message" 
              data-action="submit->message#submitForm input->message#toggleSubmitButton">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="receiver_id" value="<?php echo e($receiver_id); ?>">
            <input type="hidden" name="is_first_message" value="<?php echo e($messages->isEmpty() ? 'true' : 'false'); ?>">

            <div id="file-preview-container" class="hidden mb-2" data-message-target="filePreviewContainer">
                <div class="flex items-center gap-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg px-3 py-2 overflow-x-scroll">
                    <div class="flex items-center gap-2" data-message-target="filePreviews">
                    </div>
                    <button type="button" 
                            class="ml-auto text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200"
                            data-action="click->message#clearAllFiles"
                            title="<?php echo app('translator')->get('chat.messages.file_input.clear_all'); ?>">
                        <iconify-icon icon="heroicons:x-mark" class="text-lg"></iconify-icon>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="file" name="attachments[]" 
                      multiple 
                      class="hidden" 
                      id="file-upload-input"
                      data-message-target="fileInput" 
                      data-action="change->message#displayFilePreview">

                <button type="button" 
                        class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 transition-colors cursor-pointer"
                        data-action="click->message#triggerFileInput"
                        title="<?php echo app('translator')->get('chat.messages.file_input.attach'); ?>">
                    <iconify-icon icon="heroicons:paper-clip" class="text-lg"></iconify-icon>
                </button>

                <div class="flex-1 relative">
                    <input type="text" name="description" 
                          class="w-full py-2 pl-4 pr-12 rounded-full border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 placeholder-neutral-400 dark:placeholder-neutral-500 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500" 
                          autocomplete="off" 
                          placeholder="<?php echo app('translator')->get('chat.messages.input.placeholder'); ?>"
                          data-message-target="input">

                    <button type="submit" 
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 cursor-pointer"
                            style="display: none;"
                            data-message-target="submitButton">
                        <iconify-icon icon="heroicons:paper-airplane" class="text-lg"></iconify-icon>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\client\_messages\message.blade.php ENDPATH**/ ?>