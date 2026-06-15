{{-- expects: $threads --}}
<div class="h-full bg-gray-900/40 border border-gray-800 rounded-2xl overflow-hidden flex flex-col">
    <div class="px-4 py-3 border-b border-gray-800 bg-gray-900/60">
        <div class="text-white font-semibold">Conversations</div>
    </div>

    <div class="flex-1 overflow-y-auto divide-y divide-gray-800">
        @forelse ($threads as $u)
            <a href="{{ route('client.messages.index', ['user' => $u->id]) }}"
                class="block px-4 py-3 hover:bg-gray-800/60">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                        {{ strtoupper(substr($u->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-white font-medium truncate">{{ $u->name ?? 'User' }}</div>
                        @if (!empty($u->last_message))
                            <div class="text-xs text-gray-400 truncate">{{ $u->last_message }}</div>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="p-6 text-center text-gray-400">No conversations yet.</div>
        @endforelse
    </div>
</div>
