
<div class="h-full bg-gray-900/40 border border-gray-800 rounded-2xl overflow-hidden flex flex-col">
    <div class="px-4 py-3 border-b border-gray-800 bg-gray-900/60">
        <div class="text-white font-semibold">Conversations</div>
    </div>

    <div class="flex-1 overflow-y-auto divide-y divide-gray-800">
        <?php $__empty_1 = true; $__currentLoopData = $threads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $active = !empty($selected_user) && (int) $selected_user->id === (int) $u->id;
            ?>
            <a href="<?php echo e(route('client.messages.index', ['user' => $u->id])); ?>"
                class="block px-4 py-3 hover:bg-gray-800/60 <?php echo e($active ? 'bg-gray-800/60' : ''); ?>">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                        <?php echo e(strtoupper(substr($u->name ?? 'U', 0, 2))); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-white font-medium truncate"><?php echo e($u->name ?? 'User'); ?></div>
                            <?php if(!empty($u->last_message_at)): ?>
                                <div class="text-xs text-gray-400 shrink-0">
                                    <?php echo e(\Carbon\Carbon::parse($u->last_message_at)->format('H:i')); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if(!empty($u->last_message)): ?>
                            <div class="text-xs text-gray-400 truncate"><?php echo e($u->last_message); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($u->unread_count) && $u->unread_count > 0): ?>
                        <span
                            class="ml-2 inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-indigo-600 text-white text-[11px]">
                            <?php echo e($u->unread_count); ?>

                        </span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-6 text-center text-gray-400">No conversations yet.</div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\client\messages\thread_list.blade.php ENDPATH**/ ?>