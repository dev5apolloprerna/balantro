<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script> -->
<script>
    // Consolidated theme management
    function getStoredTheme() {
        return localStorage.getItem('theme');
    }

    function setStoredTheme(theme) {
        localStorage.setItem('theme', theme);
    }

    function getPreferredTheme() {
        const storedTheme = getStoredTheme();
        if (storedTheme) {
            return storedTheme;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function setTheme(theme) {
        // Add no-transition during theme change
        document.documentElement.classList.add('no-transition');

        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
            document.documentElement.style.backgroundColor = '#000000';
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
            document.documentElement.style.backgroundColor = '#ffffff';
        }

        // Force reflow
        document.documentElement.offsetHeight;

        // Remove no-transition after change
        setTimeout(() => {
            document.documentElement.classList.remove('no-transition');
        }, 50);

        setStoredTheme(theme);
    }

    // Sidebar functionality
    function toggleSidebar() {

        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const overlay = document.getElementById('sidebar-overlay');

        if (!sidebar) return;

        if (window.innerWidth < 1024) {

            sidebar.classList.toggle('active');

            if (overlay) {
                overlay.classList.toggle('active');
            }

        } else {

            sidebar.classList.toggle('collapsed');

            if (mainContent) {
                mainContent.classList.toggle('expanded');
            }

            const isCollapsed =
                sidebar.classList.contains('collapsed');

            localStorage.setItem(
                'sidebar-collapsed',
                isCollapsed
            );

            // close opened dropdowns
            document.querySelectorAll(
                '.reports-dropdown details'
            ).forEach(d => d.removeAttribute('open'));
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Theme is already initialized in head, just set up the toggle

        // Initialize sidebar state
        const sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');

        // if (sidebarCollapsed && window.innerWidth >= 1024) {
        //     sidebar.classList.add('collapsed');
        //     mainContent.classList.add('expanded');
        // }
        if (window.innerWidth >= 1024) {

            if (sidebarCollapsed) {

                sidebar.classList.add('collapsed');

                if (mainContent) {
                    mainContent.classList.add('expanded');
                }

            } else {

                sidebar.classList.remove('collapsed');

                if (mainContent) {
                    mainContent.classList.remove('expanded');
                }
            }
        }

        // Set up theme toggle button
        const themeToggleBtn = document.getElementById('theme-toggle');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                const currentTheme = getStoredTheme();
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            });
        }

        // Set up sidebar toggle
        const toggleSidebarBtn = document.getElementById('toggle-sidebar');
        const closeSidebarBtn = document.getElementById('close-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (toggleSidebarBtn) {
            toggleSidebarBtn.addEventListener('click', toggleSidebar);
        }

        if (closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', toggleSidebar);
        }

        if (overlay) {
            overlay.addEventListener('click', toggleSidebar);
        }

        // Set active nav item based on current URL
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-item').forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPath.startsWith(href)) {
                item.classList.add('active');
            }
        });

        // User menu toggle
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userMenu = document.getElementById('user-menu');
        if (userMenuBtn) {
            userMenuBtn.addEventListener('click', () => {
                userMenu.classList.toggle('hidden');
            });

            // Close user menu when clicking outside
            document.addEventListener('click', (event) => {
                if (!userMenuBtn.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        }
    });

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
        const storedTheme = getStoredTheme();
        if (storedTheme !== 'light' && storedTheme !== 'dark') {
            setTheme(e.matches ? 'dark' : 'light');
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const overlay = document.getElementById('sidebar-overlay');

        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');

            // Restore collapsed state
            const sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        }
    });
    
</script>
<script>
window.addEventListener('scroll', function () {
    const userMenu = document.getElementById('user-menu');
    if (userMenu && !userMenu.classList.contains('hidden')) {
        userMenu.classList.add('hidden');
    }
});
</script>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\common\footer.blade.php ENDPATH**/ ?>