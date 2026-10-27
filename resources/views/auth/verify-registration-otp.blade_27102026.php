@extends('auth.layouts.app')

@section('title', 'Verify Email')
@section('field_validation_only', true)

@section('content')
<style>
    #starfield-canvas {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        pointer-events: none;
        z-index: 0;
    }

    section {
        position: relative;
        z-index: 1;
    }

    .dark section {
        background-color: transparent !important;
    }
</style>

<section class="bg-white dark:bg-black flex flex-wrap min-h-[calc(100vh-64px)]">
    <canvas id="starfield-canvas"></canvas>

    <div class="lg:w-3/5 lg:block hidden">
        <div class="flex items-center justify-center h-full bg-white dark:bg-black">
            <img src="{{ asset('assets/images/light-logo_login.svg') }}" alt="Balantro"
                class="w-96 h-auto block dark:hidden">
            <img src="{{ asset('assets/images/dark-logo_login.svg') }}" alt="Balantro"
                class="w-96 h-auto hidden dark:block">
        </div>
    </div>

    <div class="font-sans w-full lg:w-2/5 py-8 px-4 sm:px-6 flex flex-col justify-center dark:bg-black">
        <div class="w-full max-w-md mx-auto px-4 sm:px-6 flex flex-col items-center">
            <div class="w-[350px] max-w-full text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full border border-[#22d3ee]/50 bg-cyan-50 text-[#22d3ee] dark:bg-gray-900">
                    <iconify-icon icon="mage:email" class="text-2xl"></iconify-icon>
                </div>
                <p class="mb-2 text-secondary-light dark:text-white text-base sm:text-lg">Verify your email</p>
                <p class="mb-6 text-sm text-gray-600 dark:text-gray-300">
                    Enter the 6-digit code sent to<br>
                    <strong class="font-medium text-gray-900 dark:text-white">{{ $email }}</strong>
                </p>

                @if (session('status'))
                <div class="mb-4 flex items-start gap-3 rounded-lg px-4 py-3 text-left text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-gray-900">
                    <iconify-icon icon="heroicons:check-circle" class="mt-0.5 shrink-0 text-xl"></iconify-icon>
                    <div class="flex-1 leading-relaxed">{{ session('status') }}</div>
                </div>
                @endif

                @if ($errors->any())
                <div class="mb-4 flex items-start gap-3 rounded-lg px-4 py-3 text-left text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-gray-900">
                    <iconify-icon icon="heroicons:exclamation-circle" class="mt-0.5 shrink-0 text-xl"></iconify-icon>
                    <div class="flex-1 leading-relaxed">
                        @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <form method="POST" action="{{ route('registration.otp.verify') }}">
                @csrf
                <div class="relative mx-auto">
                    <span class="absolute start-4 top-1/2 -translate-y-1/2 flex items-center text-xl text-neutral-500 dark:text-white pointer-events-none">
                        <iconify-icon icon="mage:key" class="flex items-center"></iconify-icon>
                    </span>
                    <input id="otp" name="otp" type="text" inputmode="numeric" autocomplete="one-time-code"
                        maxlength="6" pattern="[0-9]{6}" required autofocus aria-label="6-digit verification code"
                        placeholder="Verification code"
                        class="h-[48px] sm:h-[50px] ps-11 pe-4 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-lg w-[350px] max-w-full text-center text-base tracking-[0.3em] text-gray-900 dark:text-white placeholder:tracking-normal focus:outline-none focus:ring-2 focus:ring-[#22d3ee]/40 focus:border-[#22d3ee]/50">
                </div>

                <button type="submit"
                    class="mt-6 w-[350px] max-w-full h-[50px] flex items-center justify-center text-white font-medium text-sm sm:text-base rounded-lg relative overflow-hidden bg-gradient-to-r from-[#22d3ee]/70 via-[#22d3ee]/40 to-transparent border border-[#22d3ee]/50 transition-all duration-300">
                    <span class="relative z-10">Verify and create account</span>
                    <span class="absolute inset-0 bg-cyan-400/10 blur-xl opacity-50"></span>
                </button>
            </form>

            <form method="POST" action="{{ route('registration.otp.resend') }}" class="mt-6 sm:mt-3 text-center text-sm">
                @csrf
                <p class="mb-0 text-gray-600 dark:text-white">
                    Didn't receive the code?
                    <button type="submit" class="text-[#22d3ee] font-semibold hover:underline ml-1">Resend code</button>
                </p>
            </form>

            <a href="{{ route('register') }}" class="mt-4 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-[#22d3ee] dark:text-gray-400">
                <iconify-icon icon="heroicons:arrow-left"></iconify-icon>
                Back to sign up
            </a>
        </div>
    </div>
</section>

<script>
    (function() {
        const canvas = document.getElementById('starfield-canvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let width;
        let height;
        let particles = [];
        let fallers = [];
        const random = (min, max) => Math.random() * (max - min) + min;
        const isDark = () => document.documentElement.classList.contains('dark');

        function makeParticle() {
            return {
                x: random(0, width),
                y: random(0, height),
                r: random(0.4, 1.2),
                alpha: random(0.2, 0.7),
                vx: random(-0.1, 0.1),
                vy: random(-0.1, 0.1),
                dAlpha: random(0.002, 0.004) * (Math.random() < 0.5 ? 1 : -1),
                minA: 0.1,
                maxA: 0.8
            };
        }

        function makeFaller() {
            const goRight = Math.random() < 0.5;
            return {
                x: random(0, width),
                y: random(0, height * 0.4),
                r: random(1.8, 3.2),
                vx: goRight ? random(1.2, 2.5) : random(-2.5, -1.2),
                vy: random(2.5, 4.5),
                alpha: 1,
                trail: [],
                done: false
            };
        }

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            particles = Array.from({
                length: 60
            }, makeParticle);
        }

        function draw() {
            ctx.clearRect(0, 0, width, height);
            if (!isDark()) {
                requestAnimationFrame(draw);
                return;
            }

            particles.forEach(particle => {
                particle.alpha += particle.dAlpha;
                if (particle.alpha >= particle.maxA || particle.alpha <= particle.minA) particle.dAlpha *= -1;
                particle.x = (particle.x + particle.vx + width) % width;
                particle.y = (particle.y + particle.vy + height) % height;
                ctx.beginPath();
                ctx.arc(particle.x, particle.y, particle.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255,255,255,${particle.alpha})`;
                ctx.fill();
            });

            fallers.forEach(faller => {
                faller.trail.push({
                    x: faller.x,
                    y: faller.y
                });
                if (faller.trail.length > 25) faller.trail.shift();
                faller.x += faller.vx;
                faller.y += faller.vy;
                faller.alpha -= 0.01;
                faller.done = faller.alpha <= 0 || faller.y > height;

                faller.trail.forEach((point, index) => {
                    if (!index) return;
                    const strength = index / faller.trail.length;
                    ctx.beginPath();
                    ctx.moveTo(faller.trail[index - 1].x, faller.trail[index - 1].y);
                    ctx.lineTo(point.x, point.y);
                    ctx.strokeStyle = `rgba(255,255,255,${strength * faller.alpha})`;
                    ctx.lineWidth = strength * 2.2;
                    ctx.stroke();
                });
            });

            fallers = fallers.filter(faller => !faller.done);
            requestAnimationFrame(draw);
        }

        function spawnFaller() {
            if (isDark()) fallers.push(makeFaller());
            setTimeout(spawnFaller, random(4000, 7000));
        }

        window.addEventListener('resize', resize);
        resize();
        draw();
        setTimeout(spawnFaller, random(1500, 3000));
    })();
</script>
@endsection