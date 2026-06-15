
<style>
    /* Disabled button styles */
    button[type="submit"]:disabled {
        cursor: not-allowed !important;
        opacity: 0.7 !important;
    }

    /* Scrollbar styles */
    #desktopChatMessages::-webkit-scrollbar {
        width: 8px;
    }
    #desktopChatMessages::-webkit-scrollbar-thumb {
        background-color: rgba(100, 100, 100, 0.3);
        border-radius: 4px;
    }
    #desktopChatMessages:hover::-webkit-scrollbar-thumb {
        background-color: rgba(100, 100, 100, 0.5);
    }

    /* ===== DARK BLACK THEME ===== */
    /* Main container - Pure black background */
    .bg-white.dark\:bg-black {
        background-color: #000000 !important;
    }

    /* Borders - Dark gray borders */
    .border-gray-300.dark\:border-gray-800 {
        border-color: #1a1a1a !important;
    }

    /* Header background */
    .bg-gray-50.dark\:bg-gray-900 {
        background-color: #0a0a0a !important;
    }

    .border-gray-200.dark\:border-gray-800 {
        border-color: #1a1a1a !important;
    }

    /* Message backgrounds */
    .bg-gray-300.dark\:bg-gray-700 {
        background-color: #1a1a1a !important;
    }

    .bg-gray-300.dark\:bg-gray-800 {
        background-color: #1a1a1a !important;
    }

    /* Text colors - Light text for dark background */
    .text-gray-900.dark\:text-white {
        color: #ffffff !important;
    }

    .text-gray-600.dark\:text-gray-300 {
        color: #e5e5e5 !important;
    }

    .text-gray-700.dark\:text-gray-300 {
        color: #e5e5e5 !important;
    }

    .text-gray-400 {
        color: #a3a3a3 !important;
    }

    /* Day divider */
    .bg-gray-300.dark\:bg-gray-800 {
        background-color: #1a1a1a !important;
        border-color: #262626 !important;
    }

    .text-gray-600.dark\:text-gray-400 {
        color: #a3a3a3 !important;
    }

    /* Message time text */
    .text-\[10px\].opacity-80 {
        color: #737373 !important;
    }

    /* Input field */
    .bg-white.dark\:bg-gray-800 {
        background-color: #0a0a0a !important;
        color: #ffffff !important;
        border-color: #262626 !important;
    }

    .bg-white.dark\:bg-gray-800:focus {
        border-color: #6366f1 !important;
        background-color: #000000 !important;
    }

    /* Attachment button */
    .bg-white.dark\:bg-gray-800 {
        background-color: #1a1a1a !important;
    }
    
    .hover\:bg-gray-300.dark\:hover\:bg-gray-700:hover {
        background-color: #262626 !important;
    }

    /* File preview chips */
    .border-gray-300.dark\:border-gray-600 {
        border-color: #262626 !important;
    }

    .bg-gray-50.dark\:bg-gray-900 {
        background-color: #0a0a0a !important;
    }

    .hover\:bg-gray-300.dark\:hover\:bg-gray-800:hover {
        background-color: #1a1a1a !important;
    }

    /* My messages - Keep indigo colors */
    .bg-indigo-600 {
        background-color: rgb(79 70 229) !important;
    }

    /* Avatar backgrounds */
    .bg-gray-300.dark\:bg-gray-700 {
        background-color: #404040 !important;
        color: #ffffff !important;
    }

    /* Composer area */
    .bg-gray-50.dark\:bg-gray-900 {
        background-color: #000000 !important;
        border-top-color: #1a1a1a !important;
    }

    /* Message bubble borders for better separation */
    .bg-gray-300.dark\:bg-gray-800 {
        border: 1px solid #262626 !important;
    }

    .bg-indigo-600 {
        border: 1px solid #4f46e5 !important;
    }

    /* Attachment borders */
    .border-gray-300.dark\:border-gray-600 {
        border-color: #262626 !important;
    }

    .border-gray-300.dark\:border-gray-700 {
        border-color: #262626 !important;
    }

    /* Video background */
    .bg-gray-50.dark\:bg-gray-900 {
        background-color: #0a0a0a !important;
    }

    /* Override any light mode styles to force dark */
    .bg-white {
        background-color: #000000 !important;
    }

    .bg-gray-50 {
        background-color: #0a0a0a !important;
    }

    .bg-gray-300 {
        background-color: #1a1a1a !important;
    }

    .text-gray-900 {
        color: #ffffff !important;
    }

    .text-gray-600 {
        color: #e5e5e5 !important;
    }

    .text-gray-700 {
        color: #e5e5e5 !important;
    }

    .border-gray-300 {
        border-color: #1a1a1a !important;
    }

    .border-gray-200 {
        border-color: #1a1a1a !important;
    }

    /* Shadow adjustments for dark mode */
    .shadow-sm.dark\:shadow-none {
        box-shadow: none !important;
    }
