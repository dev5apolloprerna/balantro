
<div class="h-full flex flex-col bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden transition-colors duration-200">
    
    <div class="p-3 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
            <input type="text" placeholder="Search clients..." oninput="filterClientList(this.value)"
                class="w-full pl-9 pr-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-400 transition-colors">
        </div>
    </div>

    
    <div id="clientList" class="flex-1 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-800 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent">
        <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $isActive = !empty($selected_client) && (int) $selected_client->id === (int) $c->id;
                $lastAt = $c->last_message_at ?? ($c->updated_at ?? null);
            ?>
            
            <a href="<?php echo e(route('deo.messages.index', ['client' => $c->id])); ?>"
                class="block px-3 py-3 border-l-4 transition-all duration-200
                       <?php echo e($isActive 
                           ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-900 dark:text-indigo-100' 
                           : 'border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800/60'); ?>">
                <div class="flex items-center gap-3">
                    
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white grid place-items-center font-semibold flex-shrink-0">
                        <?php echo e(strtoupper(substr($c->client_name ?? ($c->name ?? 'U'), 0, 2))); ?>

                    </div>
                    
                    
                    <div class="flex-1 min-w-0">
                        <div class="font-medium truncate <?php echo e($isActive ? 'text-indigo-900 dark:text-indigo-100' : 'text-gray-900 dark:text-white'); ?>">
                            <?php echo e($c->client_name ?? ($c->name ?? 'Unknown')); ?>

                        </div>
                        <?php if(!empty($c->last_message)): ?>
                            <div class="text-xs truncate <?php echo e($isActive ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400'); ?>">
                                <?php echo e($c->last_message); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    
                    
                    <?php if($lastAt): ?>
                        <div class="text-xs whitespace-nowrap flex-shrink-0 <?php echo e($isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500'); ?>">
                            <?php echo e(\Carbon\Carbon::parse($lastAt)->diffForHumans()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-6 text-sm text-center text-gray-500 dark:text-gray-400">
                No clients found.
            </div>
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
        if (!hasVisibleResults) {
            if (!emptyMessage || !emptyMessage.textContent.includes('matching')) {
                document.getElementById('clientList').innerHTML = 
                    '<div class="p-6 text-sm text-center text-gray-500 dark:text-gray-400">No clients found matching your search.</div>';
            }
        } else if (emptyMessage && emptyMessage.textContent.includes('matching')) {
            // Reload to show original list
            window.location.reload();
        }
    }

    // Clear search on page load
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[placeholder="Search clients..."]');
        if (searchInput) {
            searchInput.value = '';
        }
    });
</script>

<style>
    /* Custom scrollbar for better cross-browser support */
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 3px;
    }
    
    .dark .scrollbar-thumb-gray-600::-webkit-scrollbar-thumb {
        background-color: #4b5563;
    }
    
    .scrollbar-track-transparent::-webkit-scrollbar-track {
        background: transparent;
    }

    /* Smooth transitions */
    .transition-colors {
        transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
        #clientList > a {
            padding: 12px 16px;
        }
        
        input[type="text"] {
            font-size: 16px; /* Prevents zoom on iOS */
        }
    }
</style><?php /**PATH D:\xampp\htdocs\balantro\resources\views\data_entry_operators\messages\mobile_client_list.blade.php ENDPATH**/ ?>