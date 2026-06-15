{{-- resources/views/managers/messages/chat_content.blade.php --}}
{{-- expects: $messages (Collection), $clientName (string|null), $selectedClientId (int) --}}
<style>
    /* Disabled button styles */
    button[type="submit"]:disabled {
        cursor: not-allowed !important;
        opacity: 0.7 !important;
        /* background-color: rgb(79 70 229) !important; */
        /* indigo-600 with lower opacity */
    }

    /* button[type="submit"]:not(:disabled) {
        background-color: rgb(79 70 229) !important;

    }

    button[type="submit"]:not(:disabled):hover {
        background-color: rgb(67 56 202) !important;

    }

    */
</style>
<div class="h-full bg-gray-900/40 border border-gray-800 rounded-2xl overflow-hidden flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800 bg-gray-900/60">
        <div class="flex items-center gap-3">
            @php $title = $clientName ?? 'Conversation'; @endphp
            <div class="w-9 h-9 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                {{ strtoupper(mb_substr($title, 0, 1)) }}
            </div>
            <div class="text-xs text-gray-400">{{ $title }}</div>
        </div>
        <div class="flex items-center gap-1 text-gray-300">
            <button type="button" class="p-2 rounded-lg hover:bg-gray-800" title="Search in chat">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>

    {{-- Messages area (only scroller) --}}
    <div id="chatScroll" class="flex-1 min-h-0 flex flex-col p-3 overflow-y-auto">
        @php
            $prevDay = null;

            $roleLetter = function ($msg) use ($selectedClientId) {
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
                    $role = optional(auth()->user())->type;
                    return $role ? strtoupper(substr($role, 0, 1)) : 'M';
                }
                if ($selectedClientId && (int) $msg->sender_id === (int) $selectedClientId) {
                    return 'C';
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
        @endphp

        @foreach ($messages as $msg)
            @php
                $isMe = (int) $msg->sender_id === (int) auth()->id();
                $dayKey = $msg->created_at->toDateString();
            @endphp

            {{-- Day divider --}}
            @if ($dayKey !== $prevDay)
                <div class="my-2 flex justify-center">
                    <span class="px-3 py-1 text-xs rounded-full bg-gray-800 text-gray-300 border border-gray-700">
                        {{ $msg->created_at->format('D, d M Y') }}
                    </span>
                </div>
            @endif

            {{-- Message row --}}
            <div class="mt-1 flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                <div class="mr-2 mt-0.5 shrink-0">
                    <div
                        class="w-7 h-7 rounded-full grid place-items-center text-[11px] font-semibold
                                {{ $isMe ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-100' }}">
                        {{ $roleLetter($msg) }}
                    </div>
                </div>

                <div
                    class="max-w-[78%] md:max-w-[66%] px-3 py-2 rounded-2xl leading-snug
                           {{ $isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-700 text-gray-100 rounded-bl-none' }}">
                    @php $text = trim((string)($msg->description ?? $msg->body ?? '')); @endphp
                    @if ($text !== '')
                        <p class="whitespace-pre-line break-words">{{ $text }}</p>
                    @endif

                    {{-- Attachments (robust field names) --}}
                    @php
                        $files = collect($msg->attachments ?? ($msg->documents ?? []));
                    @endphp

                    @if ($files->count())
                        <div class="mt-2 grid grid-cols-2 gap-2">

                            @foreach ($files as $att)
                                @php
                                    // Resolve URL (url | file_url | path | stored_path)
                                    $url =
                                        data_get($att, 'url') ??
                                        (data_get($att, 'file_url') ??
                                            ((data_get($att, 'path')
                                                ? \Illuminate\Support\Facades\Storage::url(data_get($att, 'path'))
                                                : null) ??
                                                (data_get($att, 'stored_path')
                                                    ? \Illuminate\Support\Facades\Storage::url(
                                                        data_get($att, 'stored_path'),
                                                    )
                                                    : null)));

                                    // Resolve name
                                    $name =
                                        data_get($att, 'original_name') ??
                                        (data_get($att, 'name') ??
                                            ($url ? basename(parse_url($url, PHP_URL_PATH)) : 'file'));

                                    // Resolve mime / type / ext
                                    $mime = strtolower(data_get($att, 'mime_type') ?? (data_get($att, 'mime') ?? ''));
                                    $ext = strtolower(
                                        pathinfo(parse_url($url ?? '', PHP_URL_PATH) ?? '', PATHINFO_EXTENSION),
                                    );
                                    $isImg = $mime
                                        ? str_starts_with($mime, 'image/')
                                        : in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                                    $isVideo = $mime
                                        ? str_starts_with($mime, 'video/')
                                        : in_array($ext, ['mp4', 'webm', 'mov', 'm4v']);
                                    $sizeTxt = $fmtSize(data_get($att, 'size'));
                                @endphp

                                @if ($url)
                                    @if ($isImg)
                                        <a href="{{ $url }}" target="_blank" rel="noopener"
                                            class="block overflow-hidden rounded-xl border border-white/10 hover:opacity-95">
                                            <img src="{{ $url }}" alt="{{ $name }}" loading="lazy"
                                                class="w-full h-28 object-cover">
                                        </a>
                                    @elseif ($isVideo)
                                        <div class="overflow-hidden rounded-xl border border-white/10 bg-black/10">
                                            <video src="{{ $url }}" class="w-full h-28 object-cover"
                                                controls></video>
                                        </div>
                                    @else
                                        <a href="{{ $url }}" target="_blank" rel="noopener"
                                            class="flex items-center gap-2 p-2 rounded-xl border border-white/10 bg-black/10 hover:bg-black/20">
                                            <i class="fa-regular fa-file-lines text-sm"></i>
                                            <span class="text-xs truncate max-w-[140px]">{{ $name }}</span>
                                            @if ($sizeTxt)
                                                <span class="text-[10px] opacity-70">{{ $sizeTxt }}</span>
                                            @endif
                                            <i class="fa-solid fa-arrow-down-to-line text-xs opacity-80 ml-auto"></i>
                                        </a>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="text-[10px] mt-1.5 opacity-80 text-right">
                        {{ $msg->created_at->format('H:i') }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Composer --}}
    <form id="managerChatForm" action="{{ route('manager.messages.store') }}" method="POST"
        enctype="multipart/form-data" class="border-t border-gray-800 bg-gray-900/60">
        @csrf
        <input type="hidden" name="receiver_id" value="{{ (int) $selectedClientId }}" />

        @php
            // stable, unique DOM ids per chat
            $attachId = 'attach-' . ($selectedClientId ?? 'x');
        @endphp

        <div class="px-3 pt-2">
            <div data-preview class="hidden mb-2 flex flex-wrap gap-2"></div>
        </div>

        <div class="px-3 pb-3 pt-1 flex items-end gap-2">
            {{-- IMPORTANT: use sr-only, not hidden --}}
            <input id="{{ $attachId }}" type="file" name="attachments[]" class="sr-only chat-file-input"
                multiple accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar,.csv,.ppt,.pptx">

            {{-- Native label triggers the file dialog (no JS needed) --}}
            <label for="{{ $attachId }}"
                class="shrink-0 p-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 cursor-pointer"
                title="Attach files">
                <i class="fa-solid fa-paperclip"></i>
            </label>

            <textarea name="description" rows="1" placeholder="Type a message…"
                class="min-h-[44px] max-h-40 flex-1 resize-y rounded-xl px-3 py-2 bg-gray-900 text-white border border-gray-700 focus:outline-none focus:border-indigo-500"></textarea>

            <button type="submit" id="sendButton"
                class="shrink-0 px-4 h-11 rounded-xl bg-indigo-400 text-white font-medium cursor-not-allowed" disabled>
                Send
            </button>
        </div>
    </form>
</div>

<script>
    (function() {
        // autoscroll
        const scroll = document.getElementById('chatScroll');
        if (scroll) scroll.scrollTop = scroll.scrollHeight;

        // Get form elements
        const form = document.getElementById('managerChatForm');
        const input = document.getElementById('{{ $attachId }}');
        const preview = form ? form.querySelector('[data-preview]') : null;
        const textarea = form ? form.querySelector('textarea[name="description"]') : null;
        const sendButton = document.getElementById('sendButton');

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
                sendButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
            } else {
                sendButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
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

        // File input change handler
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
                chip.className =
                    'group relative rounded-xl border border-gray-700 bg-gray-900 text-gray-200 overflow-hidden';
                chip.innerHTML = isImg ?
                    `<img src="${url}" class="w-28 h-20 object-cover block">
               <button type="button" data-i="${idx}"
                       class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center">
                 <i class="fa-solid fa-xmark text-xs"></i>
               </button>` :
                    `<div class="px-3 py-2 flex items-center gap-2">
                 <i class="fa-regular fa-file-lines"></i>
                 <span class="max-w-[180px] truncate text-sm">${f.name}</span>
               </div>
               <button type="button" data-i="${idx}"
                       class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center">
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
</script>
