<?php $__env->startSection('content'); ?>
    <div class="h-full w-full">
        <div class="hidden md:grid md:grid-cols-12 gap-6 h-[calc(100vh-140px)]">
            <main class="md:col-span-12 h-full overflow-hidden">
                <?php if(!empty($selected_user)): ?>
                    <?php echo $__env->make('client.messages.chat_content', [
                        'selected_user' => $selected_user,
                        'messages' => $messages ?? collect(),
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php else: ?>
                    <?php echo $__env->make('client.messages.empty_chat', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>
            </main>
        </div>

        
        <div class="block md:hidden h-[calc(100vh-120px)]">
            <?php echo $__env->make('client.messages.mobile_chat_content', [
                'selected_user' => $selected_user,
                'messages' => $messages ?? collect(),
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

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

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\client\messages\index.blade.php ENDPATH**/ ?>