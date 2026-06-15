<style>
    /* NUCLEAR CSS FIX */
    .h-full.bg-gray-900\/40 {
        display: flex !important;
        flex-direction: column !important;
        height: 80vh !important;
        min-height: 600px !important;
        max-height: 90vh !important;
        position: relative !important;
    }

    #chatScroll {
        height: 100% !important;
        min-height: 400px !important;
        overflow-y: auto !important;
        display: flex !important;
        flex-direction: column !important;
        flex: 1 !important;
        position: relative !important;
        background: #1f2937 !important;
        /* Force background to see if it renders */
    }

    /* Force the messages to be visible */
    #chatScroll>div {
        flex-shrink: 0 !important;
        min-height: 100px !important;
    }

    #chatBottom {
        height: 1px !important;
        width: 100% !important;
        flex-shrink: 0 !important;
    }

    /* Emergency visibility */
    .bg-gray-900\/40 {
        opacity: 1 !important;
        visibility: visible !important;
    }
</style>

<div class="h-full bg-gray-900/40 border border-gray-800 rounded-2xl overflow-hidden flex flex-col"
    style="min-height: 600px; height: 80vh;">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800 bg-gray-900/60">
        <div class="flex items-center gap-3">
            <div class="text-white font-semibold leading-tight">
                <img src="{{ asset('assets/images/light-logo.svg') }}" alt="Balantro"
                    class="h-8 block dark:hidden logo-full">
                <img src="{{ asset('assets/images/dark-logo.svg') }}" alt="Balantro"
                    class="h-8 hidden dark:block logo-full">
            </div>
        </div>
        <div class="flex items-center gap-1 text-gray-300">
            <button type="button" class="p-2 rounded-lg hover:bg-gray-800" title="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>

    <div class="px-4 py-2 bg-red-900/20 border-b border-red-800 flex items-center gap-2">
        <button onclick="window.chatScroll?.toBottomInstant()"
            class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg">
            🚨 FORCE SCROLL
        </button>
        <button onclick="window.chatScroll?.forceRefresh()"
            class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">
            🔄 REFRESH
        </button>
        <button onclick="location.reload()"
            class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg">
            ♻️ RELOAD PAGE
        </button>
        <span class="text-red-300 text-sm ml-2">Emergency scroll controls</span>
    </div>

    {{-- Messages Container --}}
    <div id="chatScroll" class="flex flex-col p-3 overflow-y-auto flex-1 min-h-0">
        @php
            $prevDay = null;

            // C/D/M/S badge
            $roleLetter = function ($msg) {
                if (isset($msg->sender) && !empty($msg->sender->type)) {
                    $t = strtolower($msg->sender->type);
                    return match ($t) {
                        'client', 'customer' => 'C',
                        'data_entry_operator', 'deo' => 'B',
                        'manager' => 'B',
                        'supervisor' => 'B',
                        default => strtoupper(substr('B', 0, 1)),
                    };
                }
                if (auth()->check() && (int) $msg->sender_id === (int) auth()->id()) {
                    $my = optional(auth()->user())->type;
                    return $my ? strtoupper(substr($my, 0, 1)) : 'C';
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
                $dayKey = \Carbon\Carbon::parse($msg->created_at)->toDateString();
                $body = $msg->body ?? ($msg->description ?? '');
                $atts = $msg->attachments ?? [];
                $attCount = is_countable($atts) ? count($atts) : 0;
            @endphp

            {{-- Date chip --}}
            @if ($dayKey !== $prevDay)
                <div class="my-2 flex justify-center">
                    <span class="px-3 py-1 text-xs rounded-full bg-gray-800 text-gray-300 border border-gray-700">
                        {{ \Carbon\Carbon::parse($msg->created_at)->format('D, d M Y') }}
                    </span>
                </div>
                @php $prevDay = $dayKey; @endphp
            @endif

            {{-- Row --}}
            <div class="mt-1 flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                {{-- Role badge (always left of bubble) --}}
                <div class="mr-2 mt-0.5 shrink-0">
                    <div
                        class="w-7 h-7 rounded-full grid place-items-center text-[11px] font-semibold
                                {{ $isMe ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-100' }}">
                        {{ $roleLetter($msg) }}
                    </div>
                </div>

                {{-- Bubble --}}
                <div
                    class="max-w-[78%] md:max-w-[66%] px-3 py-2 rounded-2xl leading-snug
                            {{ $isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-700 text-gray-100 rounded-bl-none' }}">
                    @if (!empty($body))
                        <p class="whitespace-pre-line break-words">{{ $body }}</p>
                    @endif

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
                                $isImg =
                                    str_starts_with($mime, 'image/') ||
                                    preg_match('/\.(png|jpe?g|gif|webp|bmp|heic|heif)$/i', $name);
                                $isVid =
                                    str_starts_with($mime, 'video/') || preg_match('/\.(mp4|webm|mov|m4v)$/i', $name);
                                $imageOnly = $imageOnly && $isImg;
                                $attData[] = compact('url', 'name', 'mime', 'size', 'isImg', 'isVid');
                            }
                            $singleBigImage = $attCount === 1 && $imageOnly;
                        @endphp

                        <div class="mt-2 {{ $singleBigImage ? '' : 'grid grid-cols-2 gap-2' }}">
                            @foreach ($attData as $a)
                                @if ($a['isImg'])
                                    <a href="{{ $a['url'] }}" target="_blank"
                                        class="block overflow-hidden rounded-xl border border-white/10 hover:opacity-95">
                                        <img src="{{ $a['url'] }}" alt="{{ $a['name'] }}" loading="lazy"
                                            class="w-full {{ $singleBigImage ? 'max-h-[360px]' : 'h-28' }} object-cover">
                                    </a>
                                @elseif ($a['isVid'])
                                    <div class="overflow-hidden rounded-xl border border-white/10 bg-black/10">
                                        <video src="{{ $a['url'] }}" preload="metadata"
                                            class="w-full {{ $singleBigImage ? 'max-h-[360px]' : 'h-28' }} object-cover"
                                            controls></video>
                                    </div>
                                @else
                                    <a href="{{ $a['url'] }}" target="_blank"
                                        class="flex items-center gap-2 p-2 rounded-xl border border-white/10 bg-black/10 hover:bg-black/20">
                                        <i class="fa-regular fa-file-lines text-sm"></i>
                                        <span class="text-xs truncate max-w-[140px]">{{ $a['name'] }}</span>
                                        @if ($a['size'])
                                            <span class="text-[10px] opacity-70">{{ $fmtSize($a['size']) }}</span>
                                        @endif
                                        <i class="fa-solid fa-arrow-down-to-line text-xs opacity-80 ml-auto"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="text-[10px] mt-1.5 opacity-80 text-right">
                        {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Scroll anchor --}}
        <div id="chatBottom"></div>
    </div>

    {{-- Composer --}}
    <form id="clientChatForm" action="{{ route('client.messages.store') }}" method="POST"
        enctype="multipart/form-data" class="border-t border-gray-800 bg-gray-900/60">
        @csrf
        <input type="hidden" name="to_user_id" value="{{ $selected_user->id ?? '' }}">

        {{-- FIXED: Define $attachId here --}}
        @php
            $attachId = 'client-attach-' . ($selected_user->id ?? 'x');
        @endphp

        <div class="px-3 pt-2">
            <div data-preview class="hidden mb-2 flex flex-wrap gap-2"></div>
        </div>

        <div class="px-3 pb-3 pt-1 flex items-end gap-2">
            {{-- Use sr-only instead of hidden --}}
            <input id="{{ $attachId }}" type="file" name="files[]" class="sr-only chat-file-input" multiple
                accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">

            {{-- Native label triggers the file dialog --}}
            <label for="{{ $attachId }}"
                class="shrink-0 p-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-200 cursor-pointer"
                title="Attach files">
                <i class="fa-solid fa-paperclip"></i>
            </label>

            <textarea name="body" rows="1" placeholder="Type a message…"
                class="min-h-[44px] max-h-40 flex-1 resize-y rounded-xl px-3 py-2
                   bg-gray-900 text-white border border-gray-700
                   focus:outline-none focus:border-indigo-500"></textarea>

            <button type="submit"
                class="shrink-0 px-4 h-11 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium">
                Send
            </button>
        </div>
    </form>
</div>

<script>
    // NUCLEAR SCROLL FIX - COMPLETE REWRITE
    (function() {
        console.log('💥 NUCLEAR SCROLL INITIATED');

        function forceContainerHeight() {
            const container = document.getElementById('chatScroll');
            if (!container) {
                console.error('💀 chatScroll container not found');
                return false;
            }

            // NUCLEAR HEIGHT FIX
            container.style.cssText = `
                height: 70vh !important;
                min-height: 500px !important;
                max-height: 80vh !important;
                overflow-y: auto !important;
                display: flex !important;
                flex-direction: column !important;
                position: relative !important;
                background: #1f2937 !important;
                border: 2px solid red !important;
            `;

            // Force parent heights
            let parent = container.parentElement;
            while (parent && !parent.classList.contains('h-full')) {
                parent.style.height = 'auto';
                parent.style.minHeight = '600px';
                parent = parent.parentElement;
            }

            return true;
        }

        function nuclearScroll() {
            const container = document.getElementById('chatScroll');
            const bottom = document.getElementById('chatBottom');

            if (!container) {
                console.error('💀 Container still not found after nuclear fix');
                return;
            }

            console.log('🔥 NUCLEAR SCROLL ATTEMPT', {
                scrollHeight: container.scrollHeight,
                clientHeight: container.clientHeight,
                offsetHeight: container.offsetHeight,
                hasMessages: container.children.length
            });

            // If container has no height, apply emergency fix
            if (container.clientHeight === 0 || container.scrollHeight === 0) {
                console.warn('⚠️ ZERO HEIGHT DETECTED - APPLYING EMERGENCY FIX');
                forceContainerHeight();

                // Add visible test element
                const testDiv = document.createElement('div');
                testDiv.innerHTML =
                    '<div style="padding: 20px; background: red; color: white; text-align: center;">TEST ELEMENT - HEIGHT SHOULD BE VISIBLE</div>';
                container.appendChild(testDiv);
            }

            // SCROLL METHODS - TRY ALL
            const methods = [
                () => {
                    container.scrollTop = 999999;
                },
                () => {
                    container.scrollTop = container.scrollHeight;
                },
                () => {
                    container.scrollTo(0, 999999);
                },
                () => {
                    container.scrollTo({
                        top: 999999,
                        behavior: 'instant'
                    });
                },
                () => {
                    if (bottom) {
                        bottom.scrollIntoView({
                            behavior: 'instant',
                            block: 'end'
                        });
                        bottom.scrollIntoView(false);
                    }
                },
                () => {
                    window.scrollTo(0, document.body.scrollHeight);
                }
            ];

            methods.forEach((method, i) => {
                try {
                    console.log(`Trying nuclear method ${i + 1}`);
                    method();
                } catch (e) {
                    console.warn(`Method ${i + 1} failed:`, e);
                }
            });

            // Force re-render
            container.style.display = 'none';
            container.offsetHeight; // Trigger reflow
            container.style.display = 'flex';

            console.log('🎯 SCROLL RESULT:', {
                finalScrollTop: container.scrollTop,
                finalScrollHeight: container.scrollHeight,
                finalClientHeight: container.clientHeight
            });
        }

        // MULTIPLE INITIALIZATION ATTEMPTS
        function initNuclearScroll() {
            console.log('🚀 STARTING NUCLEAR INITIALIZATION');

            // Attempt 1: Immediate
            setTimeout(nuclearScroll, 0);

            // Attempt 2: After DOM settle
            setTimeout(nuclearScroll, 100);

            // Attempt 3: After longer delay
            setTimeout(nuclearScroll, 500);

            // Attempt 4: Final attempt
            setTimeout(nuclearScroll, 1000);

            // Attempt 5: Nuclear last resort
            setTimeout(() => {
                console.log('☢️ FINAL NUCLEAR ATTEMPT');
                forceContainerHeight();
                nuclearScroll();

                // Add emergency button if still not working
                if (document.getElementById('chatScroll')?.clientHeight === 0) {
                    const emergencyBtn = document.createElement('button');
                    emergencyBtn.textContent = '🚨 EMERGENCY HEIGHT FIX';
                    emergencyBtn.style.cssText =
                        'position: fixed; top: 10px; left: 10px; z-index: 99999; background: red; color: white; padding: 10px;';
                    emergencyBtn.onclick = () => {
                        forceContainerHeight();
                        nuclearScroll();
                    };
                    document.body.appendChild(emergencyBtn);
                }
            }, 2000);
        }

        // START EVERYTHING
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initNuclearScroll);
        } else {
            initNuclearScroll();
        }

        window.addEventListener('load', initNuclearScroll);

        // EXPORT FOR MANUAL CONTROL
        window.nuclearScroll = nuclearScroll;
        window.forceContainerHeight = forceContainerHeight;

    })();

    // EMERGENCY DEBUG CONSOLE
    setTimeout(() => {
        const container = document.getElementById('chatScroll');
        console.log('🔍 FINAL CONTAINER STATE:', {
            container: container,
            exists: !!container,
            scrollHeight: container?.scrollHeight,
            clientHeight: container?.clientHeight,
            offsetHeight: container?.offsetHeight,
            children: container?.children.length,
            computedStyle: container ? window.getComputedStyle(container) : null,
            parent: container?.parentElement,
            grandParent: container?.parentElement?.parentElement
        });

        // If still broken, show big warning
        if (!container || container.clientHeight === 0) {
            console.error('💀 CRITICAL FAILURE: Chat container not rendering properly');
            alert('🚨 CHAT CRITICAL ERROR: Container has zero height. Check CSS conflicts.');
        }
    }, 3000);
</script>

<!-- EMERGENCY DEBUG OVERLAY - Add this if scrolling still doesn't work -->
<div id="chatDebug"
    style="position: fixed; top: 10px; right: 10px; background: #000; color: #0f0; padding: 10px; font-family: monospace; z-index: 9999; border: 2px solid red; display: none;">
    CHAT DEBUG:<br>
    <span id="debugInfo">Loading...</span>
</div>

<script>
    // Emergency debug - remove the display: none above to enable
    setInterval(() => {
        const scroll = document.getElementById('chatScroll');
        const debug = document.getElementById('debugInfo');

        if (scroll && debug) {
            debug.innerHTML = `
            ScrollH: ${scroll.scrollHeight}<br>
            ClientH: ${scroll.clientHeight}<br>
            ScrollT: ${scroll.scrollTop}<br>
            Diff: ${scroll.scrollHeight - scroll.clientHeight - scroll.scrollTop}
        `;

            // Auto-fix if not at bottom
            if (Math.abs(scroll.scrollHeight - scroll.clientHeight - scroll.scrollTop) > 10) {
                scroll.scrollTop = scroll.scrollHeight;
            }
        }
    }, 100);
</script>
