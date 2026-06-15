<?php if($isMobile): ?>
  <turbo-stream target="mobile_chat">
    <template>
      <?php echo $__env->make('mobile-chat-content', ['selectedClient' => $receiver, 'messages' => $messages], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </template>
  </turbo-stream>
<?php else: ?>
  <turbo-stream target="chat_content">
    <template>
      <?php echo $__env->make('chat-content', ['selectedClient' => $receiver, 'messages' => $messages], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </template>
  </turbo-stream>
<?php endif; ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\supervisors\messages\create-turbo-stream.blade.php ENDPATH**/ ?>