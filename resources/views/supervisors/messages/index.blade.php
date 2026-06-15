{{-- resources/views/supervisors/messages/index.blade.php --}}
@extends('layouts.super_admin')

@section('content')
    <div class="h-full w-full">
        {{-- Mobile View --}}
        <div class="block md:hidden h-[calc(100vh-120px)]">
            @if ($clientUserId)
                {{-- Mobile Chat View --}}
                <div class="h-full flex flex-col bg-white dark:bg-black border border-gray-300 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm dark:shadow-none">
                    @include('supervisors.messages.mobile_chat_content', [
                        'clients' => $clients ?? collect(),
                        'selected_client' => $selected_client ?? null,
                        'messages' => $messages ?? collect(),
                        'clientUserId' => $clientUserId,
                        'clientName' => $clientName,
                    ])
                </div>
            @else
                {{-- Mobile Client List --}}
                <div class="h-full flex flex-col bg-white dark:bg-black border border-gray-300 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm dark:shadow-none">
                    {{-- Header --}}
                    <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
                        <div class="text-gray-900 dark:text-white font-medium text-lg mb-3">Clients</div>
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
                            <input type="text" placeholder="Search clients..." oninput="filterClientList(this.value)"
                                class="w-full pl-10 pr-3 py-3 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-400 text-sm placeholder-gray-500 dark:placeholder-gray-400">
                        </div>
                    </div>

                    {{-- Client List --}}
                    <div id="clientList" class="flex-1 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($clients as $c)
                            <a href="{{ route('supervisor.messages.index', ['client' => $c->client_user_id]) }}"
                                class="block px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ !empty($clientUserId) && (int) $clientUserId === (int) $c->client_user_id ? 'bg-indigo-50 dark:bg-indigo-900/20 border-r-2 border-indigo-500' : '' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                                        {{ strtoupper(substr($c->client_name ?? 'U', 0, 2)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-gray-900 dark:text-white font-medium text-base">
                                            {{ $c->client_name ?? 'Unknown Client' }}</div>
                                        @if (!empty($c->last_message))
                                            <div class="text-sm text-gray-600 dark:text-gray-400 truncate mt-1">{{ $c->last_message }}</div>
                                        @endif
                                    </div>
                                    @if (!empty($c->last_message_at))
                                        <div class="text-xs text-gray-500 dark:text-gray-500 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($c->last_message_at)->diffForHumans() }}
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="flex-1 grid place-items-center text-gray-500 dark:text-gray-400 p-6">
                                <div class="text-center">
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
                @include('supervisors.messages.client_list', [
                    'clients' => $clients ?? collect(),
                    'clientUserId' => $clientUserId ?? null,
                ])
            </aside>

            <main class="md:col-span-8 h-full overflow-hidden">
                @if ($clientUserId)
                    @include('supervisors.messages.chat_content', [
                        'selected_client' => $selected_client ?? null,
                        'messages' => $messages ?? collect(),
                        'clientName' => $clientName,
                        'clientUserId' => $clientUserId,
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