</style>

<div class="h-full bg-black border border-gray-800 rounded-2xl overflow-hidden flex flex-col">

    
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800 bg-black">
        <div class="flex items-center gap-3">
            <?php $initials = strtoupper(substr($selected_client->name ?? 'U', 0, 1)); ?>
            <div class="w-9 h-9 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                <?php echo e($initials); ?>

            </div>
            <div class="text-white font-medium"><?php echo e($selected_client->name ?? 'Client'); ?></div>
        </div>
        <div class="flex items-center gap-1 text-gray-300">
            <button type="button" class="p-2 rounded-lg hover:bg-gray-800 transition-colors" title="Search in chat">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>

    
    <div id="desktopChatMessages" class="flex-1 min-h-0 flex flex-col p-3 overflow-y-auto bg-black">
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
                    <span class="px-3 py-1 text-xs rounded-full bg-gray-800 text-gray-400 border border-gray-700">
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
                <div class="mr-2 mt-0.5 shrink-0">
                    <div class="w-7 h-7 rounded-full grid place-items-center text-[11px] font-semibold <?php echo e($isMe ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-200'); ?>">
                        <?php echo e($roleLetter($msg)); ?>

                    </div>
                </div>

                <div class="max-w-[78%] md:max-w-[66%] px-3 py-2 rounded-2xl leading-snug <?php echo e($isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-800 text-gray-100 rounded-bl-none'); ?>">
                    <?php $text = trim((string)($msg->description ?? $msg->body ?? '')); ?>

                    <?php if($text !== ''): ?>
                        <div class="flex items-end justify-between gap-2">
                            <p class="whitespace-pre-line break-words flex-1 leading-snug"><?php echo e($text); ?></p>
                            <span class="text-[10px] opacity-80 shrink-0 ml-2 whitespace-nowrap self-end">
                                <?php echo e($msg->created_at->format('H:i')); ?>

                            </span>
                        </div>
                    <?php endif; ?>

                    
                    <?php
                        $files = collect($msg->attachments ?? ($msg->documents ?? []));
                    ?>

                    <?php if($files->count()): ?>
                        <div class="mt-2 grid grid-cols-2 gap-2">
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
                                            class="block overflow-hidden rounded-xl border border-gray-600 hover:opacity-95 transition-opacity">
                                            <img src="<?php echo e($url); ?>" alt="<?php echo e($name); ?>" loading="lazy"
                                                class="w-full h-28 object-cover">
                                        </a>
                                    <?php elseif($isVideo): ?>
                                        <div class="overflow-hidden rounded-xl border border-gray-600 bg-gray-800">
                                            <video src="<?php echo e($url); ?>" class="w-full h-28 object-cover"
                                                controls></video>
                                        </div>
                                    <?php else: ?>
                                        <a href="<?php echo e($url); ?>" target="_blank" rel="noopener"
                                            class="flex items-center gap-2 p-2 rounded-xl border border-gray-600 bg-gray-800 hover:bg-gray-700 transition-colors text-white">
                                            <i class="fa-regular fa-file-lines text-sm"></i>
                                            <span class="text-xs truncate max-w-[140px]"><?php echo e($name); ?></span>
                                            <?php if($sizeTxt): ?>
                                                <span class="text-[10px] opacity-70"><?php echo e($sizeTxt); ?></span>
                                            <?php endif; ?>
                                            <i class="fa-solid fa-arrow-down-to-line text-xs opacity-80 ml-auto"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <?php if($text === ''): ?>
                            <div class="text-[10px] mt-1 opacity-80 text-right text-gray-400">
                                <?php echo e($msg->created_at->format('H:i')); ?>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php
        $formId = 'managerChatForm-' . ($selected_client->id ?? 'x');
        $attachId = 'attach-' . ($selected_client->id ?? 'x');
        $sendButtonId = 'sendButton-' . ($selected_client->id ?? 'x');
    ?>

    <form id="<?php echo e($formId); ?>" action="<?php echo e(route('manager.messages.store')); ?>" method="POST"
        enctype="multipart/form-data" class="flex-shrink-0 border-t border-gray-800 bg-black">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="receiver_id" value="<?php echo e($selected_client->id); ?>" />

        <div class="px-3 pt-2">
            <div data-preview class="hidden mb-2 flex flex-wrap gap-2"></div>
        </div>

        <div class="px-3 pb-3 pt-1 flex items-end gap-2">
            <input id="<?php echo e($attachId); ?>" type="file" name="attachments[]" class="sr-only chat-file-input"
                multiple accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar,.csv,.ppt,.pptx">

            <label for="<?php echo e($attachId); ?>"
                class="shrink-0 p-2 rounded-xl bg-gray-800 border border-gray-700 hover:bg-gray-700 text-gray-400 cursor-pointer transition-colors"
                title="Attach files">
                <i class="fa-solid fa-paperclip"></i>
            </label>

            <textarea name="description" rows="1" placeholder="Type a message…"
                class="min-h-[44px] max-h-40 flex-1 resize-y rounded-xl px-3 py-2 bg-gray-900 text-white border border-gray-700 focus:outline-none focus:border-indigo-500 placeholder-gray-500"></textarea>

            <button type="submit" id="<?php echo e($sendButtonId); ?>"
                class="shrink-0 px-4 h-11 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium cursor-not-allowed transition-colors" disabled>
                Send
            </button>
        </div>
    </form>
