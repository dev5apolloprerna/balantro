<div class="flex flex-col h-full bg-white dark:bg-neutral-800" data-controller="client-selection">
    <input type="hidden" name="selection_mode" value="<?php echo e($selectionMode ?? false); ?>"
        data-client-selection-target="selectionMode">

    <!-- Header -->
    <div class="flex items-center justify-between gap-2 px-4 py-3 border-b border-neutral-200 dark:border-neutral-700">
        <div class="flex items-center gap-3">
            <img src="<?php echo e(asset('light-logo.svg')); ?>" alt="light-logo" class="light-logo block dark:hidden">
            <img src="<?php echo e(asset('dark-logo.svg')); ?>" alt="dark-logo" class="dark-logo hidden dark:block">
        </div>

        <button
            class="w-10 h-10 flex items-center justify-center rounded-md text-neutral-700 dark:text-neutral-300 hover:bg-neutral-200/50 dark:hover:bg-neutral-600/50 transition-colors cursor-pointer relative overflow-hidden"
            data-action="click->client-selection#toggleSelection mouseup->client-selection#releaseButton mousedown->client-selection#pressButton"
            data-client-selection-target="selectionButton" title="Select Clients">
            <iconify-icon icon="mdi:account-group" class="text-2xl"></iconify-icon>
            <span class="absolute inset-0 bg-neutral-200/50 opacity-0 transition-opacity duration-200"
                data-client-selection-target="buttonRipple"></span>
        </button>
    </div>

    <!-- Search -->
    <div class="chat-search w-full relative px-4 py-3 border-b border-neutral-200 dark:border-neutral-700">
        <span
            class="icon absolute start-5 top-1/2 -translate-y-1/2 text-xl flex text-neutral-700 dark:text-neutral-400">
            <iconify-icon icon="iconoir:search" class="mt-[7px]"></iconify-icon>
        </span>
        <form action="<?php echo e(route('managers.messages.index')); ?>" method="GET" data-turbo-frame="mobile_chat"
            data-controller="form" data-action="input->form#submit">
            <input type="hidden" name="selection_mode" value="<?php echo e($selectionMode ?? false); ?>"
                data-client-selection-target="selectionModeField">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                class="border-0 border-t border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 w-full focus:outline-none focus:ring-0 ps-12 pe-6 placeholder-neutral-800 dark:placeholder-neutral-200 py-3"
                autocomplete="off" placeholder="<?php echo e(__('chat.search.placeholder')); ?>">
        </form>
    </div>

    <div id="selected-clients-container"
        class="hidden bg-white dark:bg-neutral-700 px-4 py-2 border-b border-neutral-200 dark:border-neutral-600 flex items-center gap-4 overflow-x-auto"
        data-client-selection-target="selectedClientsContainer">
        <label class="flex items-center gap-2 cursor-pointer shrink-0">
            <div class="flex items-center justify-center w-5 h-5 rounded border border-neutral-300 dark:border-neutral-500 bg-white dark:bg-neutral-700 transition-all duration-200 relative"
                data-client-selection-target="selectAllCheckboxContainer">
                <input type="checkbox" data-action="change->client-selection#toggleSelectAll"
                    data-client-selection-target="selectAllCheckbox" class="hidden peer">
                <iconify-icon icon="heroicons:check-20-solid"
                    class="text-white text-sm opacity-0 peer-checked:opacity-100 transition-opacity duration-200 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10"
                    data-client-selection-target="selectAllCheckboxIcon"></iconify-icon>
                <div
                    class="w-full h-full rounded-sm peer-checked:bg-primary-600 dark:peer-checked:bg-primary-500 transition-colors duration-200">
                </div>
            </div>
            <span class="text-sm"><?php echo e(__('chat.client_list.select_all')); ?></span>
        </label>
    </div>

    <!-- Client List -->
    <div class="flex-1 overflow-y-auto divide-y divide-neutral-200 dark:divide-neutral-700">
        <?php if(count($clients) > 0): ?>
            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-3 px-4 py-3 hover:bg-neutral-100 dark:hover:bg-neutral-700 cursor-pointer"
                    data-action="click->client-selection#handleClientClick"
                    data-client-selection-client-id-value="<?php echo e($client->id); ?>">
                    <div class="flex items-center gap-2 w-full" data-controller="update-recepient"
                        data-update-recepient-recipient-id-value="<?php echo e($client->id); ?>">
                        <div class="flex items-center justify-center w-5 h-5 rounded border border-neutral-300 dark:border-neutral-500 bg-white dark:bg-neutral-700 mr-2 shrink-0 transition-all duration-200 relative <?php echo e(!$selectionMode ? 'hidden' : ''); ?>"
                            data-client-selection-target="checkbox">
                            <input type="checkbox" name="selected_clients[]" value="<?php echo e($client->id); ?>"
                                class="hidden peer" data-client-selection-target="checkboxInput">
                            <iconify-icon icon="heroicons:check-20-solid"
                                class="text-white text-sm opacity-0 peer-checked:opacity-100 transition-opacity duration-200 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10"
                                data-client-selection-target="checkboxIcon"></iconify-icon>
                            <div class="w-full h-full rounded-sm peer-checked:bg-primary-600 dark:peer-checked:bg-primary-500 transition-colors duration-200"
                                data-client-selection-target="checkboxBackground"></div>
                        </div>

                        <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center"
                            style="width: 40px; height: 40px;">
                            <?php echo e(strtoupper(substr($client->name, 0, 1))); ?>

                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium line-clamp-1"><?php echo e($client->name); ?></div>
                            <div class="text-xs text-neutral-500 dark:text-neutral-400 line-clamp-1">
                                <?php echo e(__('chat.client_list.click_to_chat')); ?>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center h-full text-center py-10 px-6">
                <div
                    class="w-20 h-20 mx-auto bg-neutral-200 dark:bg-neutral-700 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-neutral-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="text-lg font-medium text-neutral-800 dark:text-white mb-1">
                    <?php echo e(__('chat.client_list.no_clients')); ?>

                </div>
                <p class="text-neutral-600 dark:text-neutral-400 text-sm">
                    <?php if(request('search')): ?>
                        <?php echo e(__('chat.client_list.no_results', ['search' => request('search')])); ?>

                    <?php else: ?>
                        <?php echo e(__('chat.client_list.no_clients_available')); ?>

                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <div class="sticky bottom-0 z-10 p-4 bg-white dark:bg-neutral-800 border-t border-neutral-200 dark:border-neutral-700 <?php echo e(!$selectionMode ? 'hidden' : ''); ?>"
        data-client-selection-target="footer">
        <form action="<?php echo e(route('managers.messages.store')); ?>" method="POST" class="flex flex-col gap-2"
            data-controller="message" data-action="submit->message#submitForm input->message#toggleSubmitButton">
            <?php echo csrf_field(); ?>
            <div id="selected-clients-fields" data-client-selection-target="selectedClientsFields">
                <?php if($selectionMode): ?>
                    <?php $__currentLoopData = (array) $selectedClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="selected_clients[]" value="<?php echo e($clientId); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <input type="hidden" name="receiver_id" value="<?php echo e($selectedClient?->id); ?>">
                <?php endif; ?>
            </div>

            <div id="file-preview-container" class="hidden mb-2" data-message-target="filePreviewContainer">
                <div
                    class="flex items-center gap-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg px-3 py-2 overflow-x-scroll">
                    <div class="flex items-center gap-2" data-message-target="filePreviews">
                    </div>
                    <button type="button"
                        class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 !mt-[-7px]"
                        data-action="click->message#clearAllFiles"
                        title="<?php echo e(__('chat.messages.file_input.clear_all')); ?>">
                        <iconify-icon icon="heroicons:x-mark" class="text-lg"></iconify-icon>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="file" name="attachments" multiple class="hidden" id="bulk-file-upload-input-mobile"
                    data-message-target="fileInput" data-action="change->message#displayFilePreview">

                <button type="button"
                    class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 transition-colors"
                    style="margin-top: -4px;" data-action="click->message#triggerFileInput"
                    title="<?php echo e(__('chat.messages.file_input.attach')); ?>">
                    <iconify-icon icon="heroicons:paper-clip" class="text-lg"></iconify-icon>
                </button>

                <div class="flex-1 relative">
                    <textarea name="description" rows="1"
                        class="w-full resize-none overflow-y-auto max-h-[6.6em] pl-4 pr-12 pt-[0.6rem] pb-[0.6rem] rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 placeholder-neutral-800 dark:placeholder-neutral-200 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 leading-[1.5rem]"
                        placeholder="<?php echo e(__('chat.messages.input.placeholder')); ?>" autocomplete="off"
                        data-action="input->client-selection#autoResize" data-message-target="input"></textarea>

                    <button type="submit"
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 cursor-pointer transition-opacity duration-200 mt-[-4px]"
                        style="opacity: 0;" data-message-target="submitButton">
                        <iconify-icon icon="heroicons:paper-airplane" class="text-lg"></iconify-icon>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\messages\_mobile_client_list.blade.php ENDPATH**/ ?>