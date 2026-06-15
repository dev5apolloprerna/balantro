<!-- Sidebar Overlay for Mobile -->
<div class="overlay" id="sidebar-overlay"></div>

<style>
    #sidebar-stars {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
    }
    .sidebar > div {
        position: relative;
        z-index: 1;
    }
    /* .sidebar{
        width: 200px;
        background: #000 !important;
    } */
    .sidebar{
        flex-shrink: 0;
        width: 200px;
        transition: width .25s ease;
        overflow: visible !important;
    }

    .sidebar.collapsed{
        width: 72px !important;
    }

    /* Light theme */
    html:not(.dark) .sidebar {
        background: #ffffff !important;
    }

    /* Dark theme */
    .dark .sidebar {
        background: #000000 !important;
    }

    /* Light */
    html:not(.dark) .nav-item:hover .nav-text {
        color: #111827 !important; /* gray-900 */
    }

    /* Dark */
    .dark .nav-item:hover .nav-text {
        color: #e5e7eb !important; /* gray-200 */
    }

    html:not(.dark) .sidebar {
        background: linear-gradient(180deg, #ffffff, #f8fafc);
    }

    .reports-dropdown{
        position: relative;
        z-index: 99999;
    }

    .sidebar.collapsed .reports-dropdown details[open]{
        overflow: visible !important;
    }

    .sidebar.collapsed .reports-dropdown details[open] .submenu{
        overflow: visible !important;
        z-index: 999999 !important;
    }
</style>
<!-- Sidebar -->
<!-- <aside class="sidebar theme-transition bg-white dark:bg-black shadow-lg fixed lg:relative z-50" id="sidebar"> -->
<aside class="sidebar theme-transition 
    bg-white/80 dark:bg-black
    backdrop-blur-xl 
    border-r border-white/10
    fixed lg:relative z-50
    relative "
    id="sidebar">

    <!-- subtle glass glow layer -->
    <div class="absolute inset-0 bg-white/5 dark:bg-black pointer-events-none"></div>
    <div class="h-full flex flex-col bg-transparent dark:bg-black relative z-10">
        <!-- Logo -->
        <div
            class="flex items-center justify-between px-3 py-[13px] border-b border-gray-200 dark:border-gray-800 bg-transparent">
            <a href="<?php echo e(route('home')); ?>" class="flex items-center">
                <!-- Full logo (shown in expanded mode) -->
                <img src="<?php echo e(asset('assets/images/light-logo.svg')); ?>" alt="Balantro"
                    class="h-8 block dark:hidden logo-full">
                <img src="<?php echo e(asset('assets/images/dark-logo.svg')); ?>" alt="Balantro"
                    class="h-8 hidden dark:block logo-full">

                <!-- Small logo (only visible when collapsed) -->
                <img src="<?php echo e(asset('assets/images/small-logo.svg')); ?>" alt="Balantro Small"
                    class="h-8 hidden logo-small">
            </a>
            <button class="lg:hidden text-gray-500 dark:text-gray-400" id="close-sidebar">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- User Profile -->
        

        <!-- Navigation -->
        <nav class="flex-1 py-4 bg-transparent">
            <?php if(auth()->user()->role == \App\Models\User::ROLES['super_admin']): ?>
                <?php echo $__env->make('navigations.super_admin_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php elseif(auth()->user()->role == \App\Models\User::ROLES['supervisor']): ?>
                <?php echo $__env->make('navigations.supervisor_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php elseif(auth()->user()->role == \App\Models\User::ROLES['manager']): ?>
                <?php echo $__env->make('navigations.manager_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php elseif(auth()->user()->role == \App\Models\User::ROLES['data_entry_operator']): ?>
                <?php echo $__env->make('navigations.data_entry_operator_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php elseif(auth()->user()->role == \App\Models\User::ROLES['client']): ?>
                <?php echo $__env->make('navigations.client_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
        </nav>

        <!-- Bottom Actions -->
        
    </div>
</aside>

<?php /**PATH D:\xampp\htdocs\balantro\resources\views/common/sidebars.blade.php ENDPATH**/ ?>