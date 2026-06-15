{{-- resources/views/data_entry_operators/messages/chat_content.blade.php --}}
<div class="h-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-2xl overflow-hidden flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        <div class="flex items-center gap-3">
            @php 
                $clientName = $selected_client->name ?? 'Client';
                $initials = strtoupper(mb_substr($clientName, 0, 2));
            @endphp
            <div class="w-9 h-9 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                {{ $initials }}
            </div>
            <div class="text-gray-900 dark:text-white font-medium">{{ $clientName }}</div>
        </div>

        <button type="button" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300" title="Search in chat">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>

    {{-- Messages area --}}
    <div id="desktopChatMessages" class="flex-1 min-h-0 flex flex-col p-3 overflow-y-auto bg-white dark:bg-black">
        @php
            $prevDay = null;
            $roleLetter = function ($msg) use ($selected_client) {
                if (isset($msg->sender) && !empty($msg->sender->type)) {
                    return match (strtolower($msg->sender->type)) {
                        'client', 'customer' => 'C',
                        'data_entry_operator','dataentryoperator' => 'D',
                        'supervisor' => 'S',
                        'manager' => 'M',
                        default => strtoupper(substr($msg->sender->type, 0, 1)),
                    };
                }

                if (auth()->id() == $msg->sender_id) return 'D';
                if ($selected_client && $selected_client->user_id == $msg->sender_id) return 'C';

                return 'U';
            };

            $fmtSize = fn($bytes) =>
                (!$bytes || !is_numeric($bytes)) ? null :
                (function ($bytes) {
                    $units = ['B','KB','MB','GB'];
                    $i = 0;
                    while ($bytes >= 1024 && $i < 3) { $bytes /= 1024; $i++; }
                    return ($i ? number_format($bytes,1) : $bytes) . ' ' . $units[$i];
                })($bytes);
        @endphp

        @foreach ($messages as $msg)
            @php
                $ts = $msg->created_at instanceof \Carbon\Carbon ? $msg->created_at : \Carbon\Carbon::parse($msg->created_at);
                $isMe = auth()->id() == $msg->sender_id;
                $dayKey = $ts->toDateString();
            @endphp

            {{-- Day Divider --}}
            @if ($prevDay !== $dayKey)
                <div class="my-2 flex justify-center">
                    <span class="px-3 py-1 text-xs rounded-full bg-gray-300 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                        @if ($dayKey === today()->toDateString()) Today
                        @elseif ($dayKey === today()->subDay()->toDateString()) Yesterday
                        @else {{ $ts->format('D, d M Y') }}
                        @endif
                    </span>
                </div>
                @php $prevDay = $dayKey; @endphp
            @endif

            {{-- Message Row --}}
            <div class="mt-1 flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                <div class="mr-2 mt-0.5 shrink-0">
                    <div class="w-7 h-7 rounded-full grid place-items-center text-[11px] font-semibold {{ $isMe ? 'bg-indigo-600 text-white' : 'bg-gray-300 dark:bg-gray-700 text-gray-200' }}">
                        {{ $roleLetter($msg) }}
                    </div>
                </div>

                <div class="max-w-[78%] md:max-w-[66%] px-3 py-2 rounded-2xl leading-snug {{ $isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-300 dark:bg-gray-800 text-gray-100 rounded-bl-none' }}">
                    @php $text = trim($msg->description ?? $msg->body ?? ''); @endphp

                    @if ($text !== '')
                        <div class="flex justify-between items-end">
                            <p class="whitespace-pre-line break-words flex-1">{{ $text }}</p>
                            <span class="text-[10px] opacity-80 ml-2">{{ $ts->format('H:i') }}</span>
                        </div>
                    @endif

                    {{-- Attachments --}}
                    @php
                        $files = collect($msg->attachments ?? []);
                        if ($files->isEmpty() && isset($msg->documents)) {
                            $files = collect($msg->documents)->map(fn($d) => (object)[
                                'url' => $d->url ?? Storage::url($d->file_path),
                                'name' => $d->original_name ?? $d->file_name,
                                'mime' => $d->mime ?? $d->mime_type,
                                'size' => $d->size,
                            ]);
                        }
                    @endphp

                    @if ($files->count())
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach ($files as $att)
                                @php
                                    $url = $att->url;
                                    $name = $att->name;
                                    $mime = strtolower($att->mime ?? '');
                                    $isImg = str_starts_with($mime,'image/');
                                    $isVideo = str_starts_with($mime,'video/');
                                @endphp

                                @if ($isImg)
                                    <a href="{{ $url }}" target="_blank" class="rounded-xl overflow-hidden border dark:border-gray-600">
                                        <img src="{{ $url }}" class="w-full h-28 object-cover">
                                    </a>

                                @elseif ($isVideo)
                                    <video controls class="rounded-xl w-full h-28 border dark:border-gray-600 bg-black/50">
                                        <source src="{{ $url }}">
                                    </video>

                                @else
                                    <a href="{{ $url }}" target="_blank" class="flex items-center gap-2 p-2 rounded-xl border dark:border-gray-600 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <i class="fa-regular fa-file-lines text-sm"></i>
                                        <span class="text-xs truncate">{{ $name }}</span>
                                        <i class="fa-solid fa-arrow-down-to-line text-xs opacity-70 ml-auto"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        @if ($text === '')
                            <div class="text-[10px] text-right opacity-80 mt-1">{{ $ts->format('H:i') }}</div>
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

    <form id="{{ $formId }}" action="{{ route('deo.messages.store') }}" method="POST" enctype="multipart/form-data"
          class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        @csrf

        <input type="hidden" name="receiver_id" value="{{ $selected_client->user_id ?? $selected_client->id }}">
        <input type="hidden" name="client_id" value="{{ $selected_client->id }}">

        <div class="px-3 pt-2">
            <div data-preview class="hidden mb-2 flex flex-wrap gap-2"></div>
        </div>

        <div class="px-3 pb-3 flex items-end gap-2">
            <input id="{{ $attachId }}" type="file" name="files[]" class="sr-only chat-file-input" multiple
    accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar,.csv,.ppt,.pptx">


            <label for="{{ $attachId }}" class="p-2 rounded-xl bg-white dark:bg-gray-700 border dark:border-gray-600 cursor-pointer">
                <i class="fa-solid fa-paperclip"></i>
            </label>

            <textarea name="description" rows="1" placeholder="Type a message…"
                      class="min-h-[44px] max-h-40 flex-1 resize-y rounded-xl px-3 py-2 bg-white dark:bg-gray-700 border dark:border-gray-600 focus:outline-none focus:border-indigo-500"></textarea>

            <button id="{{ $sendButtonId }}" type="submit"
                    class="px-4 h-11 rounded-xl bg-indigo-400 text-white cursor-not-allowed"
                    disabled>Send</button>
        </div>
    </form>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("{{ $formId }}");
    const input = document.getElementById("{{ $attachId }}");
    const preview = form.querySelector("[data-preview]");
    const textarea = form.querySelector('textarea[name="description"]');
    const sendButton = document.getElementById("{{ $sendButtonId }}");
    const scroll = document.getElementById("desktopChatMessages");

    const objectUrls = [];

    function autoScroll() {
        if (scroll) scroll.scrollTop = scroll.scrollHeight;
    }

    autoScroll();

    function updateSendButton() {
        const hasText = textarea.value.trim().length > 0;
        const hasFiles = input.files.length > 0;

        const enable = hasText || hasFiles;

        sendButton.disabled = !enable;
        sendButton.classList.toggle("bg-indigo-600", enable);
        sendButton.classList.toggle("cursor-pointer", enable);
        sendButton.classList.toggle("bg-indigo-400", !enable);
        sendButton.classList.toggle("cursor-not-allowed", !enable);
    }

    textarea.addEventListener("input", () => {
        textarea.style.height = "auto";
        textarea.style.height = Math.min(textarea.scrollHeight, 160) + "px";
        updateSendButton();
    });

    input.addEventListener("change", () => {
        objectUrls.forEach((u) => URL.revokeObjectURL(u));
        objectUrls.length = 0;
        preview.innerHTML = "";

        const files = Array.from(input.files);
        if (!files.length) {
            preview.classList.add("hidden");
            updateSendButton();
            return;
        }

        preview.classList.remove("hidden");

        files.forEach((f, i) => {
            const isImg = f.type.startsWith("image/");
            const url = isImg ? URL.createObjectURL(f) : "";
            if (url) objectUrls.push(url);

            const chip = document.createElement("div");
            chip.className =
                "group relative rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 overflow-hidden";

            chip.innerHTML =
                isImg
                    ? `<img src="${url}" class="w-28 h-20 object-cover">
                       <button data-i="${i}" class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center">
                            <i class="fa-solid fa-xmark text-xs"></i>
                       </button>`
                    : `<div class="p-2 flex items-center gap-2">
                         <i class="fa-regular fa-file-lines"></i>
                         <span class="truncate max-w-[180px] text-xs">${f.name}</span>
                       </div>
                       <button data-i="${i}" class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-black/70 text-white hidden group-hover:grid place-items-center">
                         <i class="fa-solid fa-xmark text-xs"></i>
                       </button>`;

            preview.appendChild(chip);
        });

        preview.querySelectorAll("button[data-i]").forEach((btn) => {
            btn.addEventListener("click", () => {
                const dt = new DataTransfer();
                Array.from(input.files).forEach((f, idx) => {
                    if (idx != btn.dataset.i) dt.items.add(f);
                });
                input.files = dt.files;
                input.dispatchEvent(new Event("change"));
            });
        });

        updateSendButton();
    });

    form.addEventListener("submit", () => {
        setTimeout(() => {
            textarea.value = "";
            textarea.style.height = "auto";
            preview.innerHTML = "";
            preview.classList.add("hidden");
            input.value = "";
            updateSendButton();
            autoScroll();
        }, 80);
    });

});
</script>
