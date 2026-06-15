@extends('layouts.super_admin')

@section('content')
    <div class="h-full w-full">
        {{-- Mobile: full-screen chat --}}
        <div class="block md:hidden h-[calc(100vh-120px)]">
            @include('clients.messages.mobile_chat_content', [
                'selected_user' => $selected_user, // support user (DEO/Manager/Supervisor)
                'messages' => $messages ?? collect(),
            ])
        </div>

        {{-- Desktop: full-width chat --}}
        <div class="hidden md:block h-[calc(100vh-140px)]">
            @if (!empty($selected_user))
                @include('clients.messages.chat_content', [
                    'selected_user' => $selected_user,
                    'messages' => $messages ?? collect(),
                ])
            @else
                @include('clients.messages.empty_chat')
            @endif
        </div>
    </div>
@endsection
