<style>
    /* Your existing styles remain the same */
    #chatScroll {
        min-height: 0 !important;
        overflow-y: auto !important;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .chat-container {
        height: 100% !important;
        min-height: 0 !important;
        display: flex;
        flex-direction: column;
    }

    /* button[type="submit"]:disabled {
        cursor: not-allowed !important;
        opacity: 0.7 !important;
    } */

    .leading-snug {
        line-height: normal;
    }

    /* Enhanced Light Mode for chat */
    .chat-container {
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }

    #chatScroll {
        background-color: #ffffff !important;
    }

    /* Improved message bubbles for both modes */
    .bg-indigo-600 {
        background-color: #4f46e5 !important;
    }

    .bg-indigo-400 {
        background-color: #818cf8 !important;
    }

    .hover\:bg-indigo-700:hover {
        background-color: #4338ca !important;
    }

    /* Enhanced Dark Mode for chat */
    .dark .chat-container {
        background-color: #000000 !important;
        border-color: #374151 !important;
    }

    .dark .bg-indigo-600 {
        background-color: #3730a3 !important;
    }

    .dark .bg-indigo-400 {
        background-color: #4f46e5 !important;
    }

    .dark .hover\:bg-indigo-700:hover {
        background-color: #312e81 !important;
    }

    /* Support staff message styling - Single line design */
    .bg-gray-300 {
        background-color: #d1d5db !important;
        color: #374151 !important;
    }

    .dark .bg-gray-300 {
        background-color: #4b5563 !important;
        color: #f3f4f6 !important;
    }

    /* Single line message design */
    .single-line-message {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        max-width: 100%;
    }

    .message-content {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .message-text {
        flex: 1;
        min-width: 0;
    }

    .message-time {
        flex-shrink: 0;
        white-space: nowrap;
    }

    .bg-gray-900\/60 {
        background-color: rgba(17, 24, 39, 0.8) !important;
    }

    .dark .bg-gray-900\/60 {
        background-color: rgba(18, 18, 18, 0.9) !important;
    }

    .text-gray-300 {
        color: #d1d5db !important;
    }

    .dark .text-gray-300 {
        color: #d1d5db !important;
    }

    .text-gray-400 {
        color: #9ca3af !important;
    }

    .dark .text-gray-400 {
        color: #9ca3af !important;
    }

    .border-gray-800 {
        border-color: #1f2937 !important;
    }

    .dark .border-gray-800 {
        border-color: #374151 !important;
    }

    .py-3 {
        padding-top: 0.3rem;
        padding-bottom: 0.3rem;
    }

    .dark #chatScroll {
        background-color: #000000 !important;
    }

    /* Support staff selector */
    .staff-selector {
        max-height: 200px;
        overflow-y: auto;
    }
</style>

