<?php
    // In Laravel, we'll use a Livewire component or regular controller to handle this
    // This would be replaced with Livewire component rendering or similar
?>

<div id="chat_content">
    <?php echo $__env->make('client.messages.message', ['manager' => $manager, 'messages' => $messages], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\client\_messages\create.turbo_stream.blade.php ENDPATH**/ ?>