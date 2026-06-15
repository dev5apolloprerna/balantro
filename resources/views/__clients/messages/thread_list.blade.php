{{-- expects: $threads (Collection of users: id, name, last_message, last_message_at, unread_count), $selected_user --}}
<div class="h-full bg-gray-900/40 border border-gray-800 rounded-2xl overflow-hidden flex flex-col">
    <div class="px-4 py-3 border-b border-gray-800 bg-gray-900/60">
        <div class="text-white font-semibold">Conversations</div>
    </div>

    <div class="flex-1 overflow-y-auto divide-y divide-gray-800">
        @forelse ($threads as $u)
            @php
                $active = !empty($selected_user) && (int) $selected_user->id === (int) $u->id;
            @endphp
            <a href="{{ route('client.messages.index', ['user' => $u->id]) }}"
                class="block px-4 py-3 hover:bg-gray-800/60 {{ $active ? 'bg-gray-800/60' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                        {{ strtoupper(substr($u->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-white font-medium truncate">{{ $u->name ?? 'User' }}</div>
                            @if (!empty($u->last_message_at))
                                <div class="text-xs text-gray-400 shrink-0">
                                    {{ \Carbon\Carbon::parse($u->last_message_at)->format('H:i') }}
                                </div>
                            @endif
                        </div>
                        @if (!empty($u->last_message))
                            <div class="text-xs text-gray-400 truncate">{{ $u->last_message }}</div>
                        @endif
                    </div>
                    @if (!empty($u->unread_count) && $u->unread_count > 0)
                        <span
                            class="ml-2 inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-indigo-600 text-white text-[11px]">
                            {{ $u->unread_count }}
                        </span>
                    @endif
                </div>
            </a>
        @empty
            <div class="p-6 text-center text-gray-400">No conversations yet.</div>
        @endforelse
    </div>
</div>