<div class="h-full bg-white border border-gray-200 rounded-2xl overflow-hidden flex flex-col chat-container dark:bg-gray-900 dark:border-gray-700">

    {{-- Header with Support Staff Selector --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
        <div class="flex items-center gap-3">
            <div class="font-semibold leading-tight">
                <img src="{{ asset('assets/images/light-logo.svg') }}" alt="Balantro"
                    class="h-8 block dark:hidden logo-full">
                <img src="{{ asset('assets/images/dark-logo.svg') }}" alt="Balantro"
                    class="h-8 hidden dark:block logo-full">
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Support Team Chat
            </div>
        </div>
        <!-- <div class="flex items-center gap-2">
            {{-- Support Staff Selector Dropdown --}}
            @if(isset($supportStaffUsers) && $supportStaffUsers->count() > 1)
            <div class="relative">
                <button type="button" id="staffDropdownButton" 
                    class="flex items-center gap-2 px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                    <span>To: {{ $selected_user->name ?? 'Select' }}</span>
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </button>
                <div id="staffDropdown" 
                    class="absolute top-full right-0 mt-1 w-48 bg-white border border-gray-300 rounded-lg shadow-lg z-10 hidden staff-selector dark:bg-gray-700 dark:border-gray-600">
                    @foreach($supportStaffUsers as $staff)
                    <button type="button" class="staff-option w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-300"
                            data-staff-id="{{ $staff->id }}" 
                            data-staff-name="{{ $staff->name }}">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            {{ $staff->name }} ({{ ucfirst(str_replace('_', ' ', $staff->type)) }})
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
            
            <button type="button" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600" title="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div> -->
    </div>

    {{-- Messages Container --}}
    <div id="chatScroll" class="flex flex-col p-4 overflow-y-auto flex-1 min-h-0 bg-white dark:bg-gray-900">
        @php
            $prevDay = null;

            // All support staff show "B" badge
            $roleLetter = function ($msg) {
                if (isset($msg->sender_type)) {
                    $t = strtolower($msg->sender_type);
                    return match ($t) {
                        'client', 'customer' => 'C',
                        'data_entry_operator', 'manager', 'supervisor' => 'B',
                        default => 'U',
                    };
                }
                
                // Fallback: if sender_id is not the current user, it's support staff (B)
                if ((int) $msg->sender_id !== (int) auth()->id()) {
                    return 'B';
                }
                
                return 'C';
            };

            // Get badge color - All support staff get the same color
            $badgeColor = function ($msg, $isMe) {
                if ($isMe) return 'bg-indigo-600 text-white';
                return 'bg-gray-600 text-white'; // All support staff get gray badge
            };

            // Get bubble color - All support staff get the same bubble style
            $bubbleColor = function ($msg, $isMe) {
                if ($isMe) return 'bg-indigo-600 text-white rounded-br-md';
                return 'bg-gray-300 text-gray-800 rounded-bl-md dark:bg-gray-600 dark:text-gray-100'; // All support staff get same bubble
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
        @endphp

        @foreach ($messages as $msg)
            @php
                $isMe = (int) $msg->sender_id === (int) auth()->id();
                $dayKey = \Carbon\Carbon::parse($msg->created_at)->toDateString();
                $body = $msg->body ?? ($msg->description ?? '');
                $atts = $msg->attachments ?? [];
                $attCount = is_countable($atts) ? count($atts) : 0;
            @endphp

            {{-- Date chip --}}
            @if ($dayKey !== $prevDay)
                <div class="my-4 flex justify-center">
                    <span
                        class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600 border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        {{ \Carbon\Carbon::parse($msg->created_at)->format('D, d M Y') }}
                    </span>
                </div>
                @php $prevDay = $dayKey; @endphp
            @endif

            {{-- Message Row --}}
            <div class="mt-2 flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                {{-- Role badge (always left of bubble) --}}
                @if(!$isMe)
                <div class="mr-2 mt-0.5 shrink-0">
                    <div class="w-7 h-7 rounded-full grid place-items-center text-[11px] font-semibold {{ $badgeColor($msg, $isMe) }}">
                        {{ $roleLetter($msg) }}
                    </div>
                </div>
                @endif

                {{-- Message Bubble --}}
                <div class="max-w-[78%] md:max-w-[66%] px-4 py-3 rounded-2xl leading-snug {{ $bubbleColor($msg, $isMe) }}">

                    {{-- Single line message design for both sender and receiver --}}
                    <div class="single-line-message">
                        <div class="message-content">
                            @if (!empty($body))
                            <div class="message-text">
                                <p class="break-words leading-relaxed">
                                    {{ $body }}
                                </p>
                            </div>
                            @endif
                            
                            <div class="message-time">
                                <span class="text-[10px] opacity-70 whitespace-nowrap 
                                    @if($isMe) text-indigo-100 
                                    @else text-gray-600 dark:text-gray-400 
                                    @endif">
                                    {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Attachments (chat-style) --}}
                    @if ($attCount)
                        @php
                            $imageOnly = true;
                            $attData = [];
                            foreach ($atts as $a) {
                                $url = $a->url ?? '#';
                                $name = $a->name ?? basename(parse_url($url, PHP_URL_PATH) ?? 'file');
                                $mime = strtolower($a->mime ?? '');
                                $size = $a->size ?? null;
                                $isImg = str_starts_with($mime, 'image/') || preg_match('/\.(png|jpe?g|gif|webp|bmp|heic|heif)$/i', $name);
                                $isVid = str_starts_with($mime, 'video/') || preg_match('/\.(mp4|webm|mov|m4v)$/i', $name);
                                $imageOnly = $imageOnly && $isImg;
                                $attData[] = compact('url', 'name', 'mime', 'size', 'isImg', 'isVid');
                            }
                            $singleBigImage = $attCount === 1 && $imageOnly;
                        @endphp

                        {{-- Attachments Grid --}}
                        <div class="{{ $singleBigImage ? 'mt-2' : 'mt-3 grid grid-cols-2 gap-2' }}">
                            @foreach ($attData as $a)
                                @if ($a['isImg'])
                                    <a href="{{ $a['url'] }}" target="_blank"
                                        class="block overflow-hidden rounded-lg border border-gray-300 hover:opacity-90 dark:border-gray-600">
                                        <img src="{{ $a['url'] }}" alt="{{ $a['name'] }}" loading="lazy"
                                            class="w-full {{ $singleBigImage ? 'max-h-[300px]' : 'h-28' }} object-cover">
                                    </a>
                                @elseif ($a['isVid'])
                                    <div class="overflow-hidden rounded-lg border border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800">
                                        <video src="{{ $a['url'] }}" preload="metadata"
                                            class="w-full {{ $singleBigImage ? 'max-h-[300px]' : 'h-28' }} object-cover"
                                            controls></video>
                                    </div>
                                @else
                                    <a href="{{ $a['url'] }}" target="_blank"
                                        class="flex items-center gap-2 p-3 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700">
                                        <i class="fa-regular fa-file-lines text-gray-600 dark:text-gray-400"></i>
                                        <span class="text-sm truncate max-w-[140px] text-gray-700 dark:text-gray-300">{{ $a['name'] }}</span>
                                        @if ($a['size'])
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $fmtSize($a['size']) }}</span>
                                        @endif
                                        <i class="fa-solid fa-arrow-down-to-line text-xs text-gray-500 ml-auto dark:text-gray-400"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        {{-- Timestamp below attachments --}}
                        <div class="text-[10px] opacity-70 text-right mt-2 
                            @if($isMe) text-indigo-100 
                            @else text-gray-600 dark:text-gray-400 
                            @endif">
                            {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}
                        </div>
                    @endif
                </div>

                {{-- Role badge for sender (on the right) --}}
                @if($isMe)
                <div class="ml-2 mt-0.5 shrink-0">
                    <div class="w-7 h-7 rounded-full grid place-items-center text-[11px] font-semibold {{ $badgeColor($msg, $isMe) }}">
                        {{ $roleLetter($msg) }}
                    </div>
                </div>
                @endif
            </div>
        @endforeach

        {{-- Scroll anchor --}}
        <div id="chatBottom"></div>
    </div>

    {{-- Composer --}}
    <form id="clientChatForm" action="{{ route('client.messages.store') }}" method="POST"
        enctype="multipart/form-data" class="border-t border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
        @csrf
        <input type="hidden" name="to_user_id" id="to_user_id" value="{{ $selected_user->id ?? '' }}">

        @php
            $attachId = 'client-attach-' . ($selected_user->id ?? 'x');
        @endphp

        <div class="px-4 pt-3">
            <div data-preview class="hidden mb-3 flex flex-wrap gap-2"></div>
        </div>

        <div class="px-4 pb-4 pt-1 flex items-end gap-3">
            {{-- Selected staff indicator --}}
            <div class="text-xs text-gray-500 dark:text-gray-400 mr-2 hidden" id="selectedStaffIndicator">
                To: <span id="selectedStaffName">{{ $selected_user->name ?? '' }}</span>
            </div>

            <input id="{{ $attachId }}" type="file" name="files[]" class="sr-only chat-file-input" multiple
                accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">

            <label for="{{ $attachId }}"
                class="shrink-0 p-3 rounded-xl bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 cursor-pointer dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-600"
                title="Attach files">
                <i class="fa-solid fa-paperclip"></i>
            </label>

            <textarea name="body" rows="1" placeholder="Type a message…"
                class="min-h-[48px] max-h-40 flex-1 resize-y rounded-xl px-4 py-3
                       bg-white text-gray-900 border border-gray-300
                       focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200
                       dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300
                       dark:focus:border-indigo-400 dark:focus:ring-indigo-900
                       placeholder-gray-500 dark:placeholder-gray-400"></textarea>

            <button type="submit" id="sendButton"
                class="shrink-0 px-5 h-12 rounded-xl bg-indigo-600 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:bg-indigo-400 disabled:cursor-not-allowed dark:bg-indigo-600 dark:hover:bg-indigo-700">
                Send
            </button>
        </div>
    </form>
