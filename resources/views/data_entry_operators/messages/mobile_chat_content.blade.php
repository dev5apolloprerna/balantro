{{-- mobile full-screen chat (expects: $selected_client, $messages) --}}
<div class="h-full flex flex-col bg-gray-950/60">
    <div class="flex items-center gap-2 p-3 border-b border-gray-800">
        <a href="{{ route('deo.messages.index') }}" class="p-2 rounded-lg hover:bg-gray-800 text-gray-200">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="w-8 h-8 rounded-full bg-indigo-600 text-white grid place-items-center text-sm font-semibold">
            {{ strtoupper(substr($selected_client->name ?? 'U', 0, 2)) }}
        </div>
        <div class="text-white font-medium">{{ $selected_client->name ?? 'Unknown' }}</div>
        <div class="ml-auto"></div>
    </div>

    @include('data_entry_operators.messages.chat_content', [
        'selected_client' => $selected_client,
        'messages' => $messages,
    ])
</div>
