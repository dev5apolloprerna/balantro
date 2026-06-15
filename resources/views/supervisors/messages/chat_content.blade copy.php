{{-- expects: $messages, $clientUserId, $selectedAgentId, $clientName (optional) --}}

@php
$title = $clientName ?: 'Supervisor room';
@endphp

<div class="h-full bg-gray-900/60 border border-gray-800 rounded-2xl overflow-hidden flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800 bg-gray-900/60">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                {{ strtoupper(mb_substr($title, 0, 1)) }}
            </div>
            <div>
                {{-- <img src="{{ asset('images/brand/balantro-logo-dark.svg') }}" class="h-6 block dark:hidden" alt="Balantro">
                <img src="{{ asset('images/brand/balantro-logo-white.svg') }}" class="h-6 hidden dark:block"
                    alt="Balantro"> --}}
                <div class="text-xs text-gray-400">{{ $title }}</div>
            </div>
        </div>
        <div class="flex items-center gap-1 text-gray-300">
            <button type="button" class="p-2 rounded-lg hover:bg-gray-800" title="Search"><i
                    class="fa-solid fa-magnifying-glass"></i></button>
            <button type="button" class="p-2 rounded-lg hover:bg-gray-800" title="More"><i
                    class="fa-solid fa-ellipsis-vertical"></i></button>
        </div>
    </div>

    {{-- Messages --}}
    <div class="flex-1 overflow-y-auto p-4 bg-white dark:bg-neutral-800 messageBox-{{ request('client_id') }}"
        id="messages" data-message-target="messagesContainer" data-action="scroll->message#handleScroll">
        <div id="supervisor_message_blocks">
            @if ($messages && count($messages) > 0)

            @php $lastMessageDate = null; @endphp
            <div class="space-y-3">
                @foreach ($messages as $message)
                @php
                // robust time (works whether it's Carbon or string)
                $ts =
                $message->created_at instanceof \Carbon\Carbon
                ? $message->created_at
                : \Carbon\Carbon::parse($message->created_at);

                $currentMessageDate = $ts->toDateString();

                // am I the sender?
                $isMe = (int) ($message->sender_id ?? $message->sender?->id) === (int) auth()->id();

                // attachments: use ->attachments (from message_attachments) else fallback to ->documents
                $atts = collect($message->attachments ?? []);
                if ($atts->isEmpty() && isset($message->documents)) {
                // build a compatible structure so the renderer below works
                $atts = collect($message->documents)->map(function ($d) {
                return (object) [
                'url' => method_exists($d, 'getAttribute')
                ? $d->getAttribute('url')
                : $d->url ?? null,
                'name' => $d->original_name ?? ($d->file_name ?? 'file'),
                'mime' => $d->mime ?? ($d->mime_type ?? null),
                'size' => (int) ($d->size ?? 0),
                ];
                });
                }

                // attachment display prep
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
                $imageOnly = $attCount > 0 && $preparedAtts->every(fn($x) => $x['isImg']);
                $singleBigImage = $attCount === 1 && $imageOnly;
                @endphp

                {{-- date chip --}}
                @if ($lastMessageDate != $currentMessageDate)
                <div class="flex justify-center my-4">
                    <span
                        class="px-3 py-1 text-xs bg-neutral-200 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 rounded-full">
                        @if ($currentMessageDate == now()->toDateString())
                        Today
                        @elseif ($currentMessageDate == now()->subDay()->toDateString())
                        Yesterday
                        @else
                        {{ $ts->format('F j, Y') }}
                        @endif
                    </span>
                </div>
                @php $lastMessageDate = $currentMessageDate; @endphp
                @endif

                {{-- bubble row --}}
                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}"
                    id="message-{{ $message->id }}">
                    <div class="flex max-w-[85%] gap-2">

                        {{-- left avatar (only for others) --}}
                        @unless ($isMe)
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center"
                                style="width: 32px; height: 32px;">
                                {{-- {{ strtoupper(substr($message->sender->name ?? 'U', 0, 1)) }} --}}
                                {{ strtoupper(substr($message->sender->type ?? 'U', 0, 1)) }}
                            </div>
                        </div>
                        @endunless

                        <div class="flex-1 flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            {{-- attachments (images/videos/files) --}}
                            @if ($attCount)
                            <div
                                class="mt-0 mb-1 w-full {{ $singleBigImage ? '' : 'grid grid-cols-2 gap-2' }}">
                                @foreach ($preparedAtts as $a)
                                @if ($a['isImg'])
                                <a href="{{ $a['url'] }}" target="_blank"
                                    class="block overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-600 hover:opacity-95">
                                    <img src="{{ $a['url'] }}" alt="{{ $a['name'] }}"
                                        class="w-full {{ $singleBigImage ? 'max-h-[360px]' : 'h-28' }} object-cover">
                                </a>
                                @elseif ($a['isVid'])
                                <div
                                    class="overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-600 bg-black/10">
                                    <video src="{{ $a['url'] }}"
                                        class="w-full {{ $singleBigImage ? 'max-h-[360px]' : 'h-28' }} object-cover"
                                        controls></video>
                                </div>
                                @else
                                <a href="{{ $a['url'] }}" target="_blank"
                                    class="flex items-center gap-2 p-2 rounded-lg border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-600">
                                    <iconify-icon icon="heroicons:document"
                                        class="text-xl text-primary-600 dark:text-primary-400"></iconify-icon>
                                    <span
                                        class="text-xs truncate max-w-[160px]">{{ $a['name'] }}</span>
                                    <iconify-icon icon="heroicons:arrow-down-tray"
                                        class="text-sm ml-auto opacity-80"></iconify-icon>
                                </a>
                                @endif
                                @endforeach
                            </div>
                            @endif

                            {{-- text --}}
                            @if ($message->description)
                            <div
                                class="px-3 py-2 rounded-lg {{ $isMe ? 'bg-primary-500 text-white rounded-br-none' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200 rounded-bl-none' }}">
                                <p class="mb-0 break-all overflow-wrap-anywhere text-sm">
                                    {{ $message->description }}
                                </p>
                            </div>
                            @endif

                            <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                                {{ $ts->format('h:i A') }}
                            </div>
                        </div>

                        {{-- right avatar (only for me) --}}
                        @if ($isMe)
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center"
                                style="width: 32px; height: 32px;">
                                {{ strtoupper(substr(auth()->user()->type ?? 'U', 0, 1)) }}
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center h-full text-center py-10 px-6">
                <div
                    class="w-20 h-20 mx-auto bg-neutral-200 dark:bg-neutral-700 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-neutral-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-neutral-800 dark:text-white mb-2">No messages yet</h3>
                <p class="text-neutral-600 dark:text-neutral-300 text-sm">Start a conversation with
                    {{ $sel?->name }}
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- Composer: ONLY uses IDs, no $selected_client --}}
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

            <button type="submit"
                class="shrink-0 px-4 h-11 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium"
                {{ $clientUserId && $selectedAgentId ? '' : 'disabled' }}>
                Send
            </button>
        </div>
    </form>
