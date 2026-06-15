{{-- resources/views/data_entry_operators/messages/chat_content.blade.php --}}
{{-- expects: $selected_client, $messages (Collection) --}}
<div
    class="h-full bg-black border border-gray-800 rounded-2xl overflow-hidden flex flex-col">

    {{-- Header --}}
    <div
        class="flex items-center justify-between px-4 py-3 border-b border-gray-800 bg-gray-900">
        <div class="flex items-center gap-3">
            @php $initials = strtoupper(substr($selected_client->name ?? 'U', 0, 2)); @endphp
            <div class="w-9 h-9 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                {{ $initials }}
            </div>
            <div class="text-white font-medium">{{ $selected_client->name ?? 'Unknown' }}</div>
        </div>
        <div class="flex items-center gap-1 text-gray-400">
            <button type="button" class="p-2 rounded-lg hover:bg-gray-800" title="Search in chat">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>

    {{-- Messages area --}}
    <div id="chatScroll" class="flex-1 min-h-0 flex flex-col p-3 overflow-y-auto bg-black">
        @php
            $prevDay = null;
            $roleLetter = function ($msg) use ($selected_client) {
                if (isset($msg->sender) && !empty($msg->sender->type)) {
                    return match (strtolower($msg->sender->type)) {
                        'Client', 'customer' => 'C',
                        'data_entry_operator', 'DataEntryOperator' => 'D',
                        'manager', 'Manager' => 'M',
                        'supervisor', 'Supervisor' => 'S',
                        default => strtoupper(substr($msg->sender->type, 0, 1)),
                    };
                }

                if (auth()->check() && $msg->sender_id == auth()->id()) {
                    $role = optional(auth()->user())->type;
                    return $role ? strtoupper(substr($role, 0, 1)) : 'D';
                }

                if (!empty($selected_client?->user_id) && $msg->sender_id == $selected_client->user_id) {
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
                $ts =
                    $msg->created_at instanceof \Carbon\Carbon
                        ? $msg->created_at
                        : \Carbon\Carbon::parse($msg->created_at);
                $isMe = $msg->sender_id == auth()->id();
                $dayKey = $ts->toDateString();
            @endphp

            {{-- Day divider --}}
            @if ($dayKey !== $prevDay)
                <div class="my-2 flex justify-center">
                    <span
                        class="px-3 py-1 text-xs rounded-full bg-gray-900 text-gray-400 border border-gray-800">
                        @if ($dayKey === now()->toDateString())
                            Today
                        @elseif ($dayKey === now()->subDay()->toDateString())
                            Yesterday
                        @else
                            {{ $ts->format('D, d M Y') }}
                        @endif
                    </span>
                </div>
                @php $prevDay = $dayKey; @endphp
            @endif

            {{-- Message row --}}
            <div class="mt-1 flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                <div class="mr-2 mt-0.5 shrink-0">
                    <div
                        class="w-7 h-7 rounded-full grid place-items-center text-[11px] font-semibold {{ $isMe ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-300' }}">
                        {{ $roleLetter($msg) }}
                    </div>
                </div>

                <div
                    class="max-w-[78%] md:max-w-[66%] px-3 py-2 rounded-2xl leading-snug {{ $isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-900 text-gray-100 rounded-bl-none' }}">
                    @php $text = trim((string)($msg->description ?? $msg->body ?? '')); @endphp

                    @if ($text !== '')
                        <div class="flex items-end justify-between gap-2">
                            <p class="whitespace-pre-line break-words flex-1 leading-snug">{{ $text }}</p>
                            <span class="text-[10px] opacity-80 shrink-0 ml-2 whitespace-nowrap self-end">
                                {{ $ts->format('H:i') }}
                            </span>
                        </div>
                    @endif

                    {{-- Attachments --}}
                    @php
                        $files = collect($msg->attachments ?? []);
                        if ($files->isEmpty() && isset($msg->documents)) {
                            $files = collect($msg->documents)->map(function ($d) {
                                return (object) [
                                    'url' =>
                                        $d->url ??
                                        ($d->file_path
                                            ? \Illuminate\Support\Facades\Storage::url($d->file_path)
                                            : null),
                                    'name' => $d->original_name ?? ($d->file_name ?? 'file'),
                                    'mime' => $d->mime ?? ($d->mime_type ?? ''),
                                    'size' => (int) ($d->size ?? 0),
                                ];
                            });
                        }
                    @endphp

                    @if ($files->count())
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach ($files as $att)
                                @php
                                    $url = $att->url ?? '#';
                                    $name = $att->name ?? 'file';
                                    $mime = strtolower($att->mime ?? '');
                                    $isImg = $mime
                                        ? str_starts_with($mime, 'image/')
                                        : in_array(pathinfo($name, PATHINFO_EXTENSION), [
                                            'jpg',
                                            'jpeg',
                                            'png',
                                            'gif',
                                            'webp',
                                            'bmp',
                                            'svg',
                                        ]);
                                    $isVideo = $mime
                                        ? str_starts_with($mime, 'video/')
                                        : in_array(pathinfo($name, PATHINFO_EXTENSION), ['mp4', 'webm', 'mov', 'm4v']);
                                    $sizeTxt = $fmtSize($att->size ?? 0);
                                @endphp

                                @if ($url && $url !== '#')
                                    @if ($isImg)
                                        <a href="{{ $url }}" target="_blank" rel="noopener"
                                            class="block overflow-hidden rounded-xl border border-gray-800 hover:opacity-95">
                                            <img src="{{ $url }}" alt="{{ $name }}" loading="lazy"
                                                class="w-full h-28 object-cover">
                                        </a>
                                    @elseif ($isVideo)
                                        <div
                                            class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900">
                                            <video src="{{ $url }}" class="w-full h-28 object-cover"
                                                controls></video>
                                        </div>
                                    @else
                                        <a href="{{ $url }}" target="_blank" rel="noopener"
                                            class="flex items-center gap-2 p-2 rounded-xl border border-gray-800 bg-gray-900 hover:bg-gray-800">
                                            <i
                                                class="fa-regular fa-file-lines text-sm text-gray-400"></i>
                                            <span
                                                class="text-xs truncate max-w-[140px] text-gray-300">{{ $name }}</span>
                                            @if ($sizeTxt)
                                                <span
                                                    class="text-[10px] opacity-70 text-gray-400">{{ $sizeTxt }}</span>
                                            @endif
                                            <i
                                                class="fa-solid fa-arrow-down-to-line text-xs opacity-80 ml-auto text-gray-400"></i>
                                        </a>
                                    @endif
                                @endif
                            @endforeach
                        </div>

                        @if ($text === '')
                            <div class="text-[10px] mt-1 opacity-80 text-right text-gray-400">
                                {{ $ts->format('H:i') }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Composer --}}
    @php
        $formId = 'deoChatForm';
        $attachId = 'deoAttach';
        $sendButtonId = 'deoSendButton';
    @endphp

    <form id="{{ $formId }}" action="{{ route('deo.messages.store') }}" method="POST"
        enctype="multipart/form-data"
        class="flex-shrink-0 border-t border-gray-800 bg-gray-900">
        @csrf
        <input type="hidden" name="client_id" value="{{ $selected_client->id }}" />

        <div class="px-3 pt-2">
            <div data-preview class="hidden mb-2 flex flex-wrap gap-2"></div>
        </div>

        <div class="px-3 pb-3 pt-1 flex items-end gap-2">
            <input id="{{ $attachId }}" type="file" name="files[]" class="sr-only chat-file-input" multiple
                accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar,.csv,.ppt,.pptx">

            <label for="{{ $attachId }}"
                class="shrink-0 p-2 rounded-xl bg-gray-800 text-gray-400 cursor-pointer hover:bg-gray-700"
                title="Attach files">
                <i class="fa-solid fa-paperclip"></i>
            </label>

            <textarea name="body" rows="1" placeholder="Type a message…"
                class="min-h-[44px] max-h-40 flex-1 resize-y rounded-xl px-3 py-2 bg-gray-800 text-white border border-gray-700 focus:outline-none focus:border-indigo-500"></textarea>

            <button type="submit" id="{{ $sendButtonId }}"
                class="shrink-0 px-4 h-11 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium cursor-pointer">
                Send
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-scroll to bottom
        const scroll = document.getElementById('chatScroll');
        if (scroll) scroll.scrollTop = scroll.scrollHeight;

        // Get form elements
        const form = document.getElementById('{{ $formId }}');
        const input = document.getElementById('{{ $attachId }}');
        const preview = form ? form.querySelector('[data-preview]') : null;
        const textarea = form ? form.querySelector('textarea[name="body"]') : null;
        const sendButton = document.getElementById('{{ $sendButtonId }}');

        if (!form || !input || !preview || !textarea || !sendButton) {
            console.error('Required form elements not found');
            return;
        }

        const objectUrls = [];

        // Function to update send button state
        function updateSendButton() {
            const hasText = textarea.value.trim().length > 0;
            const hasFiles = input.files && input.files.length > 0;
            const shouldEnable = hasText || hasFiles;

            console.log('Update send button:', {
                hasText,
                hasFiles,
                shouldEnable
            });

            if (shouldEnable) {
                sendButton.disabled = false;
                sendButton.classList.remove('bg-indigo-400', 'cursor-not-allowed');
                sendButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'cursor-pointer');
            } else {
                sendButton.disabled = true;
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

        // File input change handler
        input.addEventListener('change', function() {
            console.log('File input changed');

            // Clear old previews and revoke object URLs
            objectUrls.forEach(u => URL.revokeObjectURL(u));
            objectUrls.length = 0;
            preview.innerHTML = '';

            const files = Array.from(this.files || []);
            console.log('Files selected:', files.length);

            if (!files.length) {
                preview.classList.add('hidden');
                updateSendButton();
                return;
            }

            preview.classList.remove('hidden');

            files.forEach((f, idx) => {
                const isImg = f.type && f.type.startsWith('image/');
                const url = isImg ? URL.createObjectURL(f) : null;
                if (url) objectUrls.push(url);

                const chip = document.createElement('div');
                chip.className =
                    'group relative rounded-xl border border-gray-700 bg-gray-800 text-gray-300 overflow-hidden';

                if (isImg) {
                    chip.innerHTML = `
                        <img src="${url}" class="w-28 h-20 object-cover block">
                        <button type="button" data-i="${idx}" class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-gray-900 text-white grid place-items-center hover:bg-gray-800 transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    `;
                } else {
                    chip.innerHTML = `
                        <div class="px-3 py-2 flex items-center gap-2">
                            <i class="fa-regular fa-file-lines"></i>
                            <span class="max-w-[180px] truncate text-sm">${f.name}</span>
                        </div>
                        <button type="button" data-i="${idx}" class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-gray-900 text-white grid place-items-center hover:bg-gray-800 transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    `;
                }

                preview.appendChild(chip);
            });

            // Update button state after files are added
            updateSendButton();

            // Remove file functionality
            preview.querySelectorAll('button[data-i]').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const removeIdx = parseInt(this.getAttribute('data-i'));
                    console.log('Removing file at index:', removeIdx);

                    const dt = new DataTransfer();
                    const currentFiles = Array.from(input.files);

                    currentFiles.forEach((f, i) => {
                        if (i !== removeIdx) {
                            dt.items.add(f);
                        }
                    });

                    input.files = dt.files;

                    // Trigger change event to update preview
                    const changeEvent = new Event('change', {
                        bubbles: true
                    });
                    input.dispatchEvent(changeEvent);
                });
            });
        });

        // Form submit handler
        form.addEventListener('submit', function(e) {
            const hasText = textarea.value.trim().length > 0;
            const hasFiles = input.files && input.files.length > 0;

            if (!hasText && !hasFiles) {
                e.preventDefault();
                console.log('Prevented form submission: no content');
                return;
            }

            console.log('Form submitting with:', {
                text: textarea.value,
                files: input.files ? input.files.length : 0
            });

            // Don't clear the form immediately - let the server handle it
            // The page will refresh after successful submission
        });

        // Cleanup object URLs when leaving the page
        window.addEventListener('beforeunload', function() {
            objectUrls.forEach(u => URL.revokeObjectURL(u));
        });
    });
</script>

<script>
    // Wait for the chat container to be added to DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1 && node.id === 'chatScroll') {
                    console.log('Chat container added to DOM, scrolling...');
                    setTimeout(() => {
                        node.scrollTop = node.scrollHeight;
                    }, 100);
                    observer.disconnect();
                }
            });
        });
    });

    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Fallback - direct scroll after delay
    setTimeout(() => {
        const scroll = document.getElementById('chatScroll');
        if (scroll) {
            console.log('Fallback scroll triggered');
            scroll.scrollTop = scroll.scrollHeight;
        }
    }, 1000);
</script>