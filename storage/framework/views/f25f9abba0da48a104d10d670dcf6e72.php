
<div class="h-full flex flex-col bg-white dark:bg-black border border-gray-300 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm dark:shadow-none">
    
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
        <div class="flex items-center gap-3">
            <button type="button" onclick="goBackToClientList()" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white p-2 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold text-sm">
                    <?php echo e(strtoupper(substr($selected_client->name ?? 'C', 0, 1))); ?>

                </div>
                <div class="text-gray-900 dark:text-white font-medium"><?php echo e($selected_client->name ?? 'Client'); ?></div>
            </div>
        </div>
    </div>

    
    <div id="mobileChatMessages" class="flex-1 min-h-0 flex flex-col p-3 overflow-y-auto bg-white dark:bg-black">
        <?php
            $prevDay = null;
            $roleLetter = function ($msg) use ($selected_client) {
                if (isset($msg->sender) && !empty($msg->sender->type)) {
                    return match (strtolower($msg->sender->type)) {
                        'client', 'customer' => 'C',
                        'data_entry_operator', 'dataentryoperator' => 'D',
                        'supervisor' => 'S',
                        'manager' => 'M',
                        default => strtoupper(substr($msg->sender->type, 0, 1)),
                    };
                }

                if (auth()->check() && (int) $msg->sender_id === (int) auth()->id()) {
                    return 'M'; // Manager
                }

                if ($selected_client && (int) $msg->sender_id === (int) $selected_client->user_id) {
                    return 'C'; // Client
                }

                return 'U';
            };

            $fmtSize = function ($bytes = null) {
                if (!$bytes || !is_numeric($bytes)) {
                    return null;
                }
                $u = ['B', 'KB', 'MB', 'GB', 'TB'];
                $i = 0;
                while ($bytes >= 1024 && $i < count($u) - 1) {
                    $bytes /= 1024;
                    $i++;
                }
                return ($i ? number_format($bytes, 1) : (int) $bytes) . ' ' . $u[$i];
            };
        ?>

        <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isMe = (int) $msg->sender_id === (int) auth()->id();
                $dayKey = $msg->created_at->toDateString();
            ?>

            
            <?php if($dayKey !== $prevDay): ?>
                <div class="my-2 flex justify-center">
                    <span class="px-3 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                        <?php if($dayKey === now()->toDateString()): ?>
                            Today
                        <?php elseif($dayKey === now()->subDay()->toDateString()): ?>
                            Yesterday
                        <?php else: ?>
                            <?php echo e($msg->created_at->format('D, d M Y')); ?>

                        <?php endif; ?>
                    </span>
                </div>
                <?php $prevDay = $dayKey; ?>
            <?php endif; ?>

            
            <div class="mt-1 flex <?php echo e($isMe ? 'justify-end' : 'justify-start'); ?>">
                <?php if(!$isMe): ?>
                    <div class="mr-2 mt-0.5 shrink-0">
                        <div class="w-6 h-6 rounded-full grid place-items-center text-[10px] font-semibold bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            <?php echo e($roleLetter($msg)); ?>

                        </div>
                    </div>
                <?php endif; ?>

                <div class="max-w-[85%] px-3 py-2 rounded-2xl leading-snug <?php echo e($isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-300 dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-bl-none'); ?>">

                    
                    <?php $text = trim((string)($msg->description ?? $msg->body ?? '')); ?>
                    <?php if($text !== ''): ?>
                        <div class="flex items-end justify-between gap-2">
                            <p class="whitespace-pre-line break-words flex-1 leading-snug text-sm"><?php echo e($text); ?>

                            </p>
                            <span class="text-[10px] opacity-80 shrink-0 ml-2 whitespace-nowrap self-end">
                                <?php echo e($msg->created_at->format('H:i')); ?>

                            </span>
                        </div>
                    <?php endif; ?>

                    
                    <?php
                        $files = collect($msg->attachments ?? ($msg->documents ?? []));
                    ?>

                    <?php if($files->count()): ?>
                        <div class="mt-2 grid grid-cols-1 gap-2">
                            <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $url = data_get($att, 'url') ?? (data_get($att, 'file_url') ?? (data_get($att, 'path') ? \Illuminate\Support\Facades\Storage::url(data_get($att, 'path')) : null));
                                    $name = data_get($att, 'original_name') ?? (data_get($att, 'name') ?? ($url ? basename(parse_url($url, PHP_URL_PATH)) : 'file'));
                                    $mime = strtolower(data_get($att, 'mime_type') ?? (data_get($att, 'mime') ?? ''));
                                    $ext = strtolower(pathinfo(parse_url($url ?? '', PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                                    $isImg = $mime ? str_starts_with($mime, 'image/') : in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                                    $isVideo = $mime ? str_starts_with($mime, 'video/') : in_array($ext, ['mp4', 'webm', 'mov', 'm4v']);
                                    $sizeTxt = $fmtSize(data_get($att, 'size'));
                                ?>

                                <?php if($url): ?>
                                    <?php if($isImg): ?>
                                        <a href="<?php echo e($url); ?>" target="_blank" rel="noopener"
                                            class="block overflow-hidden rounded-xl border border-gray-300 dark:border-gray-600 hover:opacity-95 transition-opacity">
                                            <img src="<?php echo e($url); ?>" alt="<?php echo e($name); ?>" loading="lazy"
                                                class="w-full h-32 object-cover">
                                        </a>
                                    <?php elseif($isVideo): ?>
                                        <div class="overflow-hidden rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900">
                                            <video src="<?php echo e($url); ?>" class="w-full h-32 object-cover"
                                                controls></video>
                                        </div>
                                    <?php else: ?>
                                        <a href="<?php echo e($url); ?>" target="_blank" rel="noopener"
                                            class="flex items-center gap-2 p-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 hover:bg-gray-300 dark:hover:bg-gray-800 transition-colors">
                                            <i class="fa-regular fa-file-lines text-sm text-gray-600 dark:text-gray-400"></i>
                                            <span class="text-xs truncate max-w-[120px] text-gray-700 dark:text-gray-300"><?php echo e($name); ?></span>
                                            <?php if($sizeTxt): ?>
                                                <span class="text-[10px] opacity-70 text-gray-600 dark:text-gray-400"><?php echo e($sizeTxt); ?></span>
                                            <?php endif; ?>
                                            <i class="fa-solid fa-arrow-down-to-line text-xs opacity-80 ml-auto text-gray-600 dark:text-gray-400"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        
                        <?php if($text === ''): ?>
                            <div class="text-[10px] mt-1 opacity-80 text-right text-gray-600 dark:text-gray-400">
                                <?php echo e($msg->created_at->format('H:i')); ?>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if($isMe): ?>
                    <div class="ml-2 mt-0.5 shrink-0">
                        <div class="w-6 h-6 rounded-full grid place-items-center text-[10px] font-semibold bg-indigo-600 text-white">
                            <?php echo e($roleLetter($msg)); ?>

                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php if($selected_client): ?>
        <?php
            $formId = 'mobileManagerChatForm';
            $attachId = 'mobileManagerAttach';
            $sendButtonId = 'mobileManagerSendButton';
        ?>

        <form id="<?php echo e($formId); ?>" action="<?php echo e(route('manager.messages.store')); ?>" method="POST"
            enctype="multipart/form-data" class="flex-shrink-0 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="receiver_id" value="<?php echo e($selected_client->id); ?>" />

            <div class="px-3 pt-2">
                <div data-preview class="hidden mb-2 flex flex-wrap gap-2"></div>
            </div>

            <div class="px-3 pb-3 pt-1 flex items-end gap-2">
                <input id="<?php echo e($attachId); ?>" type="file" name="attachments[]" class="sr-only chat-file-input"
                    multiple accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar,.csv,.ppt,.pptx">

                <label for="<?php echo e($attachId); ?>"
                    class="shrink-0 p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 cursor-pointer transition-colors"
                    title="Attach files">
                    <i class="fa-solid fa-paperclip text-sm"></i>
                </label>

                <textarea name="description" rows="1" placeholder="Type a message…"
                    class="min-h-[44px] max-h-32 flex-1 resize-y rounded-xl px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-400 placeholder-gray-500 dark:placeholder-gray-400 text-sm"></textarea>

                <button type="submit" id="<?php echo e($sendButtonId); ?>"
                    class="shrink-0 px-4 h-11 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition-colors">
                    Send
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    function goBackToClientList() {
        // Remove the client parameter from URL to go back to client list
        const url = new URL(window.location.href);
        url.searchParams.delete('client');
        window.location.href = url.toString();
    }

    // Rest of your existing JavaScript for form handling...
    (function() {
        // Mobile chat auto-scroll
        const mobileScroll = document.getElementById('mobileChatMessages');
        if (mobileScroll) {
            mobileScroll.scrollTop = mobileScroll.scrollHeight;
        }

        // Get form elements for mobile
        const form = document.getElementById('<?php echo e($formId); ?>');
        const input = document.getElementById('<?php echo e($attachId); ?>');
        const preview = form ? form.querySelector('[data-preview]') : null;
        const textarea = form ? form.querySelector('textarea[name="description"]') : null;
        const sendButton = document.getElementById('<?php echo e($sendButtonId); ?>');

        if (!form || !input || !preview || !textarea || !sendButton) return;

        const objectUrls = [];

        // Function to update send button state
        function updateSendButton() {
            const hasText = textarea.value.trim().length > 0;
            const hasFiles = input.files && input.files.length > 0;
            const shouldEnable = hasText || hasFiles;

            sendButton.disabled = !shouldEnable;

            if (shouldEnable) {
                sendButton.classList.remove('bg-indigo-400', 'cursor-not-allowed');
                sendButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'cursor-pointer');
            } else {
                sendButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'cursor-pointer');
                sendButton.classList.add('bg-indigo-400', 'cursor-not-allowed');
            }
        }

        // Initialize button state
        updateSendButton();

        // Listen for text input
        textarea.addEventListener('input', updateSendButton);

        // Auto-resize textarea
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 128) + 'px';
        });

        input.addEventListener('change', () => {
            // clear old previews and revoke object URLs
            objectUrls.splice(0).forEach(u => URL.revokeObjectURL(u));
            preview.innerHTML = '';

            const files = Array.from(input.files || []);
            if (!files.length) {
                preview.classList.add('hidden');
                updateSendButton();
                return;
            }
            preview.classList.remove('hidden');

            files.forEach((f, idx) => {
                const isImg = f.type && f.type.startsWith('image/');
                const url = isImg ? URL.createObjectURL(f) : '';
                if (url) objectUrls.push(url);

                const chip = document.createElement('div');
                chip.className = 'group relative rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 overflow-hidden';
                chip.innerHTML = isImg ?
                    `<img src="${url}" class="w-20 h-16 object-cover block">
                       <button type="button" data-i="${idx}"
                               class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center transition-opacity">
                         <i class="fa-solid fa-xmark text-xs"></i>
                       </button>` :
                    `<div class="px-2 py-1 flex items-center gap-2">
                         <i class="fa-regular fa-file-lines text-xs"></i>
                         <span class="max-w-[100px] truncate text-xs">${f.name}</span>
                       </div>
                       <button type="button" data-i="${idx}"
                               class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center transition-opacity">
                         <i class="fa-solid fa-xmark text-xs"></i>
                       </button>`;

                preview.appendChild(chip);
            });

            // Update button state after files are added
            updateSendButton();

            // remove single file from FileList
            preview.querySelectorAll('button[data-i]').forEach(btn => {
                btn.classList.remove('hidden');
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const removeIdx = Number(btn.dataset.i);
                    const dt = new DataTransfer();
                    Array.from(input.files).forEach((f, i) => {
                        if (i !== removeIdx) dt.items.add(f);
                    });
                    input.files = dt.files;
                    input.dispatchEvent(new Event('change'));
                });
            });
        });

        // Reset form after sending
        form.addEventListener('submit', function() {
            // Clear form after a short delay
            setTimeout(() => {
                textarea.value = '';
                textarea.style.height = 'auto';
                preview.innerHTML = '';
                preview.classList.add('hidden');
                updateSendButton();

                // Clear file input
                input.value = '';

                // Scroll to bottom after sending
                if (mobileScroll) {
                    mobileScroll.scrollTop = mobileScroll.scrollHeight;
                }
            }, 100);
        });

        // cleanup
        window.addEventListener('beforeunload', () => objectUrls.forEach(u => URL.revokeObjectURL(u)));
    })();
</script><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\messages\mobile_chat_content.blade.php ENDPATH**/ ?>