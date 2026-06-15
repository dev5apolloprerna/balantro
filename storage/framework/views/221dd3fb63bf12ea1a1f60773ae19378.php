<style>
    .nav-item.active {
        border-right: 1px solid rgba(34, 211, 238, 0.5)
    }
    /* Default (expanded) */
.submenu {
    margin-left: 1rem; /* ml-4 */
}

/* Collapsed state */
.sidebar.collapsed .submenu {
    margin-left: 4rem !important; /* ml-16 */
}

/*----------------------------*/
/* =========================
   COLLAPSED REPORT FIX
========================= */

.sidebar.collapsed .reports-dropdown summary {
    justify-content: center !important;
    align-items: center !important;
    padding: 12px 0 !important;
    position: relative;
    min-height: 54px;
}

.sidebar.collapsed .reports-dropdown summary > div {
    justify-content: center !important;
    width: 100%;
}

.sidebar.collapsed .reports-dropdown img {
    margin: 0 auto;
}

/* hide text */
.sidebar.collapsed .reports-dropdown .nav-text {
    display: none !important;
}

/* hide chevron */
/* .sidebar.collapsed .reports-dropdown .fa-chevron-down {
    display: none !important;
} */
/* collapsed chevron */
.sidebar.collapsed .reports-dropdown .fa-chevron-down {
    display: block !important;
    position: absolute;
    bottom: 6px;
    right: 6px;
    font-size: 9px;
    opacity: 0.7;
}
/* submenu popup style */
.sidebar.collapsed .reports-dropdown details[open] .submenu {

    position: fixed;
    left: 82px;
    top: 220px;
    width: 200px;
    margin-left: 0 !important;
    padding: 14px;
    border-radius: 18px;
    background: rgba(10, 10, 15, 0.92);
    backdrop-filter: blur(30px);
    -webkit-backdrop-filter: blur(30px);
    border: 1px solid rgba(34,211,238,0.18);
    box-shadow:
        0 10px 40px rgba(0,0,0,0.55),
        0 0 25px rgba(34,211,238,0.12);
    z-index: 999999 !important;
    overflow: hidden;
}
.sidebar.collapsed .submenu li a{
    color: #ffffff !important;
    font-size: 16px;
    padding: 12px 16px !important;
    border-radius: 12px;
    display: flex;
    align-items: center;
    transition: all .25s ease;
}
.sidebar.collapsed .submenu li a:hover{
    background: rgba(34,211,238,0.14);
    color: #22d3ee !important;
    transform: translateX(4px);
}
.sidebar.collapsed .submenu li a.active{
    background: rgba(34,211,238,0.18);
    border: 1px solid rgba(34,211,238,0.22);
    color: #22d3ee !important;
}
.sidebar.collapsed .reports-dropdown details{
    overflow: visible !important;
}

.sidebar.collapsed .reports-dropdown{
    overflow: visible !important;
}
</style>

