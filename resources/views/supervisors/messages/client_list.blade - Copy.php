{{-- expects: $clients, $selectedAgentId, $clientUserId --}}
<div class="h-full bg-gray-900/40 border border-gray-800 rounded-2xl overflow-hidden flex flex-col">
    <div class="p-3 border-b border-gray-800 bg-gray-900/60">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" placeholder="Search clients..." oninput="filterClientList(this.value)"
                class="w-full pl-9 pr-3 py-2 rounded-xl bg-gray-900 text-white border border-gray-700 focus:outline-none focus:border-indigo-500">
        </div>
    </div>

    <div id="clientList" class="flex-1 overflow-y-auto divide-y divide-gray-800">

        @forelse($clients as $c)
            @php
                $active = (int) $clientUserId === (int) $c->client_user_id;
                $lastMsg = $c->last_message
                    ? \Illuminate\Support\Str::limit($c->last_message, 36)
                    : 'Start a conversation';
            @endphp
            <a href="{{ route('supervisor.messages.index', ['client' => $c->client_user_id]) }}"
                class="block px-3 py-3 hover:bg-gray-800/60 {{ $active ? 'bg-gray-800/70' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                        {{ strtoupper(substr($c->client_name ?? ($c->client_name ?? 'U'), 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-white font-medium truncate">
                            {{ $c->client_name ?? ($c->client_name ?? 'Unknown') }}
                        </div>
                        @if (!empty($c->last_message))
                            <div class="text-xs text-gray-400 truncate">{{ $c->last_message }}</div>
                        @endif
                    </div>
                    @if ($c->last_message_at)
                        <div class="text-xs text-gray-500 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($c->last_message_at)->diffForHumans() }}
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <li class="px-3 py-8 text-center text-gray-400">No clients found.</li>
        @endforelse
    </div>
</div>

<script>
    function filterClientList(q) {
        q = (q || '').toLowerCase();
        const rows = document.querySelectorAll('#clientList > a');
        rows.forEach(r => {
            const text = r.textContent.toLowerCase();
            r.style.display = text.includes(q) ? '' : 'none';
        });
    }
</script>
