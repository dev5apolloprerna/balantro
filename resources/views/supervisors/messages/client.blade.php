{{-- expects: $agents (Collection), $selectedAgentId (int) --}}
<div class="bg-gray-900/40 border border-gray-800 rounded-2xl px-4 py-3">

    <form method="GET" action="{{ route('supervisor.messages.index') }}" class="grid grid-cols-12 gap-3 items-center">
        {{-- Preserve current selection if present --}}
        @if (request()->filled('client_user'))
            <input type="hidden" name="client_user" value="{{ (int) request('client_user') }}">
        @endif

        {{-- Label --}}
        <label class="col-span-12 sm:col-span-2 text-xs text-gray-400">Data Entry Operator</label>

        {{-- DEO dropdown --}}
        <div class="col-span-12 sm:col-span-4">
            <select name="agent" class="w-full rounded-xl bg-gray-800 text-gray-100 border border-gray-700 px-3 py-2">
                @foreach ($agents as $a)
                    <option value="{{ $a->id }}" @selected((int) $a->id === (int) $selectedAgentId)>{{ $a->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Name search (client list filter by name) --}}
        <div class="col-span-12 sm:col-span-4">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search clients by name…"
                    class="w-full rounded-xl bg-gray-900 text-gray-100 border border-gray-700 pl-10 pr-10 py-2" />
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

                @if (request()->filled('search'))
                    <a href="{{ route('supervisor.messages.index', ['agent' => (int) $selectedAgentId]) }}"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-200"
                        title="Clear">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Apply --}}
        <div class="col-span-12 sm:col-span-2">
            <button type="submit"
                class="w-full sm:w-auto px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium">
                Apply
            </button>
        </div>
    </form>

</div>
