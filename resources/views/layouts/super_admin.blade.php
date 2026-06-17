<!DOCTYPE html>
<html class="h-full">

<head>
    <title>@yield('title', 'Balantro - Super Admin')</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    @csrf
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('common.head')

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        };
    </script>

    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Force pure black backgrounds */
        .dark body {
            background-color: #000000 !important;
        }

        .dark .min-h-screen {
            background-color: #000000 !important;
        }

        .dark .main-content {
            /* background-color: #000000 !important; */
            background-color: transparent !important;
        }

        .dark main {
            background-color: #000000 !important;
        }

        .dark .dashboard-main-body {
            background-color: #000000 !important;
        }

        /* Smooth transitions for theme switching */
        body,
        header,
        .theme-transition {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        /* Responsive action icons: compact on small screens, clearer on large displays. */
        .action-icon {
            font-size: 0.875rem;
            line-height: 1;
        }

        @media (min-width: 1280px) {
            .action-icon {
                font-size: 1rem;
            }
        }

        @media (min-width: 1536px) {
            .action-icon {
                font-size: 1.125rem;
            }
        }

        /* ── STARFIELD ADDITIONS (do not remove) ── */
        #starfield-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 0;
        }
 
        .dark .main-content,
        .dark main,
        .dark .dashboard-main-body,
        .dark .min-h-screen {
            background-color: transparent !important;
        }
        
        .sidebar.collapsed .nav-item {
            justify-content: center;
            align-items: center;
            padding: 12px 0;
        }

        /* .sidebar.collapsed .nav-text {
            display: none;     
        } */

        .sidebar.collapsed .menu-icon,
        .sidebar.collapsed i {
            margin: 0 !important;
        }
        .sidebar.collapsed ul {
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar.collapsed ul li {
            display: flex;
            justify-content: center;
            margin-bottom: 6px;
        }

        /* .sidebar.collapsed .nav-item {
            justify-content: center;
        } */
        .sidebar.collapsed i {
            font-size: 20px;
        }

        .sidebar.collapsed .sidebar-list {
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar.collapsed .user-info {
            display: none;
        }

        .sidebar.collapsed .sidebar img.logo-full {
            display: none;
        }

        .sidebar.collapsed .sidebar img.logo-small {
            display: block;
            margin: auto;
        }
        .sidebar.collapsed .sidebar > div:first-child {
            justify-content: center !important;
        }
        .sidebar.collapsed .nav-text {
            display: none;
            flex: 0 !important;
            margin: 0 !important;
        }

        html.sidebar-precollapsed #sidebar {
            width: 72px !important;
        }
        html.sidebar-precollapsed #sidebar{
            width:72px !important;
            min-width:72px !important;
        }

        html.sidebar-precollapsed #sidebar .nav-text{
            display:none !important;
        }

        html.sidebar-precollapsed #sidebar .logo-full{
            display:none !important;
        }

        html.sidebar-precollapsed #sidebar .logo-small{
            display:block !important;
            margin:auto;
        }
    </style>

</head>

<body class="font-sans antialiased h-full bg-white dark:bg-black text-gray-900 dark:text-gray-100">
    
    <canvas id="starfield-canvas"></canvas>
    
    <div class="flex min-h-screen bg-white dark:bg-black" style="position:relative;z-index:1;">
        @include('common.sidebars')
        <!-- <div class="main-content theme-transition flex-1 overflow-hidden bg-white dark:bg-black" id="main-content"> -->
        <!-- <div class="main-content flex-1 overflow-hidden"> -->
        <div id="main-content" class="main-content flex-1 overflow-hidden">
            @include('common.header')
            <!-- Main Content -->
            <!-- Page Content -->
            <!-- <main class="p-6 overflow-y-auto bg-white dark:bg-black" style="height: calc(100vh - 80px)"> -->
            <main class="p-3 bg-white dark:bg-black">
                <!-- Welcome Header -->

                <div class="dashboard-main-body bg-white dark:bg-black">
                    <div id="flashMessages">
                        @if (session('notice'))
                            <div
                                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 dark:bg-green-900 dark:border-green-700 dark:text-green-300">
                                {{ session('notice') }}
                            </div>
                        @endif
                        @if (session('alert'))
                            <div
                                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 dark:bg-red-900 dark:border-red-700 dark:text-red-300">
                                {{ session('alert') }}
                            </div>
                        @endif
                    </div>

                    @yield('content')
                    <!-- jQuery -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                    <!-- Select2 -->
                    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
                    
                    <style>
                        /* Keep native and Select2 dropdowns dark immediately when the page is in dark mode. */
                        .dark select,
                        .dark select option,
                        .dark select optgroup {
                            background-color: #000000 !important;
                            color: #ffffff !important;
                        }

                        .dark .select2-container--default .select2-selection--single,
                        .dark .select2-container--default .select2-selection--multiple,
                        .dark .select2-container--default .select2-results__option,
                        .dark .select2-container--default .select2-results,
                        .dark .select2-dropdown,
                        .select2-dropdown.dark-theme,
                        .select2-dropdown.dark-theme .select2-results,
                        .select2-dropdown.dark-theme .select2-results__option {
                            background-color: #ffffff !important;
                            color: #000000 !important;
                            transition: none !important;
                        }

                        .dark .select2-container--default .select2-selection--single,
                        .dark .select2-container--default .select2-selection--multiple,
                        .dark .select2-dropdown,
                        .select2-dropdown.dark-theme {
                            border-color: #d1d5db !important;
                        }

                        .dark .select2-container--default .select2-selection__rendered,
                        .dark .select2-container--default .select2-selection__placeholder,
                        .select2-dropdown.dark-theme .select2-search__field {
                            color: #000000 !important;
                        }

                        .dark .select2-container--default .select2-search--dropdown .select2-search__field,
                        .select2-dropdown.dark-theme .select2-search__field {
                            background-color: #111827 !important;
                            border-color: #374151 !important;
                            color: #ffffff !important;
                        }

                        .dark .select2-container--default .select2-results__option--highlighted,
                        .select2-dropdown.dark-theme .select2-results__option--highlighted {
                            background-color: #2563eb !important;
                            color: #ffffff !important;
                        }
                    </style>
                    <script>
                        $(document).on('select2:open', function () {
                            const isDark = document.documentElement.classList.contains('dark');
                            $('.select2-dropdown').toggleClass('dark-theme', isDark);
                        });
                    </script>

                    @include('common.footer')

                    @yield('scripts')
                    @stack('scripts')
                </div>
            </main>
        </div>
    </div>
   

    <!-- STARFIELD SCRIPT — added, do not remove -->
    <script>