<ul class="space-y-2 px-2 md:px-2 sidebar-list">
    <li>
        <a href="<?php echo e(route('home')); ?>" data-tooltip="Dashboard" title="Dashboard"
            class="nav-item flex items-center justify-start px-3 py-3 gap-3 box-border rounded-lg text-black dark:text-gray-300 hover:bg-white/10  theme-transition transition-all duration-300  <?php echo e(Route::is('home')
                ? 'active 
                        bg-gradient-to-r from-[#22d3ee]/20 via-[#22d3ee]/10 to-transparent
                        border border-[#22d3ee]/50
                         text-black dark:text-white'
                : ''); ?>">
            
            <img src="<?php echo e(asset('assets/images/dashboard.png')); ?>" class="w-6">
            <span class="nav-text flex-1 text-black dark:text-white ">Dashboard</span>
            <?php if(Route::is('home')): ?>
                <span class="absolute inset-0 bg-cyan-400/10 blur-md opacity-20 pointer-events-none"></span>
            <?php endif; ?>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('documents.index')); ?>" data-tooltip="Documents" 
            class="nav-item flex items-center justify-start px-3 py-3 gap-3 box-border rounded-lg text-black dark:text-gray-300 hover:bg-white/10  theme-transition transition-all duration-300  <?php echo e(Route::is('documents.*')
                ? 'active
                        bg-gradient-to-r from-[#22d3ee]/20 via-[#22d3ee]/10 to-transparent
                        border border-[#22d3ee]/50
            shadow-[0_0_10px_#22d3ee40] text-black dark:text-white'
                : ''); ?>">
            
            <img src="<?php echo e(asset('assets/images/document.png')); ?>" class="w-6">
            <span class="nav-text flex-1 text-black dark:text-white">Documents</span>
            <?php if(Route::is('documents')): ?>
                <span class="absolute inset-0 bg-cyan-400/10 blur-md opacity-20 pointer-events-none"></span>
            <?php endif; ?>
        </a>
    </li>
    <!-- <?php echo e(Route::is('reports.*') ? 'open' : ''); ?> -->
    <li class="relative reports-dropdown <?php echo e(Route::is('reports.*') ? 'active' : ''); ?>">
        <details class="group">
            <summary data-tooltip="Reports" title="Reports"
                class="cursor-pointer w-full list-none flex items-center justify-between px-3 py-3 rounded-lg text-black dark:text-gray-300 hover:bg-white/10  theme-transition transition-all duration-300  <?php echo e(Route::is('reports.*') ? 'active' : ''); ?>">
                <div class="flex items-center gap-3">
                    <!-- <i class="fas fa-file-alt text-xl w-6 text-center drop-shadow-[0_0_6px_#22d3ee]"></i> -->
                    
                    <img src="<?php echo e(asset('assets/images/reports.png')); ?>" class="w-6">
                    <span class="nav-text flex-1 text-black dark:text-white">Reports</span>
                </div>
                <i class="fas fa-chevron-down text-xs opacity-70 transition-transform group-open:rotate-180"></i>
            </summary>

            <ul class="submenu  ml-4 mt-2 space-y-1 p-2 min-w-[180px]">
                <li>
                    <a href="<?php echo e(route('reports.balance_sheet')); ?>" title="Balance Sheet"
                        class="flex items-center gap-2 px-3 py-2 rounded-md text-black dark:text-gray-300 whitespace-nowrap before:w-1 before:h-4 before:bg-cyan-400 before:rounded before:mr-2 before:opacity-0 hover:before:opacity-100 dark:text-gray-300 hover:bg-white/10  theme-transition transition-all duration-300  <?php echo e(Route::is('reports.balance_sheet')
                            ? 'active bg-white/20 dark:bg-white/10 
                        border border-cyan-400/30 
                        shadow-[0_0_10px_#22d3ee40] text-black dark:text-white'
                            : ''); ?>">
                        Balance Sheet
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('reports.pl')); ?>" title="Profit & Loss"
                        class="flex items-center gap-2 px-3 py-2 rounded-md text-black dark:text-gray-300 whitespace-nowrap before:w-1 before:h-4 before:bg-cyan-400 before:rounded before:mr-2 before:opacity-0 hover:before:opacity-100 dark:text-gray-300 hover:bg-white/10  theme-transition transition-all duration-300  <?php echo e(Route::is('reports.pl')
                            ? 'active bg-white/20 dark:bg-white/10 
                        border border-cyan-400/30 
                        shadow-[0_0_10px_#22d3ee40] text-black dark:text-white'
                            : ''); ?>">
                        Profit & Loss
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('reports.ledger')); ?>" title="All Ledger"
                        class="flex items-center gap-2 px-3 py-2 rounded-md text-black dark:text-gray-300 whitespace-nowrap before:w-1 before:h-4 before:bg-cyan-400 before:rounded before:mr-2 before:opacity-0 hover:before:opacity-100 dark:text-gray-300 hover:bg-white/10  theme-transition transition-all duration-300  <?php echo e(Route::is('reports.ledger')
                            ? 'active bg-white/20 dark:bg-white/10 
                        border border-cyan-400/30 
                        shadow-[0_0_10px_#22d3ee40] text-black dark:text-white'
                            : ''); ?>">
                        All Ledger
                    </a>
                </li>
            </ul>
        </details>
    </li>
    <li>
        <a href="<?php echo e(route('clients.suspense')); ?>" data-tooltip="Bank Suspense"
            class="nav-item flex items-center justify-start px-3 py-3 gap-3 box-border rounded-lg text-black dark:text-gray-300 hover:bg-white/10  theme-transition transition-all duration-300  <?php echo e(Route::is('clients.suspense')
                ? 'active 
                         bg-gradient-to-r from-[#22d3ee]/20 via-[#22d3ee]/10 to-transparent
                        border border-[#22d3ee]/50
            shadow-[0_0_10px_#22d3ee40] text-black dark:text-white'
                : ''); ?>">
            <img src="<?php echo e(asset('assets/images/action.png')); ?>" class="w-6">
            
            <span class="nav-text flex-1 text-black dark:text-white">Action</span>
            <?php if(Route::is('clients.suspense')): ?>
                <span class="absolute inset-0 bg-cyan-400/10 blur-md opacity-20 pointer-events-none"></span>
            <?php endif; ?>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('client.messages.index')); ?>" data-tooltip="Chat"
            class="nav-item flex items-center justify-start px-3 py-3 gap-3 box-border rounded-lg text-black dark:text-gray-300 hover:bg-white/10  theme-transition transition-all duration-300  <?php echo e(Route::is('client.*')
                ? 'active
                        bg-gradient-to-r from-[#22d3ee]/20 via-[#22d3ee]/10 to-transparent
                        border border-[#22d3ee]/50
            shadow-[0_0_10px_#22d3ee40] text-black dark:text-white'
                : ''); ?>">
            <img src="<?php echo e(asset('assets/images/chat.png')); ?>" class="w-6">
            
            <span class="nav-text flex-1 text-black dark:text-white">Chat</span>
            <?php if(Route::is('client.*')): ?>
                <span class="absolute inset-0 bg-cyan-400/10 blur-md opacity-20 pointer-events-none"></span>
            <?php endif; ?>
        </a>
    </li>
</ul>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportsDropdown = document.querySelector('.reports-dropdown');
        if (!reportsDropdown) return;

        const details = reportsDropdown.querySelector('details');
        const summary = reportsDropdown.querySelector('summary');

        // Remove no-transition class after page load
        setTimeout(() => {
            document.documentElement.classList.remove('no-transition');
        }, 100);

        // Fix click issue in collapsed state
        summary.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const isCollapsed = sidebar && sidebar.classList.contains('collapsed');

            if (isCollapsed) {
                e.preventDefault(); // only block native toggle in collapsed mode

                // Toggle dropdown manually
                if (details.hasAttribute('open')) {
                    details.removeAttribute('open');
                } else {
                    details.setAttribute('open', '');
                }
            }
        });

        // Handle dropdown closing when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInside = reportsDropdown.contains(event.target);
            if (!isClickInside) {
                details.removeAttribute('open');
            }
        });

        // Close dropdown when a link is clicked
        document.querySelectorAll('.reports-dropdown a').forEach(link => {
            link.addEventListener('click', function() {
                // Small delay to allow navigation before closing
                setTimeout(() => {
                    details.removeAttribute('open');
                }, 100);
            });
        });

        // Handle sidebar collapse state changes
        function handleSidebarCollapse() {
            const sidebar = document.querySelector('.sidebar');
            if (!sidebar) return;

            const isCollapsed = sidebar.classList.contains('collapsed');

            if (isCollapsed) {
                document.body.classList.add('sidebar-collapsed');
                // Ensure dropdown is closed when collapsing
                details.removeAttribute('open');
            } else {
                document.body.classList.remove('sidebar-collapsed');
            }
        }

        // Initialize
        handleSidebarCollapse();

        // Watch for sidebar changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    handleSidebarCollapse();
                }
            });
        });

        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            observer.observe(sidebar, {
                attributes: true
            });
        }

        // Auto-close dropdown when sidebar is collapsed
        const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                setTimeout(() => {
                    const sidebar = document.querySelector('.sidebar');
                    if (sidebar && sidebar.classList.contains('collapsed')) {
                        details.removeAttribute('open');
                    }
                }, 300);
            });
        }
    });
</script>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\navigations\client_nav.blade.php ENDPATH**/ ?>