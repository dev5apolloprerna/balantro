@extends('auth.layouts.app')

@section('title', 'Sign Up')
@section('field_validation_only', true)

@section('content')
<style>
    .form-control:-webkit-autofill,
    .form-control:-webkit-autofill:hover,
    .form-control:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 1000px rgb(249 250 251) inset !important;
        -webkit-text-fill-color: rgb(17 24 39) !important;
        border-color: #22d3ee !important;
        transition: background-color 5000s ease-in-out 0s;
    }

    .dark .form-control:-webkit-autofill,
    .dark .form-control:-webkit-autofill:hover,
    .dark .form-control:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 1000px #000000 inset !important;
        -webkit-text-fill-color: rgb(255 255 255) !important;
        border-color: #22d3ee !important;
    }

    #starfield-canvas {
        position: fixed;
        top: 0;
        left: 0;
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

    .password-toggle-icon {
        display: block;
        overflow: visible;
    }
</style>

<section class="bg-white dark:bg-black flex flex-wrap min-h-[calc(100vh-64px)]">
    <canvas id="starfield-canvas"></canvas>

    <div class="lg:w-3/5 lg:block hidden">
        <div class="flex items-center justify-center h-full bg-white dark:bg-black">
            <img src="{{ asset('assets/images/light-logo_login.svg') }}"
                alt="Balantro"
                class="w-96 h-auto block dark:hidden">

            <img src="{{ asset('assets/images/dark-logo_login.svg') }}"
                alt="Balantro"
                class="w-96 h-auto hidden dark:block">
        </div>
    </div>

    <div class="font-sans w-full lg:w-2/5 py-8 px-4 sm:px-6 flex flex-col justify-center dark:bg-black">
        <div class="w-full max-w-md mx-auto px-4 sm:px-6 flex flex-col items-center">
            <div class="text-center">
                <p class="mb-6 text-secondary-light dark:text-white text-base sm:text-lg">Create your account</p>

               </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="relative mb-4 sm:mb-6 mx-auto">
                        <div class="icon-field relative">
                            <span
                                class="absolute start-4 top-1/2 -translate-y-1/2 flex text-xl text-neutral-500 dark:text-white h-[26px] items-center pointer-events-none">
                                <iconify-icon icon="f7:person" class="flex items-center"></iconify-icon>
                            </span>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                autocomplete="name" placeholder="Full Name"
                                class="form-control h-[48px] sm:h-[50px] ps-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-lg w-[350px] text-sm sm:text-base text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#22d3ee]/40 focus:border-[#22d3ee]/50">
                        </div>
                        @error('name')
                            <p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="relative mb-4 sm:mb-6 mx-auto">
                        <div class="icon-field relative">
                            <span
                                class="absolute start-4 top-1/2 -translate-y-1/2 flex text-xl text-neutral-500 dark:text-white h-[26px] items-center pointer-events-none">
                                <iconify-icon icon="mage:email" class="flex items-center"></iconify-icon>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email" placeholder="Email"
                                class="form-control h-[48px] sm:h-[50px] ps-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-lg w-[350px] text-sm sm:text-base text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#22d3ee]/40 focus:border-[#22d3ee]/50">
                        </div>
                        @error('email')
                            <p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4 sm:mb-6 relative">
                        <div class="icon-field relative mt-2">
                            <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none text-xl text-neutral-500 dark:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                placeholder="Password"
                                class="form-control h-[48px] sm:h-[50px] ps-11 pe-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-lg w-[350px] text-gray-900 dark:text-white text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#22d3ee]/40 focus:border-[#22d3ee]/50">
                            <button type="button" class="toggle-password absolute end-3 top-1/2 -translate-y-1/2 inline-flex h-8 w-8 items-center justify-center text-secondary-light dark:text-white" data-toggle="password" aria-label="Show password" aria-pressed="false">
                                <svg class="eye-icon password-toggle-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4 sm:mb-6 relative">
                        <div class="icon-field relative mt-2">
                            <button type="button" class="toggle-password absolute end-3 top-1/2 -translate-y-1/2 inline-flex h-8 w-8 items-center justify-center text-secondary-light dark:text-white" data-toggle="password_confirmation" aria-label="Show confirm password" aria-pressed="false">
                                <svg class="eye-icon password-toggle-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </button>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                placeholder="Confirm Password"
                                class="form-control h-[48px] sm:h-[50px] ps-11 pe-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-lg w-[350px] text-gray-900 dark:text-white text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#22d3ee]/40 focus:border-[#22d3ee]/50">
                            <span class="toggle-password absolute end-4 top-1/2 -translate-y-1/2 text-secondary-light dark:text-white mt-[-2px] cursor-pointer" data-toggle="password_confirmation">
                                <svg class="eye-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </span>
                        </div>
                        @error('password_confirmation')
                            <p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="mt-6 w-[350px] h-[50px] flex items-center justify-center text-white font-medium text-sm sm:text-base rounded-lg relative overflow-hidden bg-gradient-to-r from-[#22d3ee]/70 via-[#22d3ee]/40 to-transparent border border-[#22d3ee]/50 transition-all duration-300">
                        <span class="relative z-10">Sign Up</span>
                        <span class="absolute inset-0 bg-cyan-400/10 blur-xl opacity-50"></span>
                    </button>
                </form>

                <div class="mt-6 sm:mt-3 text-center text-sm">
                    <p class="mb-0 text-gray-600 dark:text-white">
                        Already have an account?
                        <a href="{{ route('login') }}"
                            class="text-[#22d3ee] dark:text-[#22d3ee] font-semibold hover:underline ml-1">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
