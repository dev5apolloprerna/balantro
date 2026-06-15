
<div class="h-full flex flex-col bg-white dark:bg-black border border-gray-300 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm dark:shadow-none">
    
    <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
        <div class="text-gray-900 dark:text-white font-medium text-lg mb-3">Clients</div>
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400"></i>
            <input type="text" placeholder="Search clients..." oninput="filterClientList(this.value)"
                class="w-full pl-10 pr-3 py-3 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-400 text-sm placeholder-gray-500 dark:placeholder-gray-400">
        </div>
    </div>

    
    <div id="clientList" class="flex-1 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-800">
        <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('manager.messages.index', ['client' => $c->client_id])); ?>"
                class="block px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                        <?php echo e(strtoupper(substr($c->client_name ?? 'U', 0, 2))); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-gray-900 dark:text-white font-medium text-base"><?php echo e($c->client_name ?? 'Unknown Client'); ?></div>
                        <?php if(!empty($c->last_message)): ?>
                            <div class="text-sm text-gray-600 dark:text-gray-400 truncate mt-1"><?php echo e($c->last_message); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($c->last_message_at)): ?>
                        <div class="text-xs text-gray-500 dark:text-gray-500 whitespace-nowrap">
                            <?php echo e(\Carbon\Carbon::parse($c->last_message_at)->diffForHumans()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="flex-1 grid place-items-center text-gray-500 dark:text-gray-400 p-6">
                <div class="text-center">
                    <i class="fa-regular fa-comments text-2xl mb-2"></i>
                    <div>No clients found</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function filterClientList(q) {
        q = (q || '').toLowerCase();
        const rows = document.querySelectorAll('#clientList > a');
        rows.forEach(r => {
            const text = r.textContent.toLowerCase();
            r.style.display = text.includes(q) ? '' : 'none';
        });
    }
</script><?php /**PATH D:\xampp\htdocs\balantro\resources\views\managers\messages\mobile_client_list.blade.php ENDPATH**/ ?>