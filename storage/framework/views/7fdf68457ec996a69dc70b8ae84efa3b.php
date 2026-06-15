<div id="chat_content">
    <?php echo $__env->make('chat_content', ['selectedClient' => $selectedClient, 'messages' => $messages], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\messages\create.blade.php ENDPATH**/ ?>