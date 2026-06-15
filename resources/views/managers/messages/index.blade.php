{{-- managers/messages/index.blade.php --}}
@extends('layouts.super_admin')

@section('content')
    <div class="h-full w-full">
        {{-- Mobile View --}}
        <div class="block md:hidden h-[calc(100vh-120px)]">
            @if (request()->has('client') && $selected_client)
                {{-- Mobile Chat View --}}
                <div class="h-full flex flex-col bg-white dark:bg-black border border-gray-300 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm dark:shadow-none">
                    @include('managers.messages.mobile_chat_content', [
                        'clients' => $clients ?? collect(),
                        'selected_client' => $selected_client ?? null,
                        'messages' => $messages ?? collect(),
                    ])
                </div>
            @else
                {{-- Mobile Client List --}}
                @include('managers.messages.mobile_client_list', [
                    'clients' => $clients ?? collect(),
                ])
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden md:grid md:grid-cols-12 gap-6 h-[calc(100vh-140px)]">
            <aside class="md:col-span-4 h-full overflow-hidden">
                @include('managers.messages.client_list', [
                    'clients' => $clients ?? collect(),
                    'selected_client' => $selected_client ?? null,
                ])
            </aside>

            <main class="md:col-span-8 h-full overflow-hidden">
                @if ($selected_client)
                    @include('managers.messages.chat_content', [
                        'selected_client' => $selected_client,
                        'messages' => $messages ?? collect(),
                    ])
                @else
                    <div class="flex-1 grid place-items-center text-gray-500 dark:text-gray-400 bg-white dark:bg-black border border-gray-300 dark:border-gray-800 rounded-2xl shadow-sm dark:shadow-none">
                        <div class="text-center">
                            <i class="fa-regular fa-comments text-4xl mb-3"></i>
                            <div>Select a client to start chatting</div>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>

    {{-- Auto-scroll Script --}}
    <script>
        function scrollToBottom(containerId) {
            const chatContainer = document.getElementById(containerId);
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }

        // Scroll on initial load for both mobile and desktop
        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom('mobileChatMessages');
            scrollToBottom('desktopChatMessages');
        });

        // Auto-scroll when window is resized
        window.addEventListener('resize', function() {
            setTimeout(() => {
                scrollToBottom('mobileChatMessages');
                scrollToBottom('desktopChatMessages');
            }, 100);
        });

        // Mobile client list filtering
        function filterClientList(q) {
            q = (q || '').toLowerCase();
            const rows = document.querySelectorAll('#clientList > a');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        }

        // Auto-scroll when new messages are added
        function observeChatChanges(containerId) {
            const chatContainer = document.getElementById(containerId);
            if (chatContainer) {
                const observer = new MutationObserver(function() {
                    scrollToBottom(containerId);
                });
                observer.observe(chatContainer, {
                    childList: true,
                    subtree: true
                });
            }
        }

        // Initialize observers
        document.addEventListener('DOMContentLoaded', function() {
            observeChatChanges('mobileChatMessages');
            observeChatChanges('desktopChatMessages');
        });
    </script>
@endsection