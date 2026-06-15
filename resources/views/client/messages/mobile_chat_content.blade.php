{{-- client/messages/mobile_chat_content.blade.php --}}
<div
    class="mobile-chat-wrapper h-full bg-white border border-gray-200 rounded-2xl overflow-hidden flex flex-col dark:bg-gray-900 dark:border-gray-700">
    <style>
        .dark #mobile-messages-container {
            background-color: #000 !important;
        }
    </style>
    {{-- Fixed Header --}}
    <div
        class="flex-shrink-0 flex items-center gap-2 px-4 py-3 border-b border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
        <a href="{{ url()->previous() }}" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600"
            aria-label="Back">
            <i class="fa-solid fa-arrow-left text-gray-600 dark:text-gray-400"></i>
        </a>
        <div class="font-semibold truncate text-gray-800 dark:text-gray-300">
            {{ 'Balantro' }}
        </div>
    </div>

    {{-- Scrollable Messages Area --}}
    <div id="mobile-messages-container"
        class="mobile-messages-container p-4 space-y-4 mobile-scroll-fix bg-white dark:bg-gray-900">

        @php
            $prevDay = null;
            $roleLetter = function ($msg) {
                if (isset($msg->sender) && !empty($msg->sender->type)) {
                    $t = strtolower($msg->sender->type);
                    return match ($t) {
                        'client', 'customer' => 'C',
                        'data_entry_operator', 'deo', 'manager', 'supervisor' => 'B',
                        default => 'B',
                    };
                }
                if (auth()->check() && (int) $msg->sender_id === (int) auth()->id()) {
                    $my = optional(auth()->user())->type;
                    return $my ? strtoupper(substr($my, 0, 1)) : 'C';
                }
                return 'U';
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
                <div class="my-3 flex justify-center">
                    <span
                        class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600 border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        {{ \Carbon\Carbon::parse($msg->created_at)->format('D, d M Y') }}
                    </span>
                </div>
                @php $prevDay = $dayKey; @endphp
            @endif

            {{-- Message Row --}}
            <div class="mt-2 flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                <div class="mr-2 mt-0.5 shrink-0">
                    <div
                        class="w-7 h-7 rounded-full grid place-items-center text-[11px] font-semibold {{ $isMe ? 'bg-indigo-600 text-white' : 'bg-gray-500 text-white dark:bg-gray-600' }}">
                        {{ $roleLetter($msg) }}
                    </div>
                </div>

                <div
                    class="max-w-[85%] px-4 py-3 rounded-2xl leading-snug {{ $isMe ? 'bg-indigo-600 text-white rounded-br-md' : 'bg-gray-100 text-gray-800 border border-gray-200 rounded-bl-md dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600' }}">

                    {{-- Message Body --}}
                    @if (!empty($body))
                        <div class="flex items-end justify-between gap-2">
                            <p class="break-words flex-1 leading-relaxed">{{ $body }}</p>
                            <span
                                class="text-[10px] opacity-70 shrink-0 ml-2 whitespace-nowrap {{ $isMe ? 'text-indigo-100' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}
                            </span>
                        </div>
                    @endif

                    {{-- Attachments --}}
                    @if ($attCount)
                        @php
                            $imageOnly = true;
                            $attData = [];
                            foreach ($atts as $a) {
                                $url = $a->url ?? '#';
                                $name = $a->name ?? basename(parse_url($url, PHP_URL_PATH) ?? 'file');
                                $mime = strtolower($a->mime ?? '');
                                $isImg =
                                    str_starts_with($mime, 'image/') ||
                                    preg_match('/\.(png|jpe?g|gif|webp|bmp|heic|heif)$/i', $name);
                                $isVid =
                                    str_starts_with($mime, 'video/') || preg_match('/\.(mp4|webm|mov|m4v)$/i', $name);
                                $imageOnly = $imageOnly && $isImg;
                                $attData[] = compact('url', 'name', 'mime', 'isImg', 'isVid');
                            }
                            $singleBigImage = $attCount === 1 && $imageOnly;
                        @endphp

                        <div class="{{ $singleBigImage ? 'mt-2' : 'mt-3 grid grid-cols-2 gap-2' }}">
                            @foreach ($attData as $a)
                                @if ($a['isImg'])
                                    <a href="{{ $a['url'] }}" target="_blank"
                                        class="block overflow-hidden rounded-lg border border-gray-300 hover:opacity-90 dark:border-gray-600">
                                        <img src="{{ $a['url'] }}" alt="{{ $a['name'] }}" loading="lazy"
                                            class="w-full {{ $singleBigImage ? 'max-h-[200px]' : 'h-20' }} object-cover">
                                    </a>
                                @elseif ($a['isVid'])
                                    <div
                                        class="overflow-hidden rounded-lg border border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800">
                                        <video src="{{ $a['url'] }}" preload="metadata"
                                            class="w-full {{ $singleBigImage ? 'max-h-[200px]' : 'h-20' }} object-cover"
                                            controls></video>
                                    </div>
                                @else
                                    <a href="{{ $a['url'] }}" target="_blank"
                                        class="flex items-center gap-2 p-3 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700">
                                        <i class="fa-regular fa-file-lines text-gray-600 dark:text-gray-400"></i>
                                        <span
                                            class="text-xs truncate text-gray-700 dark:text-gray-300">{{ $a['name'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        <div
                            class="text-[10px] opacity-70 text-right mt-2 {{ $isMe ? 'text-indigo-100' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <div id="scroll-anchor"></div>
    </div>

    {{-- Input Area --}}
    <div class="flex-shrink-0 p-4 border-t border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
        <form id="mobile-message-form" action="{{ route('client.messages.store') }}" method="POST"
            enctype="multipart/form-data" class="flex gap-3">
            @csrf
            <input type="hidden" name="to_user_id" value="{{ $selected_user->id ?? '' }}">
            <input type="file" name="files[]" id="mobile-file-input" class="sr-only" multiple
                accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
            <label for="mobile-file-input"
                class="shrink-0 p-3 rounded-xl bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 cursor-pointer dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-600">
                <i class="fa-solid fa-paperclip"></i>
            </label>
            <textarea name="body" rows="1" placeholder="Type a message…"
                class="min-h-[48px] max-h-40 flex-1 resize-y rounded-xl px-4 py-3 bg-white text-gray-900 border border-gray-300 focus:outline-none focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"></textarea>
            <button type="submit"
                class="shrink-0 px-4 h-12 rounded-xl bg-indigo-600 text-white font-medium hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-700">
                Send
            </button>
        </form>
    </div>
</div>

{{-- Mobile-specific JavaScript remains the same --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile auto-scroll function
        function scrollToBottomMobile() {
            const container = document.getElementById('mobile-messages-container');
            const anchor = document.getElementById('scroll-anchor');

            if (container && anchor) {
                // Use multiple methods for better mobile compatibility
                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;
                }, 100);

                setTimeout(() => {
                    anchor.scrollIntoView({
                        behavior: 'smooth',
                        block: 'end'
                    });
                }, 150);
            }
        }

        // Initial scroll
        scrollToBottomMobile();

        // Scroll when keyboard appears/disappears
        window.addEventListener('resize', scrollToBottomMobile);

        // Auto-scroll when new messages are added
        const observer = new MutationObserver(scrollToBottomMobile);
        const messagesContainer = document.getElementById('mobile-messages-container');
        if (messagesContainer) {
            observer.observe(messagesContainer, {
                childList: true,
                subtree: true
            });
        }

        // Handle form submission
        const messageForm = document.getElementById('mobile-message-form');
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                // Allow form to submit normally
                // Scroll will happen when page reloads with new message
                setTimeout(scrollToBottomMobile, 500);
            });

            // Auto-resize textarea
            const textarea = messageForm.querySelector('textarea[name="body"]');
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 160) + 'px';
                });
            }
        }

        // Additional safety scroll
        setTimeout(scrollToBottomMobile, 500);

        // Handle page visibility (when returning to tab)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                setTimeout(scrollToBottomMobile, 300);
            }
        });
    });
</script>
