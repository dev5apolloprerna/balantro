{{-- client/messages/index.blade.php --}}
@extends('layouts.super_admin')

@section('content')
    <div class="h-full w-full">
        <div class="hidden md:grid md:grid-cols-12 gap-6 h-[calc(100vh-140px)]">
            <main class="md:col-span-12 h-full overflow-hidden">
                @if (!empty($selected_user))
                    @include('client.messages.chat_content', [
                        'selected_user' => $selected_user,
                        'messages' => $messages ?? collect(),
                    ])
                @else
                    @include('client.messages.empty_chat')
                @endif
            </main>
        </div>

        {{-- Mobile View --}}
        <div class="block md:hidden h-[calc(100vh-120px)]">
            @include('client.messages.mobile_chat_content', [
                'selected_user' => $selected_user,
                'messages' => $messages ?? collect(),
            ])
        </div>
    </div>
@endsection

<style>
    /* Mobile-specific fixes */
    @media (max-width: 768px) {
        .mobile-chat-wrapper {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .mobile-messages-container {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            /* Smooth scrolling on iOS */
            min-height: 0;
            /* Crucial for flex scrolling */
        }

        /* Force scroll to bottom on mobile */
        .mobile-scroll-fix {
            overflow-anchor: auto;
        }
    }
</style>