(function () {
    const canvas = document.getElementById('starfield-canvas');
    const ctx    = canvas.getContext('2d');

    let W, H, particles = [], fallers = [];

    const rand   = (a, b) => Math.random() * (b - a) + a;
    const isDark = () => document.documentElement.classList.contains('dark');

    function makeParticle() {
        return {
            x: rand(0, W),
            y: rand(0, H),
            r: rand(0.4, 1.2), // 🔽 smaller
            alpha: rand(0.2, 0.7),
            vx: rand(-0.1, 0.1),
            vy: rand(-0.1, 0.1),
            dAlpha: rand(0.002, 0.004) * (Math.random() < 0.5 ? 1 : -1),
            minA: 0.1,
            maxA: 0.8,
        };
    }

    function makeFaller() {
        const goRight = Math.random() < 0.5;

        return {
            x: rand(0, W),
            y: rand(0, H * 0.4),
            r: rand(1.8, 3.2), // 🔥 bigger head
            vx: goRight ? rand(1.2, 2.5) : rand(-2.5, -1.2),
            vy: rand(2.5, 4.5),
            alpha: 1,
            trail: [],
            done: false,
        };
    }

    function resize() {
        W = canvas.width  = window.innerWidth;
        H = canvas.height = window.innerHeight;

        // 🔽 reduce stars
        particles = Array.from({ length: 60 }, makeParticle);
    }

    function draw() {
        ctx.clearRect(0, 0, W, H);

        if (!isDark()) {
            requestAnimationFrame(draw);
            return;
        }

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

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255,255,255,${p.alpha})`;
            ctx.fill();
        }

        /* Shooting stars */
        for (const f of fallers) {
            f.trail.push({ x: f.x, y: f.y });

            // 🔥 longer trail
            if (f.trail.length > 25) f.trail.shift();

            f.x += f.vx;
            f.y += f.vy;
            f.alpha -= 0.01;

            if (f.alpha <= 0 || f.y > H) {
                f.done = true;
                continue;
            }

            // 🔥 brighter + thicker tail
            for (let i = 1; i < f.trail.length; i++) {
                const t = i / f.trail.length;

                ctx.beginPath();
                ctx.moveTo(f.trail[i - 1].x, f.trail[i - 1].y);
                ctx.lineTo(f.trail[i].x, f.trail[i].y);

                ctx.strokeStyle = `rgba(255,255,255,${t * f.alpha})`;
                ctx.lineWidth = t * 2.2;
                ctx.stroke();
            }

            // 🔥 glowing head
            ctx.beginPath();
            ctx.arc(f.x, f.y, f.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255,255,255,${f.alpha})`;

            ctx.shadowBlur = 20;
            ctx.shadowColor = 'white';
            ctx.fill();
            ctx.shadowBlur = 0;
        }

        fallers = fallers.filter(f => !f.done);

        requestAnimationFrame(draw);
    }

    function spawnFaller() {
        if (isDark()) {
            fallers.push(makeFaller());
        }

        // 🔥 more frequent
        setTimeout(spawnFaller, rand(4000, 7000));
    }

    window.addEventListener('resize', resize);

    resize();
    draw();

    setTimeout(spawnFaller, rand(1500, 3000));
})();
</script>
<script>
// Disable Right Click
// document.addEventListener('contextmenu', function(e) {
//     e.preventDefault();
// });

// // Disable F12 and DevTools shortcuts
// document.addEventListener('keydown', function(e) {

//     // F12
//     if (e.keyCode === 123) {
//         e.preventDefault();
//         return false;
//     }

//     // Ctrl+Shift+I
//     if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
//         e.preventDefault();
//         return false;
//     }

//     // Ctrl+Shift+J
//     if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
//         e.preventDefault();
//         return false;
//     }

//     // Ctrl+Shift+C
//     if (e.ctrlKey && e.shiftKey && e.keyCode === 67) {
//         e.preventDefault();
//         return false;
//     }

//     // Ctrl+U
//     if (e.ctrlKey && e.keyCode === 85) {
//         e.preventDefault();
//         return false;
//     }

//     // Ctrl+S
//     if (e.ctrlKey && e.keyCode === 83) {
//         e.preventDefault();
//         return false;
//     }
// });
</script>
<script>
    let idleTime = 0;
    setInterval(function () {
        idleTime++;
        if (idleTime >= 60) { // 60 minute
            $.post("{{ route('logout.idle') }}", {
                _token: "{{ csrf_token() }}"
            }, function () {
                window.location.href = "{{ route('login') }}";
            });
        }
    }, 60000); // 60,000 ms = 1 minute
    $(document).on(
        'mousemove keypress click scroll touchstart',
        function () {
            idleTime = 0;
        }
    );
</script>
</body>

</html>
