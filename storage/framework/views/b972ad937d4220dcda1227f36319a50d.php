
<div class="h-full theme-bg-secondary border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden flex flex-col theme-transition">
    
    <div class="p-3 border-b border-gray-200 dark:border-gray-700 theme-bg-secondary">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 theme-text-tertiary"></i>
            <input type="text" placeholder="Search clients..." oninput="filterClientList(this.value)"
                class="w-full pl-9 pr-3 py-2 rounded-xl theme-bg-tertiary theme-text-primary border border-gray-200 dark:border-gray-700 focus:outline-none focus:border-indigo-500">
        </div>
    </div>

    
    <div id="clientList" class="flex-1 overflow-y-auto theme-scrollbar">
        <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $isActive = !empty($selected_client) && (int) $selected_client->id === (int) $c->id;
                $lastAt = $c->last_message_at ?? ($c->updated_at ?? null);
            ?>
            
            <a href="<?php echo e(route('deo.messages.index', ['client' => $c->id])); ?>"
                class="block px-3 py-3 border-l-4 theme-transition
                       <?php echo e($isActive ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-transparent hover:bg-gray-100 dark:hover:bg-gray-800/60'); ?>">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold">
                        <?php echo e(strtoupper(substr($c->client_name ?? ($c->name ?? 'U'), 0, 2))); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="theme-text-primary font-medium truncate">
                            <?php echo e($c->client_name ?? ($c->name ?? 'Unknown')); ?>

                        </div>
                        <?php if(!empty($c->last_message)): ?>
                            <div class="text-xs theme-text-secondary truncate"><?php echo e($c->last_message); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if($lastAt): ?>
                        <div class="text-xs theme-text-tertiary whitespace-nowrap">
                            <?php echo e(\Carbon\Carbon::parse($lastAt)->diffForHumans()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-6 text-sm theme-text-tertiary">No clients found.</div>
        <?php endif; ?>
    </div>
</div>

<script>
    function filterClientList(q) {
        q = (q || '').toLowerCase();
        const rows = document.querySelectorAll('#clientList > a');
        let hasVisibleResults = false;
        
        rows.forEach(r => {
            const text = r.textContent.toLowerCase();
            const isVisible = text.includes(q);
            r.style.display = isVisible ? '' : 'none';
            
            if (isVisible) hasVisibleResults = true;
        });

        // Show "No results" message if needed
        const emptyMessage = document.querySelector('#clientList > .p-6');
        if (!hasVisibleResults && !emptyMessage) {
            document.getElementById('clientList').innerHTML = 
                '<div class="p-6 text-sm theme-text-tertiary text-center">No clients found matching your search.</div>';
        } else if (hasVisibleResults && emptyMessage && emptyMessage.textContent.includes('matching')) {
            // Reload the original list if we have results but were showing "no matching results"
            location.reload();
        }
    }

    // Optional: Clear search on page load or when navigating away
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[placeholder="Search clients..."]');
        if (searchInput) {
            searchInput.value = '';
        }
    });
</script><?php /**PATH D:\xampp\htdocs\balantro\resources\views/data_entry_operators/messages/client_list.blade.php ENDPATH**/ ?>