{{-- data_entry_operators/messages/index.blade.php --}}
@extends('layouts.super_admin')

@section('content')
    <div class="h-full w-full">
        {{-- Mobile View --}}
        {{-- Mobile View --}}
<div class="block md:hidden h-[calc(100vh-120px)]">
    @if ($selected_client)
        {{-- Mobile Chat View --}}
        <div class="h-full flex flex-col bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden transition-colors">
            @include('data_entry_operators.messages.mobile_chat_content', [
                'clients' => $clients ?? collect(),
                'selected_client' => $selected_client ?? null,
            ])
        </div>
    @else
        {{-- Mobile Client List --}}
        <div class="h-full flex flex-col bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden transition-colors">
            {{-- Header --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                <div class="text-gray-900 dark:text-white font-medium text-lg mb-3">Clients</div>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                    <input type="text" placeholder="Search clients..." oninput="filterClientList(this.value)"
                        class="w-full pl-10 pr-3 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-400 text-sm transition-colors">
                </div>
            </div>

            {{-- Client List --}}
            <div id="clientList" class="flex-1 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($clients as $c)
                    @php
                        $isActive = !empty($selected_client) && (int) $selected_client->id === (int) $c->id;
                    @endphp
                    
                    <a href="{{ route('deo.messages.index', ['client' => $c->id]) }}"
                        class="block px-4 py-4 border-l-4 transition-colors
                               {{ $isActive 
                                   ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-900 dark:text-indigo-100' 
                                   : 'border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800/60' 
                               }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold flex-shrink-0">
                                {{ strtoupper(substr($c->name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-base truncate {{ $isActive ? 'text-indigo-900 dark:text-indigo-100' : 'text-gray-900 dark:text-white' }}">
                                    {{ $c->name ?? 'Unknown Client' }}
                                </div>
                                @if (!empty($c->last_message))
                                    <div class="text-sm truncate mt-1 {{ $isActive ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $c->last_message }}
                                    </div>
                                @endif
                            </div>
                            @if (!empty($c->last_message_at))
                                <div class="text-xs whitespace-nowrap flex-shrink-0 {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ \Carbon\Carbon::parse($c->last_message_at)->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="flex-1 grid place-items-center p-6">
                        <div class="text-center text-gray-500 dark:text-gray-400">
                            <i class="fa-regular fa-comments text-2xl mb-2"></i>
                            <div>No clients found</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>

        {{-- Desktop View --}}
        <div class="hidden md:grid md:grid-cols-12 gap-6 h-[calc(100vh-140px)]">
            <aside class="md:col-span-4 h-full overflow-hidden">
                @include('data_entry_operators.messages.client_list', [
                    'clients' => $clients ?? collect(),
                    'selected_client' => $selected_client ?? null,
                ])
            </aside>

            <main class="md:col-span-8 h-full overflow-hidden">
                @if ($selected_client)
                    @include('data_entry_operators.messages.chat_content', [
                        'selected_client' => $selected_client,
                        'messages' => $messages ?? collect(),
                    ])
                @else
                    <div class="flex-1 grid place-items-center text-gray-400">
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
                console.log('Scrolling to bottom of:', containerId, 'Scroll height:', chatContainer.scrollHeight);
                chatContainer.scrollTop = chatContainer.scrollHeight;
            } else {
                console.log('Container not found:', containerId);
            }
        }

        // Scroll on initial load for both mobile and desktop
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll mobile chat
            scrollToBottom('mobileChatMessages');

            // Scroll desktop chat - use multiple attempts with delays
            setTimeout(() => {
                scrollToBottom('desktopChatMessages');
            }, 100);

            setTimeout(() => {
                scrollToBottom('desktopChatMessages');
            }, 500);

            setTimeout(() => {
                scrollToBottom('desktopChatMessages');
            }, 1000);
        });

        // Auto-scroll when window is resized (handles layout changes)
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

        // Auto-scroll when new messages are added (for real-time updates)
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
            } else {
                // Retry after a delay if container not found
                setTimeout(() => {
                    observeChatChanges(containerId);
                }, 500);
            }
        }

        // Initialize observers after DOM load with delays
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile observer
            setTimeout(() => {
                observeChatChanges('mobileChatMessages');
            }, 100);

            // Desktop observer with multiple attempts
            setTimeout(() => {
                observeChatChanges('desktopChatMessages');
            }, 500);

            setTimeout(() => {
                observeChatChanges('desktopChatMessages');
            }, 1000);
        });

        // Additional scroll trigger when navigating between clients
        window.addEventListener('load', function() {
            setTimeout(() => {
                scrollToBottom('desktopChatMessages');
            }, 300);
        });
    </script>
@endsection
