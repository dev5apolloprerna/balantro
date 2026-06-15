@extends('auth.layouts.app')

@section('title', 'Forgot Password')

@section('content')

<style>
#starfield-canvas {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    pointer-events: none;
    z-index: 0;
}

/* keep content above stars */
section {
    position: relative;
    z-index: 1;
}

/* IMPORTANT: allow stars to be visible */
.dark section {
    background-color: transparent !important;
}
</style>
    <section class="min-h-screen  theme-transition flex flex-wrap">
        <canvas id="starfield-canvas"></canvas>
        <!-- Left image (large screens) -->
        <div class="lg:w-3/5 lg:block hidden">
            <div class="flex items-center justify-center h-full bg-white dark:bg-black">

                <!-- Light Logo (shown in light mode) -->
                <img src="{{ asset('assets/images/light-logo.svg') }}"
                    alt="Balantro"
                    class="w-96 h-auto block dark:hidden">

                <!-- Dark Logo (shown in dark mode) -->
                <img src="{{ asset('assets/images/dark-logo.svg') }}"
                    alt="Balantro"
                    class="w-96 h-auto hidden dark:block">

            </div>
        </div>

        <!-- Right content -->
        <div class="lg:w-2/5 flex items-center lg:justify-start justify-center py-10 sm:py-12 px-4 sm:px-6">
            <div class="w-full max-w-md flex flex-col items-center lg:items-start">
                <div class="text-center lg:text-left  w-[350px]">
                    {{-- <a href="/" class="mb-6 block w-[200px] sm:w-[290px] mx-auto lg:mx-0">
                        <!-- Light logo -->
                        <img src="{{ asset('assets/images/light-logo.svg') }}" alt="Balantro"
                            class="w-full block dark:hidden">
                        <!-- Dark logo -->
                        <img src="{{ asset('assets/images/dark-logo.svg') }}" alt="Balantro"
                            class="w-full hidden dark:block">
                    </a> --}}

                    <h1 class="mb-2 text-2xl font-semibold text-gray-900 dark:text-white">
                        Forgot your password?
                    </h1>
                    <p class="mb-6 text-justify text-sm sm:text-base text-gray-600 dark:text-gray-300">
                        Enter your email address and we’ll send you a link to reset your password.
                    </p>
                </div>

                {{-- Session status --}}
                @if (session('status'))
                    <div
                        class="mb-4 flex gap-3 rounded-lg px-4 py-3 text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20">
                        <span class="pt-0.5">
                            <iconify-icon icon="heroicons:check-circle" class="h-5 w-5"></iconify-icon>
                        </span>
                        <div class="leading-relaxed">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                {{-- Validation errors (top summary) --}}
                @if ($errors->any())
                    <div
                        class="mb-4 flex gap-3 rounded-lg px-4 py-3 text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20">
                        <span class="pt-0.5">
                            <iconify-icon icon="heroicons:exclamation-circle" class="h-5 w-5"></iconify-icon>
                        </span>
                        <div class="leading-relaxed space-y-1">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" novalidate>
                    
                    @csrf

                    <label for="email" class="sr-only">Email</label>
                    <div class="relative">
                        <span
                                class="absolute start-4 top-1/2 -translate-y-1/2 flex text-xl text-neutral-500 dark:text-white h-[26px] items-center pointer-events-none">
                                <iconify-icon icon="mage:email" class="flex items-center"></iconify-icon>
                            </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            autocomplete="email" placeholder="Email" @class([
                                'block w-[350px] rounded-lg',
                                'bg-gray-50 dark:bg-black',
                                'border border-neutral-300 dark:border-gray-700',
                                'ps-10 pe-3 py-3.5',
                                'text-sm sm:text-base text-gray-900 dark:text-white',
                                'placeholder:text-gray-400 dark:placeholder:text-gray-500',
                                'focus:outline-none focus:ring-2 focus:ring-[#22d3ee]/40 focus:border-[#22d3ee]/50',
                                'transition',
                            ])
                            aria-invalid="@error('email') true @else false @enderror"
                            aria-describedby="email-help @error('email') email-error @enderror" />
                    </div>
                    <div id="email-help" class="mt-1 text-xs text-gray-500 dark:text-white">
                        We’ll never share your email.
                    </div>
                    {{-- @error('email')
                        <p id="email-error" class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $message }}
                        </p>
                    @enderror --}}

                    <button type="submit"
                        class="mt-6 w-[350px] h-[50px] 
                        flex items-center justify-center

                        text-white font-medium text-sm sm:text-base 
                        rounded-lg relative overflow-hidden

                        bg-gradient-to-r from-[#22d3ee]/70 via-[#22d3ee]/40 to-transparent

                        border border-[#22d3ee]/50

                        {{-- shadow-[0_0_15px_rgba(34,211,238,0.45),inset_0_0_10px_rgba(34,211,238,0.2)] --}}

                        transition-all duration-300">

                            <span class="relative z-10"> Send reset password instructions</span>

                            <!-- constant glow layer -->
                            <span class="absolute inset-0 bg-cyan-400/10 blur-xl opacity-50"></span>
                        </button>

                    {{-- <button type="submit"
                        class="mt-5 sm:mt-6 inline-flex w-[350px] items-center justify-center rounded-lg px-5 py-3
                           text-sm font-medium text-white
                           bg-primary-600 hover:bg-primary-700
                           focus:outline-none focus:ring-2 focus:ring-primary-500
                           disabled:opacity-60 disabled:cursor-not-allowed transition">
                        Send reset password instructions
                    </button> --}}

                    <div class="mt-5 sm:mt-6 text-center">
                        <a href="{{ route('login') }}"
                            class="text-[#22d3ee] dark:text-[#22d3ee] hover:underline text-sm sm:text-base">
                            Back to Sign In
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </section>

    <script>
(function () {
    const canvas = document.getElementById('starfield-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let W, H, particles = [], fallers = [];

    const rand = (a, b) => Math.random() * (b - a) + a;
    const isDark = () => document.documentElement.classList.contains('dark');

    function makeParticle() {
        return {
            x: rand(0, W),
            y: rand(0, H),
            r: rand(0.4, 1.2), // 🔽 smaller stars
            alpha: rand(0.2, 0.7),
            vx: rand(-0.1, 0.1),
            vy: rand(-0.1, 0.1),
            dAlpha: rand(0.002, 0.004) * (Math.random() < 0.5 ? 1 : -1),
            minA: 0.1,
            maxA: 0.8
        };
    }

    function makeFaller() {
        const goRight = Math.random() < 0.5;
        return {
            x: rand(0, W),
            y: rand(0, H * 0.4),
            r: rand(1.8, 3.2), // 🔥 BIGGER head
            vx: goRight ? rand(1.2, 2.5) : rand(-2.5, -1.2), // 🔥 faster
            vy: rand(2.5, 4.5),
            alpha: 1,
            trail: [],
            done: false,
        };
    }

    function resize() {
        W = canvas.width = window.innerWidth;
        H = canvas.height = window.innerHeight;

        // 🔽 REDUCED stars (from 120 → 60)
        particles = Array.from({ length: 60 }, makeParticle);
    }

    function draw() {
        ctx.clearRect(0, 0, W, H);

        if (!isDark()) {
            requestAnimationFrame(draw);
            return;
        }

        /* Floating stars */
        particles.forEach(p => {
            p.alpha += p.dAlpha;
            if (p.alpha >= p.maxA || p.alpha <= p.minA) p.dAlpha *= -1;

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
        });

        /* Shooting stars */
        for (const f of fallers) {
            f.trail.push({ x: f.x, y: f.y });

            // 🔥 longer trail
            if (f.trail.length > 25) f.trail.shift();

            f.x += f.vx;
            f.y += f.vy;
            f.alpha -= 0.01; // slower fade → more visible

            if (f.alpha <= 0 || f.y > H) {
                f.done = true;
                continue;
            }

            // 🔥 BRIGHT tail
            for (let i = 1; i < f.trail.length; i++) {
                const t = i / f.trail.length;
                ctx.beginPath();
                ctx.moveTo(f.trail[i - 1].x, f.trail[i - 1].y);
                ctx.lineTo(f.trail[i].x, f.trail[i].y);
                ctx.strokeStyle = `rgba(255,255,255,${t * f.alpha})`;
                ctx.lineWidth = t * 2.2; // 🔥 thicker trail
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
        setTimeout(spawnFaller, rand(4000, 7000)); // 🔥 more frequent
    }

    window.addEventListener('resize', resize);

    resize();
    draw();

    setTimeout(spawnFaller, rand(1500, 3000));
})();
</script>
@endsection