</div>

<script>
    (function() {
        let autoScrollEnabled = true;
        let userScrolledUp = false;

        function initializeChat() {
            const scroll = document.getElementById('chatScroll');
            const bottom = document.getElementById('chatBottom');

            if (!scroll) return;

            // Initial scroll to bottom
            //scrollToBottom();
            const chatScroll = document.getElementById('chatScroll');

            if (chatScroll) {
                chatScroll.scrollTop = chatScroll.scrollHeight;
            }

            // Track user scroll behavior
            scroll.addEventListener('scroll', () => {
                const isAtBottom = Math.abs(scroll.scrollHeight - scroll.clientHeight - scroll.scrollTop) < 10;

                if (!isAtBottom) {
                    userScrolledUp = true;
                    autoScrollEnabled = false;
                }

                if (isAtBottom) {
                    userScrolledUp = false;
                    autoScrollEnabled = true;
                }
            });

            // Auto-scroll when new messages arrive
            const observer = new MutationObserver(() => {
                if (autoScrollEnabled && !userScrolledUp) {
                    setTimeout(scrollToBottom, 100);
                }
            });

            observer.observe(scroll, {
                childList: true,
                subtree: true
            });

            initializeFileAttachments();
            initializeStaffSelector();
        }

        function scrollToBottom() {
            const scroll = document.getElementById('chatScroll');
            if (scroll) {
                scroll.scrollTop = scroll.scrollHeight;
            }
        }

        // Staff selector functionality
        function initializeStaffSelector() {
            const dropdownButton = document.getElementById('staffDropdownButton');
            const dropdown = document.getElementById('staffDropdown');
            const staffOptions = document.querySelectorAll('.staff-option');
            const toUserIdInput = document.getElementById('to_user_id');
            const selectedStaffName = document.getElementById('selectedStaffName');
            const selectedStaffIndicator = document.getElementById('selectedStaffIndicator');

            if (!dropdownButton || !dropdown) return;

            // Toggle dropdown
            dropdownButton.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            // Select staff member
            staffOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const staffId = this.dataset.staffId;
                    const staffName = this.dataset.staffName;
                    
                    toUserIdInput.value = staffId;
                    selectedStaffName.textContent = staffName;
                    selectedStaffIndicator.classList.remove('hidden');
                    
                    // Update dropdown button text
                    dropdownButton.querySelector('span').textContent = 'To: ' + staffName;
                    
                    dropdown.classList.add('hidden');
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                dropdown.classList.add('hidden');
            });
        }

        // File attachment functionality
        function initializeFileAttachments() {
            const form = document.getElementById('clientChatForm');
            const attachId = 'client-attach-' + ('{{ $selected_user->id ?? 'x' }}');
            const input = document.getElementById(attachId);
            const preview = form ? form.querySelector('[data-preview]') : null;
            const textarea = form ? form.querySelector('textarea[name="body"]') : null;
            const submitBtn = document.getElementById('sendButton');

            if (!form || !input || !preview || !submitBtn) return;

            const objectUrls = [];

            function updateSubmitButton() {
                const hasText = textarea && textarea.value.trim().length > 0;
                const hasFiles = input.files && input.files.length > 0;
                const shouldEnable = hasText || hasFiles;
                // submitBtn.disabled = !shouldEnable;
                submitBtn.disabled = false;
            }

            if (textarea) {
                textarea.addEventListener('input', updateSubmitButton);
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 160) + 'px';
                });
            }

            input.addEventListener('change', () => {
                objectUrls.splice(0).forEach(u => URL.revokeObjectURL(u));
                preview.innerHTML = '';

                const files = Array.from(input.files || []);
                if (!files.length) {
                    preview.classList.add('hidden');
                    updateSubmitButton();
                    return;
                }

                preview.classList.remove('hidden');

                files.forEach((f, idx) => {
                    const isImg = f.type && f.type.startsWith('image/');
                    const isVid = f.type && f.type.startsWith('video/');
                    const url = (isImg || isVid) ? URL.createObjectURL(f) : '';
                    if (url) objectUrls.push(url);

                    const chip = document.createElement('div');
                    chip.className = 'group relative rounded-lg border border-gray-300 bg-white overflow-hidden dark:border-gray-600 dark:bg-gray-700';

                    if (isImg) {
                        chip.innerHTML = `<img src="${url}" class="w-28 h-20 object-cover block" alt=""><button type="button" data-i="${idx}" class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-gray-600 text-white hidden group-hover:grid place-items-center dark:bg-gray-500"><i class="fa-solid fa-xmark text-xs"></i></button>`;
                    } else if (isVid) {
                        chip.innerHTML = `<video src="${url}" class="w-28 h-20 object-cover block" muted playsinline></video><button type="button" data-i="${idx}" class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-gray-600 text-white hidden group-hover:grid place-items-center dark:bg-gray-500"><i class="fa-solid fa-xmark text-xs"></i></button>`;
                    } else {
                        chip.innerHTML = `<div class="px-3 py-2 flex items-center gap-2 text-gray-700 dark:text-gray-300"><i class="fa-regular fa-file-lines"></i><span class="max-w-[160px] truncate text-sm">${f.name}</span></div><button type="button" data-i="${idx}" class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-gray-600 text-white hidden group-hover:grid place-items-center dark:bg-gray-500"><i class="fa-solid fa-xmark text-xs"></i></button>`;
                    }

                    preview.appendChild(chip);
                });

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

                updateSubmitButton();
            });

            // form.addEventListener('submit', function(e) {
            //     autoScrollEnabled = true;
            //     userScrolledUp = false;

            //     setTimeout(() => {
            //         if (textarea) {
            //             textarea.value = '';
            //             textarea.style.height = 'auto';
            //         }
            //         preview.innerHTML = '';
            //         preview.classList.add('hidden');
            //         updateSubmitButton();

            //         setTimeout(scrollToBottom, 300);
            //     }, 0);
            // });

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const bodyInput = textarea;
                const body = bodyInput.value.trim();
                const files = input.files;
                if (!body && !files.length) {
                    return;
                }
                // ✅ Disable only button click spam
                submitBtn.disabled = true;
                // ✅ FormData
                const formData = new FormData(form);
                // ✅ Optimistic UI (instant message)
                //appendOwnMessage(body);
                appendOwnMessage(body, files);
                // ✅ Clear immediately
                bodyInput.value = '';
                bodyInput.style.height = 'auto';

                preview.innerHTML = '';
                preview.classList.add('hidden');

                input.value = '';
                updateSubmitButton();
                //scrollToBottom();
                const chatScroll = document.getElementById('chatScroll');

                if (chatScroll) {
                    chatScroll.scrollTop = chatScroll.scrollHeight;
                }
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    const data = await response.json();
                    if (!data.success) {
                        console.error(data);
                    }
                } catch (err) {
                    console.error(err);
                } finally {
                    submitBtn.disabled = false;
                }
            });
            window.addEventListener('beforeunload', () => {
                objectUrls.forEach(u => URL.revokeObjectURL(u));
            });
        }

        // Initialize when ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeChat);
        } else {
            initializeChat();
        }
    })();

    function appendOwnMessage(message, files = []) {
        const scroll = document.getElementById('chatScroll');
        const wrapper = document.createElement('div');
        wrapper.className = 'mt-2 flex justify-end';
        let attachmentsHtml = '';
        if (files.length > 0) {
            attachmentsHtml += `
                <div class="mt-3 grid grid-cols-2 gap-2">
            `;
            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const url = URL.createObjectURL(file);
                    attachmentsHtml += `
                        <div class="overflow-hidden rounded-lg border border-gray-300 dark:border-gray-600">
                            <img src="${url}"
                                class="w-full h-28 object-cover">
                        </div>
                    `;
                } else {
                    attachmentsHtml += `
                        <div class="flex items-center gap-2 p-3 rounded-lg
                                    border border-gray-300 bg-white
                                    dark:border-gray-600 dark:bg-gray-800">
                            <i class="fa-regular fa-file-lines"></i>
                            <span class="text-sm truncate">
                                ${escapeHtml(file.name)}
                            </span>
                        </div>
                    `;
                }
            });
            attachmentsHtml += `</div>`;
        }
        wrapper.innerHTML = `
            <div class="max-w-[78%] md:max-w-[66%]
                        px-4 py-3 rounded-2xl
                        leading-snug
                        bg-indigo-600 text-white rounded-br-md">
                <div class="single-line-message">
                    <div class="message-content">
                        ${
                            message
                            ?
                            `
                            <div class="message-text">
                                <p class="break-words leading-relaxed">
                                    ${escapeHtml(message)}
                                </p>
                            </div>
                            `
                            :
                            ''
                        }
                        <div class="message-time">
                            <span class="text-[10px]
                                        opacity-70
                                        whitespace-nowrap
                                        text-indigo-100">
                                ${new Date().toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                            </span>
                        </div>
                    </div>
                </div>
                ${attachmentsHtml}
            </div>
            <div class="ml-2 mt-0.5 shrink-0">
                <div class="w-7 h-7 rounded-full
                            grid place-items-center
                            text-[11px]
                            font-semibold
                            bg-indigo-600 text-white">
                    C
                </div>
            </div>
        `;
        scroll.insertBefore(
            wrapper,
            document.getElementById('chatBottom')
        );
        //scrollToBottom();
        const chatScroll = document.getElementById('chatScroll');

        if (chatScroll) {
            chatScroll.scrollTop = chatScroll.scrollHeight;
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }
</script>