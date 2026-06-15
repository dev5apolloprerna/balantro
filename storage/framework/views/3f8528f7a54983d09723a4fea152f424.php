<?php $__env->startSection('content'); ?>
    <div class="h-full w-full">
        
        <div class="block md:hidden h-[calc(100vh-120px)]">
            <?php echo $__env->make('clients.messages.mobile_chat_content', [
                'selected_user' => $selected_user, // support user (DEO/Manager/Supervisor)
                'messages' => $messages ?? collect(),
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        
        <div class="hidden md:block h-[calc(100vh-140px)]">
            <?php if(!empty($selected_user)): ?>
                <?php echo $__env->make('clients.messages.chat_content', [
                    'selected_user' => $selected_user,
                    'messages' => $messages ?? collect(),
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php else: ?>
                <?php echo $__env->make('clients.messages.empty_chat', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\__clients\messages\index copy.blade.php ENDPATH**/ ?>