</div>

<script>
    (function() {
        // autoscroll
        const scroll = document.getElementById('desktopChatMessages');
        if (scroll) scroll.scrollTop = scroll.scrollHeight;

        // Get form elements
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
            this.style.height = Math.min(this.scrollHeight, 160) + 'px';
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
                chip.className = 'group relative rounded-xl border border-gray-600 bg-gray-800 text-gray-200 overflow-hidden';
                chip.innerHTML = isImg ?
                    `<img src="${url}" class="w-28 h-20 object-cover block">
               <button type="button" data-i="${idx}"
                       class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center transition-opacity">
                 <i class="fa-solid fa-xmark text-xs"></i>
               </button>` :
                    `<div class="px-3 py-2 flex items-center gap-2">
                 <i class="fa-regular fa-file-lines"></i>
                 <span class="max-w-[180px] truncate text-sm">${f.name}</span>
               </div>
               <button type="button" data-i="${idx}"
                       class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center transition-opacity">
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

        // Reset form and disable button after sending
        form.addEventListener('submit', function() {
            // Clear form and disable button after a short delay
            setTimeout(() => {
                textarea.value = '';
                textarea.style.height = 'auto';
                preview.innerHTML = '';
                preview.classList.add('hidden');
                updateSendButton();

                // Clear file input
                input.value = '';

                // Scroll to bottom after sending
                if (scroll) {
                    scroll.scrollTop = scroll.scrollHeight;
                }
            }, 100);
        });

        // cleanup
        window.addEventListener('beforeunload', () => objectUrls.forEach(u => URL.revokeObjectURL(u)));
    })();
</script><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\messages\chat_content.blade.php ENDPATH**/ ?>