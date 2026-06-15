@if($isMobile)
  <turbo-stream target="mobile_chat">
    <template>
      @include('mobile-chat-content', ['selectedClient' => $receiver, 'messages' => $messages])
    </template>
  </turbo-stream>
@else
  <turbo-stream target="chat_content">
    <template>
      @include('chat-content', ['selectedClient' => $receiver, 'messages' => $messages])
    </template>
  </turbo-stream>
@endif