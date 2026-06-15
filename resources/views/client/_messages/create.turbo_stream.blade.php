@php
    // In Laravel, we'll use a Livewire component or regular controller to handle this
    // This would be replaced with Livewire component rendering or similar
@endphp

<div id="chat_content">
    @include('client.messages.message', ['manager' => $manager, 'messages' => $messages])
</div>