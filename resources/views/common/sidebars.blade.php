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
{{-- <canvas id="sidebar-stars"></canvas> --}}
    <!-- subtle glass glow layer -->
    <div class="absolute inset-0 bg-white/5 dark:bg-black pointer-events-none"></div>
    <div class="h-full flex flex-col bg-transparent dark:bg-black relative z-10">
        <!-- Logo -->
        <div
            class="flex items-center justify-between px-3 py-[13px] border-b border-gray-200 dark:border-gray-800 bg-transparent">
            <a href="{{ route('home') }}" class="flex items-center">
                <!-- Full logo (shown in expanded mode) -->
                <img src="{{ asset('assets/images/light-logo.svg') }}" alt="Balantro"
                    class="h-8 block dark:hidden logo-full">
                <img src="{{ asset('assets/images/dark-logo.svg') }}" alt="Balantro"
                    class="h-8 hidden dark:block logo-full">

                <!-- Small logo (only visible when collapsed) -->
                <img src="{{ asset('assets/images/small-logo.svg') }}" alt="Balantro Small"
                    class="h-8 hidden logo-small">
            </a>
            <button class="lg:hidden text-gray-500 dark:text-gray-400" id="close-sidebar">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- User Profile -->
        {{-- <div class="px-2 border-b border-gray-200 dark:border-gray-800 flex items-center bg-transparent">
            <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-gray-900 flex items-center justify-center">
                <i class="fas fa-user text-primary-600 dark:text-primary-400"></i>
            </div>
            <div class="ml-3 user-info">
                <p class="text-sm font-medium dark:text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->type }}</p>
            </div>
        </div> --}}

        <!-- Navigation -->
        <nav class="flex-1 py-4 bg-transparent">
            @if (auth()->user()->role == \App\Models\User::ROLES['super_admin'])
                @include('navigations.super_admin_nav')
            @elseif (auth()->user()->role == \App\Models\User::ROLES['supervisor'])
                @include('navigations.supervisor_nav')
            @elseif (auth()->user()->role == \App\Models\User::ROLES['manager'])
                @include('navigations.manager_nav')
            @elseif (auth()->user()->role == \App\Models\User::ROLES['data_entry_operator'])
                @include('navigations.data_entry_operator_nav')
            @elseif (auth()->user()->role == \App\Models\User::ROLES['client'])
                @include('navigations.client_nav')
            @endif
        </nav>

        <!-- Bottom Actions -->
        {{-- <div class="px-2 border-t border-gray-200 dark:border-gray-800 bg-transparent">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center p-3 rounded-lg text-black dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 mt-2">
                    <i class="fas fa-sign-out-alt text-xl mr-3"></i>
                    <span class="nav-text flex-1 text-left">Logout</span>
                </button>
            </form>
        </div> --}}
    </div>
</aside>
{{-- <script>
const sidebarCanvas = document.getElementById('sidebar-stars');
const ctx2 = sidebarCanvas.getContext('2d');

let W, H, particles = [], fallers = [];

const rand = (a, b) => Math.random() * (b - a) + a;

function resizeSidebar() {
    const sidebar = document.getElementById('sidebar');

    W = sidebarCanvas.width = sidebar.clientWidth;
    H = sidebarCanvas.height = sidebar.clientHeight;

    // 🔽 fewer particles for sidebar
    particles = Array.from({length: 30}, () => ({
        x: rand(0, W),
        y: rand(0, H),
        r: rand(0.4, 1.2),
        alpha: rand(0.2, 0.7),
        vx: rand(-0.1, 0.1),
        vy: rand(-0.1, 0.1),
        dAlpha: rand(0.002, 0.004) * (Math.random() < 0.5 ? 1 : -1),
        minA: 0.1,
        maxA: 0.8
    }));
}

window.addEventListener('resize', resizeSidebar);
resizeSidebar();

/* Shooting star */
function makeFaller() {
    const goRight = Math.random() < 0.5;
    return {
        x: rand(0, W),
        y: rand(0, H * 0.4),
        r: rand(1.5, 2.5),
        vx: goRight ? rand(0.8, 1.8) : rand(-1.8, -0.8),
        vy: rand(1.5, 3),
        alpha: 1,
        trail: [],
        done: false,
    };
}

function drawSidebarStars() {
    ctx2.clearRect(0, 0, W, H);

    /* Floating stars */
    for (const p of particles) {
        p.alpha += p.dAlpha;

        if (p.alpha >= p.maxA || p.alpha <= p.minA) {
            p.dAlpha *= -1;
        }

        p.x += p.vx;
        p.y += p.vy;

        if (p.x < 0) p.x = W;
        if (p.x > W) p.x = 0;
        if (p.y < 0) p.y = H;
        if (p.y > H) p.y = 0;

        ctx2.beginPath();
        ctx2.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx2.fillStyle = `rgba(255,255,255,${p.alpha})`;
        ctx2.fill();
    }

    /* Shooting stars */
    for (const f of fallers) {
        f.trail.push({ x: f.x, y: f.y });

        if (f.trail.length > 20) f.trail.shift();

        f.x += f.vx;
        f.y += f.vy;
        f.alpha -= 0.015;

        if (f.alpha <= 0 || f.y > H) {
            f.done = true;
            continue;
        }

        // tail
        for (let i = 1; i < f.trail.length; i++) {
            const t = i / f.trail.length;

            ctx2.beginPath();
            ctx2.moveTo(f.trail[i - 1].x, f.trail[i - 1].y);
            ctx2.lineTo(f.trail[i].x, f.trail[i].y);

            ctx2.strokeStyle = `rgba(255,255,255,${t * f.alpha})`;
            ctx2.lineWidth = t * 1.5;
            ctx2.stroke();
        }

        // head
        ctx2.beginPath();
        ctx2.arc(f.x, f.y, f.r, 0, Math.PI * 2);
        ctx2.fillStyle = `rgba(255,255,255,${f.alpha})`;
        ctx2.shadowBlur = 12;
        ctx2.shadowColor = 'white';
        ctx2.fill();
        ctx2.shadowBlur = 0;
    }

    fallers = fallers.filter(f => !f.done);

    requestAnimationFrame(drawSidebarStars);
}

/* Spawn shooting stars */
function spawnFaller() {
    fallers.push(makeFaller());
    setTimeout(spawnFaller, rand(6000, 10000));
}

drawSidebarStars();
setTimeout(spawnFaller, rand(2000, 4000));
</script> --}}
