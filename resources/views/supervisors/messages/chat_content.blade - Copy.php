{{-- expects: $messages, $clientUserId, $selectedAgentId, $clientName (optional) --}}

@php
    $title = $clientName ?: 'Supervisor room';

    $roleLetter = function ($msg) {
        // prefer relation, then column alias from controller, then fallback
        $type = $msg->sender->type ?? ($msg->sender_type ?? (null ?? null));

        if (!$type) {
            // last resort: infer by id match (not perfect, but avoids blank)
            if (auth()->check() && (int) $msg->sender_id === (int) auth()->id()) {
                return 'S';
            }
            return 'U';
        }

        $type = strtolower($type);
        return match ($type) {
            'client', 'customer' => 'C',
            'data_entry_operator', 'dataentryoperator' => 'D',
            'supervisor' => 'S',
            'manager' => 'M',
            default => strtoupper(substr($type, 0, 1)),
        };
    };
@endphp

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

    } */

    /* button[type="submit"]:not(:disabled):hover {
        background-color: rgb(67 56 202) !important;

    } */
</style>

<div class="h-full bg-gray-900/40 border border-gray-800 rounded-2xl overflow-hidden flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800 bg-gray-900/60">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                {{ strtoupper(mb_substr($title, 0, 1)) }}
            </div>
            <div class="text-white font-semibold leading-tight">{{ $title }}</div>
        </div>

        <div class="flex items-center gap-1 text-gray-300">
            <button type="button" class="p-2 rounded-lg hover:bg-gray-800" title="Search in chat">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>

    {{-- Messages --}}
    <div id="chatScroll" class="flex-1 min-h-0 flex flex-col p-3 overflow-y-auto">
        <div id="supervisor_message_blocks">

            @if (!empty($messages) && count($messages) > 0)

                @php $lastMessageDate = null; @endphp

                @foreach ($messages as $message)
                    @php
                        // robust time
                        $ts =
                            $message->created_at instanceof \Carbon\Carbon
                                ? $message->created_at
                                : \Carbon\Carbon::parse($message->created_at);

                        $currentMessageDate = $ts->toDateString();
                        $isMe = (int) ($message->sender_id ?? $message->sender?->id) === (int) auth()->id();

                        // normalize attachments (already normalized in controller,
                        // but keep a defensive fallback)
                        $atts = collect($message->attachments ?? []);
                        if ($atts->isEmpty() && isset($message->documents)) {
                            $atts = collect($message->documents)->map(function ($d) {
                                $url = method_exists($d, 'getAttribute') ? $d->getAttribute('url') : $d->url ?? null;
                                return (object) [
                                    'url' => $url,
                                    'name' => $d->original_name ?? ($d->file_name ?? 'file'),
                                    'mime' => $d->mime ?? ($d->mime_type ?? null),
                                    'size' => (int) ($d->size ?? 0),
                                ];
                            });
                        }

                        $preparedAtts = $atts
                            ->map(function ($a) {
                                $url = $a->url ?? '#';
                                $name = $a->name ?? basename(parse_url($url, PHP_URL_PATH) ?? 'file');
                                $mime = strtolower($a->mime ?? '');
                                $isImg =
                                    (bool) (str_starts_with($mime, 'image/') ||
                                        preg_match('/\.(png|jpe?g|gif|webp|bmp|heic|heif)$/i', $name));
                                $isVid =
                                    (bool) (str_starts_with($mime, 'video/') ||
                                        preg_match('/\.(mp4|webm|mov|m4v)$/i', $name));
                                return compact('url', 'name', 'mime', 'isImg', 'isVid');
                            })
                            ->values();

                        $attCount = $preparedAtts->count();
                    @endphp

                    {{-- Date chip --}}
                    @if ($lastMessageDate !== $currentMessageDate)
                        <div class="my-2 flex justify-center">
                            <span
                                class="px-3 py-1 text-xs rounded-full bg-gray-800 text-gray-300 border border-gray-700">
                                @if ($currentMessageDate === now()->toDateString())
                                    Today
                                @elseif ($currentMessageDate === now()->subDay()->toDateString())
                                    Yesterday
                                @else
                                    {{ $ts->format('D, d M Y') }}
                                @endif
                            </span>
                        </div>
                        @php $lastMessageDate = $currentMessageDate; @endphp
                    @endif

                    {{-- Message row --}}
                    <div class="mt-1 flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        {{-- Left badge (always left side) --}}
                        <div class="mr-2 mt-0.5 shrink-0">
                            <div
                                class="w-7 h-7 rounded-full grid place-items-center text-[11px] font-semibold
                                        {{ $isMe ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-100' }}">
                                {{ $roleLetter($message) }}
                            </div>
                        </div>

                        <div class="flex-1 flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            {{-- Attachments --}}
                            @if ($attCount)
                                <div class="mt-2 grid grid-cols-2 gap-2">
                                    @foreach ($preparedAtts as $a)
                                        @if ($a['isImg'])
                                            <a href="{{ $a['url'] }}" target="_blank" rel="noopener"
                                                class="block overflow-hidden rounded-xl border border-white/10 hover:opacity-95">
                                                <img src="{{ $a['url'] }}" alt="{{ $a['name'] }}"
                                                    class="w-full h-28 object-cover">
                                            </a>
                                        @elseif ($a['isVid'])
                                            <div class="overflow-hidden rounded-xl border border-white/10 bg-black/10">
                                                <video src="{{ $a['url'] }}" class="w-full h-28 object-cover"
                                                    controls></video>
                                            </div>
                                        @else
                                            <a href="{{ $a['url'] }}" target="_blank" rel="noopener"
                                                class="flex items-center gap-2 p-2 rounded-xl border border-white/10 bg-black/10 hover:bg-black/20">
                                                <i class="fa-regular fa-file-lines text-sm"></i>
                                                <span class="text-xs truncate max-w-[160px]">{{ $a['name'] }}</span>
                                                <i
                                                    class="fa-solid fa-arrow-down-to-line text-xs opacity-80 ml-auto"></i>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            {{-- Text bubble --}}
                            @if (!empty($message->description))
                                <div
                                    class="max-w-[78%] md:max-w-[66%] px-3 py-2 rounded-2xl leading-snug
                                            {{ $isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-700 text-gray-100 rounded-bl-none' }}">
                                    <p class="whitespace-pre-line break-words">{{ $message->description }}</p>
                                </div>
                            @endif

                            <div class="text-[10px] mt-1.5 opacity-80 {{ $isMe ? 'text-right' : 'text-left' }}">
                                {{ $ts->format('h:i A') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Composer --}}
    <form action="{{ route('supervisor.messages.store') }}" method="POST" enctype="multipart/form-data"
        class="sup-chat-form border-t border-gray-800 bg-gray-900/60">
        @csrf
        <input type="hidden" name="client_user_id" value="{{ $clientUserId ?? '' }}">
        <input type="hidden" name="agent_id" value="{{ $selectedAgentId ?? '' }}">
        @php $receiverId = $clientUserId ?: $selectedAgentId; @endphp
        <input type="hidden" name="receiver_id" value="{{ $receiverId }}">

        <div class="px-3 pt-2">
            <div data-preview class="hidden mb-2 flex flex-wrap gap-2"></div>
        </div>

        <div class="px-3 pb-3 pt-1 flex items-end gap-2">
            <input type="file" name="files[]" class="chat-file-input hidden" multiple
                accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">

            <button type="button" data-attach
                class="shrink-0 p-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200" title="Attach files">
                <i class="fa-solid fa-paperclip"></i>
            </button>

            <textarea name="description" rows="1" placeholder="Type a message…"
                class="min-h-[44px] max-h-40 flex-1 resize-y rounded-xl px-3 py-2
                             bg-gray-900 text-white border border-gray-700
                             focus:outline-none focus:border-indigo-500"
                {{ $clientUserId && $selectedAgentId ? '' : 'disabled' }}></textarea>

            <button type="submit" id="sendButton"
                class="shrink-0 px-4 h-11 rounded-xl bg-indigo-400 text-white font-medium cursor-not-allowed"
                {{ $clientUserId && $selectedAgentId ? '' : 'disabled' }} disabled>
                Send
            </button>
        </div>
    </form>
</div>

<script>
    (function() {
        // keep scrolled to bottom
        const scroller = document.getElementById('chatScroll');
        if (scroller) scroller.scrollTop = scroller.scrollHeight;

        document.querySelectorAll('.sup-chat-form').forEach(form => {
            const input = form.querySelector('.chat-file-input');
            const attach = form.querySelector('[data-attach]');
            const preview = form.querySelector('[data-preview]');
            const textarea = form.querySelector('textarea[name="description"]');
            const sendButton = form.querySelector('#sendButton');

            if (!input || !attach || !preview || !textarea || !sendButton) return;

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

            attach.addEventListener('click', () => {
                input.value = '';
                input.click();
            });

            input.addEventListener('change', () => {
                const files = Array.from(input.files || []);
                preview.innerHTML = '';
                if (!files.length) {
                    preview.classList.add('hidden');
                    updateSendButton();
                    return;
                }
                preview.classList.remove('hidden');

                files.forEach((f, idx) => {
                    const isImg = f.type && f.type.startsWith('image/');
                    const isVid = f.type && f.type.startsWith('video/');
                    const url = (isImg || isVid) ? URL.createObjectURL(f) : null;

                    const chip = document.createElement('div');
                    chip.className =
                        'group relative rounded-xl border border-gray-700 bg-gray-900 text-gray-200 overflow-hidden';
                    chip.innerHTML = isImg ?
                        `<img src="${url}" class="w-28 h-20 object-cover block" alt="">
                       <button type="button" data-i="${idx}" class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center"><i class="fa-solid fa-xmark text-xs"></i></button>` :
                        isVid ?
                        `<video src="${url}" class="w-28 h-20 object-cover block" muted></video>
                       <button type="button" data-i="${idx}" class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center"><i class="fa-solid fa-xmark text-xs"></i></button>` :
                        `<div class="px-3 py-2 flex items-center gap-2"><i class="fa-regular fa-file-lines"></i><span class="max-w-[180px] truncate text-sm">${f.name}</span></div>
                       <button type="button" data-i="${idx}" class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center"><i class="fa-solid fa-xmark text-xs"></i></button>`;

                    if (url) chip.dataset.url = url;
                    preview.appendChild(chip);
                });

                // Update button state after files are added
                updateSendButton();

                preview.querySelectorAll('button[data-i]').forEach(btn => {
                    btn.classList.remove('hidden');
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const removeIdx = Number(btn.dataset.i);
                        const dt = new DataTransfer();
                        Array.from(input.files).forEach((f, i) => {
                            if (i !== removeIdx) dt.items.add(f);
                        });
                        const chip = btn.closest('.group');
                        if (chip?.dataset.url) URL.revokeObjectURL(chip.dataset
                            .url);
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
                    if (scroller) {
                        scroller.scrollTop = scroller.scrollHeight;
                    }
                }, 100);
            });
        });
    })();
</script>
