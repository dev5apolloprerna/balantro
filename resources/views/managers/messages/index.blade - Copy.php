{{-- managers/messages/index.blade.php --}}
@extends('layouts.super_admin')

@section('content')
    <div class="h-full w-full">

        {{-- Left column: Clients --}}
        <div class="block md:hidden h-[calc(100vh-120px)]">
            <form method="GET" action="{{ route('manager.messages.index') }}">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search clients…"
                        class="w-full rounded-lg bg-gray-800 text-sm text-gray-200 px-3 py-2 pr-8 border border-gray-700 focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </button>
                </div>
            </form>
        </div>

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
                        'messages' => $messages,
                        'clientName' => $selected_client->name,
                        'selectedClientId' => $selected_client->id,
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
@endsection
