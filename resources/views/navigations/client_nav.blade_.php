<style>
    [x-cloak] {
        display: none !important
    }

    /* Dropdown styles for both states */
    .reports-dropdown details ul {
        transition: all 0.3s ease;
    }

    /* Expanded sidebar - normal dropdown */
    .sidebar:not(.collapsed) .reports-dropdown details ul {
        position: static;
        margin-left: 2rem;
        margin-top: 0.25rem;
    }

    /* Collapsed sidebar - popout dropdown */
    .sidebar.collapsed .reports-dropdown details ul {
        position: absolute;
        left: 100%;
        top: 0;
        min-width: 200px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 50;
        margin-left: 8px;
    }

    .dark .sidebar.collapsed .reports-dropdown details ul {
        background: #374151;
        border-color: #4b5563;
    }

    /* Ensure dropdown is visible */
    .reports-dropdown details[open] ul {
        display: block !important;
    }

    /* Hide text when sidebar is collapsed */
    .sidebar.collapsed .nav-text {
        display: none !important;
    }

    /* Show only icons when collapsed */
    .sidebar.collapsed .nav-item,
    .sidebar.collapsed .reports-dropdown summary {
        justify-content: center !important;
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }

    .sidebar.collapsed .reports-dropdown summary .flex.items-center {
        justify-content: center !important;
    }

    /* Hide chevron when collapsed */
    .sidebar.collapsed .reports-dropdown .fa-chevron-down {
        display: none !important;
    }

    /* Add tooltip for collapsed icons */
    .sidebar.collapsed .nav-item::after,
    .sidebar.collapsed .reports-dropdown summary::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: #000;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        margin-left: 8px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
        z-index: 60;
    }

    .sidebar.collapsed .nav-item:hover::after,
    .sidebar.collapsed .reports-dropdown summary:hover::after {
        opacity: 1;
    }

    /* Fix for clickable area in collapsed state */
    .sidebar.collapsed .reports-dropdown summary {
        cursor: pointer !important;
        pointer-events: auto !important;
    }

    /* MATCH THE EXACT ACTIVE STYLING FROM YOUR NAV ITEMS */
    .reports-dropdown summary.active,
    .reports-dropdown.active summary {
        background-color: rgb(14 165 233 / 0.1) !important;
        color: #0ea5e9 !important;
        border-right: 3px solid #0ea5e9 !important;
    }

    /* Ensure the summary element behaves like a nav-item when active */
    .reports-dropdown summary.active {
        background-color: rgb(14 165 233 / 0.1) !important;
        color: #0ea5e9 !important;
        border-right: 3px solid #0ea5e9 !important;
    }

    /* Make sure active state overrides hover */
    .reports-dropdown summary.active:hover,
    .reports-dropdown.active summary:hover {
        background-color: rgb(14 165 233 / 0.1) !important;
        color: #0ea5e9 !important;
        border-right: 3px solid #0ea5e9 !important;
    }

    /* Dark mode support for active state */
    .dark .reports-dropdown summary.active,
    .dark .reports-dropdown.active summary {
        background-color: rgb(14 165 233 / 0.1) !important;
        color: #0ea5e9 !important;
        border-right: 3px solid #0ea5e9 !important;
    }

    .dark .reports-dropdown summary.active:hover,
    .dark .reports-dropdown.active summary:hover {
        background-color: rgb(14 165 233 / 0.1) !important;
        color: #0ea5e9 !important;
        border-right: 3px solid #0ea5e9 !important;
    }
</style>

<ul class="space-y-1 px-2">
    <li>
        <a href="{{ route('home') }}" data-tooltip="Dashboard"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('home') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
            <i class="fas fa-chart-bar text-xl"></i>
            <span class="nav-text flex-1 ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('documents.index') }}" data-tooltip="Documents"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('documents.*') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
            <i class="fa-solid fa-file-lines menu-icon text-xl"></i>
            <span class="nav-text flex-1 ml-3">Documents</span>
        </a>
    </li>
    <li class="relative reports-dropdown {{ Route::is('reports.*') ? 'active' : '' }}">
        <details class="group" {{ Route::is('reports.*') ? 'open' : '' }}>
            <summary data-tooltip="Reports"
                class="cursor-pointer w-full list-none flex items-center justify-between p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('reports.*') ? 'active' : '' }}">
                <div class="flex items-center">
                    <i class="fas fa-file-alt text-xl"></i>
                    <span class="nav-text flex-1 ml-3">Reports</span>
                </div>
                <i class="fas fa-chevron-down ml-2 text-sm transition-transform group-open:rotate-180"></i>
            </summary>

            <ul
                class="ml-8 mt-1 space-y-1 bg-gray-50 dark:bg-gray-800 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <li>
                    <a href="{{ route('reports.balance_sheet') }}"
                        class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('reports.balance_sheet') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
                        Balance Sheet
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.pl') }}"
                        class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('reports.pl') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
                        Profit & Loss A/C
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.ledger') }}"
                        class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('reports.ledger') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
                        All Ledger
                    </a>
                </li>
            </ul>
        </details>
    </li>
    <li>
        <a href="{{ route('client.messages.index') }}" data-tooltip="Chat"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ Route::is('client.*') ? 'active bg-gray-200 dark:bg-gray-600' : '' }}">
            <i class="fas fa-comment-dots menu-icon text-xl"></i>
            <span class="nav-text flex-1 ml-3">Chat</span>
        </a>
    </li>
</ul>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportsDropdown = document.querySelector('.reports-dropdown');
        if (!reportsDropdown) return;

        const details = reportsDropdown.querySelector('details');
        const summary = reportsDropdown.querySelector('summary');

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
