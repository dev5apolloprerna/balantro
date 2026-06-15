{{-- expects: $clients (Collection), $selected_client (Model|null) --}}
<div class="h-full bg-gray-900/40 border border-gray-800 rounded-2xl overflow-hidden flex flex-col">
    <div class="p-3 border-b border-gray-800 bg-gray-900/60">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" placeholder="Search clients..." oninput="filterClientList(this.value)"
                class="w-full pl-9 pr-3 py-2 rounded-xl bg-gray-900 text-white border border-gray-700 focus:outline-none focus:border-indigo-500">
        </div>
    </div>

    <div id="clientList" class="flex-1 overflow-y-auto divide-y divide-gray-800">
        @forelse ($clients as $c)
            @php
                $active =
                    isset($selected_client) && $selected_client && (int) $selected_client->id === (int) $c->client_id;
            @endphp
            <a href="{{ route('manager.messages.index', ['client' => $c->client_id, 'search' => request('search')]) }}"
                class="block px-3 py-2 border-b border-gray-800 hover:bg-gray-800/60 {{ $active ? 'bg-gray-800/90' : '' }}">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                        {{ strtoupper(mb_substr($c->client_name ?? 'C', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-sm truncate">{{ $c->client_name }}</div>
                        @if (!empty($c->last_message))
                            <div class="text-xs text-gray-400 truncate">{{ $c->last_message }}</div>
                        @endif
                    </div>
                    @if (!empty($c->last_message_at))
                        <div class="text-[10px] text-gray-500 ml-1 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($c->last_message_at)->diffForHumans(null, true) }}
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="p-3 text-sm text-gray-400">No clients found.</div>
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