</section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const eyeOpen = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            `;
            const eyeClosed = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 002.458 12C3.732 16.057 7.523 19 12 19c1.624 0 3.162-.387 4.522-1.074M6.228 6.228A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.513 10.513 0 01-1.274 2.592M6.228 6.228L3 3m3.228 3.228 3.65 3.65m6.244 6.244L21 21m-4.878-4.878-2.001-2.001m0 0A3 3 0 009.88 9.88m4.242 4.242L9.88 9.88"/>
            `;

            document.querySelectorAll('.toggle-password').forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const passwordInput = document.getElementById(this.dataset.toggle);
                    const eyeIcon = this.querySelector('.eye-icon');
                    if (!passwordInput || !eyeIcon) return;

                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';
                    eyeIcon.innerHTML = isPassword ? eyeClosed : eyeOpen;
                });
            });

            function handleAutofill() {
                const inputs = document.querySelectorAll('#name, #email, #password, #password_confirmation');
                inputs.forEach(input => {
                    setTimeout(() => {
                        const isAutofilled = input.matches(':-webkit-autofill') ||
                            input.matches(':autofill') ||
                            getComputedStyle(input).backgroundColor !== 'rgba(0, 0, 0, 0)';

                        if (isAutofilled) {
                            const isDarkMode = document.documentElement.classList.contains('dark');
                            input.style.backgroundColor = isDarkMode ? '#000000' : 'rgb(249 250 251)';
                            input.style.color = isDarkMode ? 'rgb(255 255 255)' : 'rgb(17 24 39)';
                        }
                    }, 100);
                });
            }

            handleAutofill();
            setInterval(handleAutofill, 1000);
        });
    </script>

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
                    r: rand(0.4, 1.2),
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
                    r: rand(1.8, 3.2),
                    vx: goRight ? rand(1.2, 2.5) : rand(-2.5, -1.2),
                    vy: rand(2.5, 4.5),
                    alpha: 1,
                    trail: [],
                    done: false,
                };
            }

            function resize() {
                W = canvas.width = window.innerWidth;
                H = canvas.height = window.innerHeight;
                particles = Array.from({ length: 60 }, makeParticle);
            }

            function draw() {
                ctx.clearRect(0, 0, W, H);

                if (!isDark()) {
                    requestAnimationFrame(draw);
                    return;
                }

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

                for (const f of fallers) {
                    f.trail.push({ x: f.x, y: f.y });
                    if (f.trail.length > 25) f.trail.shift();

                    f.x += f.vx;
                    f.y += f.vy;
                    f.alpha -= 0.01;

                    if (f.alpha <= 0 || f.y > H) {
                        f.done = true;
                        continue;
                    }

                    for (let i = 1; i < f.trail.length; i++) {
                        const t = i / f.trail.length;
                        ctx.beginPath();
                        ctx.moveTo(f.trail[i - 1].x, f.trail[i - 1].y);
                        ctx.lineTo(f.trail[i].x, f.trail[i].y);
                        ctx.strokeStyle = `rgba(255,255,255,${t * f.alpha})`;
                        ctx.lineWidth = t * 2.2;
                        ctx.stroke();
                    }

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
                setTimeout(spawnFaller, rand(4000, 7000));
            }

            window.addEventListener('resize', resize);

            resize();
            draw();
            setTimeout(spawnFaller, rand(1500, 3000));
        })();
    </script>

@endsection
