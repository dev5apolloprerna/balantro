<!DOCTYPE html>
<html class="h-full">

<head>
    <title><?php echo $__env->yieldContent('title', 'Balantro - Super Admin'); ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <?php echo csrf_field(); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <?php echo $__env->make('common.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

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
        <?php echo $__env->make('common.sidebars', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!-- <div class="main-content theme-transition flex-1 overflow-hidden bg-white dark:bg-black" id="main-content"> -->
        <!-- <div class="main-content flex-1 overflow-hidden"> -->
        <div id="main-content" class="main-content flex-1 overflow-hidden">
            <?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!-- Main Content -->
            <!-- Page Content -->
            <!-- <main class="p-6 overflow-y-auto bg-white dark:bg-black" style="height: calc(100vh - 80px)"> -->
            <main class="p-3 bg-white dark:bg-black">
                <!-- Welcome Header -->

                <div class="dashboard-main-body bg-white dark:bg-black">
                    <div id="flashMessages">
                        <?php if(session('notice')): ?>
                            <div
                                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 dark:bg-green-900 dark:border-green-700 dark:text-green-300">
                                <?php echo e(session('notice')); ?>

                            </div>
                        <?php endif; ?>
                        <?php if(session('alert')): ?>
                            <div
                                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 dark:bg-red-900 dark:border-red-700 dark:text-red-300">
                                <?php echo e(session('alert')); ?>

                            </div>
                        <?php endif; ?>
                    </div>

                    <?php echo $__env->yieldContent('content'); ?>
                    <!-- jQuery -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                    <!-- Select2 -->
                    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

                    <?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <?php echo $__env->yieldContent('scripts'); ?>
                    <?php echo $__env->yieldPushContent('scripts'); ?>
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
</body>

</html>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\layouts\super_admin.blade.php ENDPATH**/ ?>