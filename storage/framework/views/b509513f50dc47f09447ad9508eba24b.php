<style>
    /* Prevent transitions during initial load */
    .no-transition * {
        transition: none !important;
    }

    .theme-transition * {
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    .sidebar {
        width: 260px;
        transition: all 0.3s ease;
    }

    .sidebar.collapsed {
        width: 80px;
    }

    .main-content {
        transition: all 0.3s ease;
    }

    .main-content.expanded {
        margin-left: 0;
    }

    .main-content.full {
        margin-left: 0;
    }

    @media (max-width: 1024px) {
        .sidebar {
            transform: translateX(-100%);
            position: fixed;
            z-index: 40;
            height: 100vh;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 30;
        }

        .overlay.active {
            display: block;
        }
    }

    .nav-item.active {
        background-color: rgb(14 165 233 / 0.1);
        color: #0ea5e9;
        border-right: 3px solid #0ea5e9;
    }

    .nav-item.active i {
        color: #0ea5e9;
    }

    .stat-card {
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    /* New styles for collapsed sidebar */
    .nav-text {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .sidebar.collapsed .nav-text {
        opacity: 0;
        visibility: hidden;
    }

    .sidebar.collapsed .user-info,
    .sidebar.collapsed .section-title {
        display: none;
    }

    .sidebar.collapsed .logo-text {
        display: none;
    }

    .sidebar.collapsed .flex-1 {
        flex: 0;
    }

    .sidebar.collapsed .p-4 {
        padding: 1rem 0.5rem;
    }

    .sidebar.collapsed .px-2 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    .sidebar.collapsed .justify-between {
        justify-content: center;
    }

    /* Show small logo only when sidebar is collapsed */
    .sidebar .logo-small {
        display: none;
    }

    .sidebar.collapsed .logo-full {
        display: none !important;
    }

    .sidebar.collapsed .logo-small {
        display: block !important;
    }

    .sidebar.collapsed .logo-text {
        display: none;
    }

    .sidebar.collapsed .user-info,
    .sidebar.collapsed .section-title {
        display: none;
    }

    /* PURE BLACK DARK MODE THEME */
    body.dark {
        background: #000000 !important;
        color: #e6e6e6 !important;
    }

    .sidebar.dark,
    .navbar.dark,
    .card.dark,
    .table.dark {
        background: #000000 !important;
        color: #cfcfcf !important;
        border-color: #1a1a1a !important;
    }

    .table.dark th,
    .table.dark td {
        background: #000000 !important;
        border-bottom: 1px solid #1f1f1f !important;
    }

    /* Inputs */
    .dark input,
    .dark select {
        background: #0a0a0a !important;
        border: 1px solid #222 !important;
        color: #fff !important;
    }

    .dark input:focus,
    .dark select:focus {
        border-color: #00aaff !important;
        box-shadow: 0 0 4px rgba(0, 170, 255, 0.6);
    }

    /* Buttons */
    .btn-primary {
        background: #007bff;
        border: 0;
    }

    .btn-primary:hover {
        background: #0069d9;
    }

    .btn-danger {
        background: #ff4757;
        border: 0;
    }

    .btn-danger:hover {
        background: #e84118;
    }

    /* Smooth transition */
    * {
        transition: background 0.25s, color 0.25s;
    }

    .sidebar.dark {
        background: rgba(0, 0, 0, 0.95);
        backdrop-filter: blur(8px);
    }

    .table.dark tbody tr td {
        text-align: center;
        color: #777 !important;
        font-style: italic;
    }

    /* Sidebar separation (clean vertical border) */
    .sidebar {
        border-right: 1px solid rgba(255, 255, 255, 0.08);
        /* thin line */
    }

    /* Dark mode sidebar background */
    body.dark .sidebar {
        background: #000000 !important;
    }

    /* Add a soft shadow instead of border (optional alternative) */
    body.dark .sidebar {
        box-shadow: 3px 0 12px rgba(0, 0, 0, 0.6);
        /* premium shadow */
    }

    body.dark .sidebar {
        background: #050505 !important;
    }

    .sidebar .active i {
        color: transparent;
        /* <-- this hides icons on dark background */
    }

    /* ✅ SIDEBAR ICONS - ALWAYS VISIBLE IN DARK MODE */
    .sidebar i,
    .sidebar svg {
        color: #8aa4b2 !important;
        /* default icon color */
        fill: #8aa4b2 !important;
    }

    /* ✅ ACTIVE MENU ITEM ICON COLOR */
    .sidebar .active i,
    .sidebar .active svg {
        color: #00c2ff !important;
        /* highlight cyan icon on active */
        fill: #00c2ff !important;
    }

    /* ✅ MENU ITEM HOVER ICON COLOR */
    .sidebar a:hover i,
    .sidebar a:hover svg {
        color: #00c2ff !important;
        fill: #00c2ff !important;
    }

    /* ------------------------------
   GLOBAL SIDEBAR ICON FIX
   (Works on ALL pages)
--------------------------------*/

    /* Default sidebar icon color */
    .sidebar i,
    .sidebar svg {
        color: #94a3b8 !important;
        /* grayish blue */
        fill: #94a3b8 !important;
    }

    /* Active menu icon color */
    .sidebar .active i,
    .sidebar .active svg,
    .sidebar .mm-active i,
    .sidebar .mm-active svg {
        color: #00c2ff !important;
        /* cyan highlight */
        fill: #00c2ff !important;
    }

    /* Hover icon effect */
    .sidebar a:hover i,
    .sidebar a:hover svg {
        color: #00c2ff !important;
        fill: #00c2ff !important;
    }

    /* Fix when menu collapses (icons disappear issue) */
    .sidebar .open i,
    .sidebar .open svg {
        color: #94a3b8 !important;
        fill: #94a3b8 !important;
    }

    .sidebar svg {
        filter: none !important;
        opacity: 1 !important;
    }

    /* ============================
   SIDEBAR ICON FIX (GLOBAL)
   ============================ */

    /* Default icon color in dark mode */
    .sidebar i,
    .sidebar svg {
        color: #94a3b8 !important;
        /* Slate-400 like */
        fill: #94a3b8 !important;
    }

    /* Active / selected menu item */
    .sidebar .active i,
    .sidebar .mm-active i,
    .sidebar .active svg,
    .sidebar .mm-active svg {
        color: #00c2ff !important;
        /* Cyan highlight */
        fill: #00c2ff !important;
    }

    /* Hover icon */
    .sidebar a:hover i,
    .sidebar a:hover svg {
        color: #00c2ff !important;
        fill: #00c2ff !important;
    }

    /* Fix icon disappearing on menu collapse / expand */
    .sidebar .menu-open i,
    .sidebar .menu-open svg {
        color: #94a3b8 !important;
        fill: #94a3b8 !important;
    }
</style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script>
    // ✅ CRITICAL: Theme initialization before ANY content renders
    (function() {
        try {
            const storedTheme = localStorage.getItem('theme');
            // const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            // const theme = storedTheme || (prefersDark ? 'dark' : 'light');
            const theme = storedTheme || 'dark';

            // Apply theme immediately to prevent FOUC (Flash of Unstyled Content)
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.style.colorScheme = 'dark';
                document.documentElement.style.backgroundColor = '#000000';
                document.body.style.backgroundColor = '#000000';
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.style.colorScheme = 'light';
                document.documentElement.style.backgroundColor = '#ffffff';
                document.body.style.backgroundColor = '#ffffff';
            }
        } catch (e) {
            console.error('Theme initialization error:', e);
        }
    })();
</script>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\common\head.blade.php ENDPATH**/ ?>