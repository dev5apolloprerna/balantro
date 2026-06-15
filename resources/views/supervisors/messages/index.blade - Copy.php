{{-- resources/views/supervisors/messages/index.blade.php --}}
{{-- expects: $agents, $selectedAgentId, $clients, $clientUserId, $messages, $clientName --}}

@extends('layouts.super_admin') {{-- or your layout --}}

@section('content')
    <div class="h-full w-full">
        <div class="block md:hidden h-[calc(100vh-120px)]">
            @if (!empty($selected_client))
                @include('supervisors.messages.mobile_chat_content', [
                    'selected_client' => $selected_client,
                    'messages' => $messages ?? collect(),
                ])
            @else
                @include('supervisors.messages.mobile_client_list', [
                    'clients' => $clients ?? collect(),
                    'selection_mode' => $selection_mode ?? false,
                ])
            @endif
        </div>
        <div class="hidden md:grid md:grid-cols-12 gap-6 h-[calc(100vh-140px)]">
            <aside class="md:col-span-4 h-full overflow-hidden">

                @include('supervisors.messages.client_list', [
                    'clients' => $clients,
                    'selectedAgentId' => $selectedAgentId,
                    'clientUserId' => $clientUserId,
                ])
            </aside>

            <main class="md:col-span-8 h-full overflow-hidden">
                @if ($clientUserId)
                    @include('supervisors.messages.chat_content', [
                        'messages' => $messages,
                        'clientName' => $clientName,
                        'clientUserId' => $clientUserId,
                    ])
                @else
                    @include('supervisors.messages.empty_chat')
                @endif
            </main>
        </div>
    </div>
@endsection