</div>

<script>
    (function() {
        const scroller = document.getElementById('chatScroll');
        if (scroller) scroller.scrollTop = scroller.scrollHeight;

        document.querySelectorAll('.sup-chat-form').forEach(form => {
            const input = form.querySelector('.chat-file-input');
            const attach = form.querySelector('[data-attach]');
            const preview = form.querySelector('[data-preview]');
            if (!input || !attach || !preview) return;

            attach.addEventListener('click', () => {
                input.value = '';
                input.click();
            });

            input.addEventListener('change', () => {
                const files = Array.from(input.files || []);
                preview.innerHTML = '';
                if (!files.length) {
                    preview.classList.add('hidden');
                    return;
                }
                preview.classList.remove('hidden');

                files.forEach((f, idx) => {
                    const isImg = f.type?.startsWith('image/');
                    const isVid = f.type?.startsWith('video/');
                    const url = (isImg || isVid) ? URL.createObjectURL(f) : null;
                    const chip = document.createElement('div');
                    chip.className =
                        'group relative rounded-xl border border-gray-700 bg-gray-900 text-gray-200 overflow-hidden';
                    chip.innerHTML = isImg ?
                        `
          <img src="${url}" class="w-28 h-20 object-cover block" alt="">
          <button type="button" data-i="${idx}" class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center"><i class="fa-solid fa-xmark text-xs"></i></button>` :
                        isVid ?
                        `
          <video src="${url}" class="w-28 h-20 object-cover block" muted></video>
          <button type="button" data-i="${idx}" class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center"><i class="fa-solid fa-xmark text-xs"></i></button>` :
                        `
          <div class="px-3 py-2 flex items-center gap-2"><i class="fa-regular fa-file-lines"></i><span class="max-w-[180px] truncate text-sm">${f.name}</span></div>
          <button type="button" data-i="${idx}" class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center"><i class="fa-solid fa-xmark text-xs"></i></button>`;
                    if (url) chip.dataset.url = url;
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
                        const chip = btn.closest('.group');
                        if (chip?.dataset.url) URL.revokeObjectURL(chip.dataset
                            .url);
                        input.files = dt.files;
                        input.dispatchEvent(new Event('change'));
                    });
                });
            });
        });
    })();
</script>