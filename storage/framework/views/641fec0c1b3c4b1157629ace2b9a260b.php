<?php $__env->startSection('title', 'Login'); ?>
<?php $__env->startSection('field_validation_only', true); ?>

<?php $__env->startSection('content'); ?>
    <style>
        /* .form-control:focus {
    border-color: #22d3ee !important;
    box-shadow: 0 0 0 2px #22d3ee !important;
    outline: none !important;
}

.dark .form-control:focus {
    border-color: #22d3ee !important;
    box-shadow: 0 0 0 2px #22d3ee !important;
} */
    </style>
    <style>
        /* Consolidated autofill fix */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        /* .form-control:-webkit-autofill:focus, */
        .form-control:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px rgb(249 250 251) inset !important;
            -webkit-text-fill-color: rgb(17 24 39) !important;
            border-color: #22d3ee !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* .form-control:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px rgb(249 250 251) inset, 0 0 0 2px #22d3ee !important;
            border-color:#22d3ee !important;
        } */

        /* Dark mode autofill - Pure Black */
        .dark .form-control:-webkit-autofill,
        .dark .form-control:-webkit-autofill:hover,
        /* .dark .form-control:-webkit-autofill:focus, */
        .dark .form-control:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #000000 inset !important;
            -webkit-text-fill-color: rgb(255 255 255) !important;
            border-color: #22d3ee !important;
        }

        /* .dark .form-control:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #000000 inset, 0 0 0 2px #22d3ee !important;
            border-color: #22d3ee !important;
        } */
    </style>

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

    <section class="bg-white dark:bg-black flex flex-wrap min-h-[calc(100vh-64px)]">
        <canvas id="starfield-canvas"></canvas>
       <div class="lg:w-3/5 lg:block hidden">
            <div class="flex items-center justify-center h-full bg-white dark:bg-black">

                <!-- Light Logo (shown in light mode) -->
                <img src="<?php echo e(asset('assets/images/light-logo_login.svg')); ?>"
                    alt="Balantro"
                    class="w-96 h-auto block dark:hidden">

                <!-- Dark Logo (shown in dark mode) -->
                <img src="<?php echo e(asset('assets/images/dark-logo_login.svg')); ?>"
                    alt="Balantro"
                    class="w-96 h-auto hidden dark:block">

            </div>
        </div>
        <div class="font-sans w-full lg:w-2/5 py-8 px-4 sm:px-6 flex flex-col justify-center dark:bg-black">
            <div class="w-full max-w-md mx-auto px-4 sm:px-6 flex flex-col items-center">
                <div class="text-center">
                    
                    <p class="mb-6 text-secondary-light dark:text-white text-base sm:text-lg">Sign in to your account</p>

                    <!-- Session Status -->
                    <?php if(session('status')): ?>
                        <div
                            class="mb-4 flex items-start gap-3 w-full rounded-lg px-4 py-3 text-base sm:text-lg text-green-600 dark:text-green-400 bg-green-50 dark:bg-gray-900">
                            <div class="pt-0.5">
                                <iconify-icon icon="heroicons:check-circle" class="w-5 h-5"></iconify-icon>
                            </div>
                            <div class="flex-1 leading-relaxed">
                                <?php echo e(session('status')); ?>

                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Rest of your form content remains exactly the same -->
                <form method="POST" id="addLoginForm" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="relative mb-4 sm:mb-6 mx-auto">
                        <div class="icon-field relative">
                            <span
                                class="absolute start-4 top-1/2 -translate-y-1/2 flex text-xl text-neutral-500 dark:text-white h-[26px] items-center pointer-events-none">
                                <iconify-icon icon="mage:email" class="flex items-center"></iconify-icon>
                            </span>
                            <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" required
                                autofocus autocomplete="email" placeholder="Email"
                                class="form-control h-[48px] sm:h-[50px] ps-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-lg w-[350px] text-sm sm:text-base text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#22d3ee]/40 focus:border-[#22d3ee]/50">
                        </div>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-4 sm:mb-6 relative">
						<div class="icon-field relative mt-2">
							<span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none text-xl text-neutral-500 dark:text-white">
								<!-- Lock Icon -->
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
								</svg>
							</span>
							<input id="password" type="password" name="password" required autocomplete="current-password"
								placeholder="Password"
								class="form-control h-[48px] sm:h-[50px] ps-11 pe-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-lg w-[350px] text-gray-900 dark:text-white text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-[#22d3ee]/40 focus:border-[#22d3ee]/50">
							<button type="button"
                                class="password-visibility-toggle absolute end-2 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center text-secondary-light dark:text-gray-300"
                                aria-label="Show password" aria-pressed="false">
								<svg class="password-visible-icon h-5 w-5 shrink-0 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
								</svg>
							    <svg class="password-hidden-icon hidden h-5 w-5 shrink-0 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.585 10.587a2 2 0 002.828 2.828M9.363 5.365A9.466 9.466 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.486 9.486 0 01-2.264 3.592M6.61 6.611A9.53 9.53 0 002.458 12C3.732 16.057 7.522 19 12 19a9.45 9.45 0 004.057-.908"/>
                                </svg>
							</button>
						</div>
						<?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
							<p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1"><?php echo e($message); ?></p>
						<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
					</div>

                    <div class="mt-5 sm:mt-5">
                        <div class="flex justify-between items-center w-full gap-4">
                            <div class="flex items-center shrink-0">
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="cursor-pointer rounded border-neutral-300 dark:border-gray-700 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:bg-gray-900">
                                <label for="remember_me"
                                    class="ps-2 text-neutral-600 dark:text-gray-300 text-sm  whitespace-nowrap">Remember
                                    me</label>
                            </div>
                            <?php if(Route::has('password.request')): ?>
                                <a href="<?php echo e(route('password.request')); ?>"
                                    class="text-[#22d3ee] dark:text-[#22d3ee] font-medium hover:underline text-sm  whitespace-nowrap text-left">Forgot
                                    your password?</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit"
                        class="mt-6 w-[350px] h-[50px] 
                        flex items-center justify-center

                        text-white font-medium text-sm sm:text-base 
                        rounded-lg relative overflow-hidden

                        bg-gradient-to-r from-[#22d3ee]/70 via-[#22d3ee]/40 to-transparent

                        border border-[#22d3ee]/50

                        

                        transition-all duration-300">

                            <span class="relative z-10">Sign in</span>

                            <!-- constant glow layer -->
                            <span class="absolute inset-0 bg-cyan-400/10 blur-xl opacity-50"></span>
                        </button>
                </form>

                <div class="mt-6 sm:mt-3 text-center text-sm">
                    <p class="mb-0 text-gray-600 dark:text-white">
                        Don't have an account?
                        <a href="<?php echo e(route('register')); ?>"
                            class="text-[#22d3ee] dark:text-[#22d3ee] font-semibold hover:underline ml-1">Sign up</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const togglePassword = document.querySelector('.password-visibility-toggle');
    const passwordInput = document.getElementById('password');
    const visibleIcon = togglePassword?.querySelector('.password-visible-icon');
    const hiddenIcon = togglePassword?.querySelector('.password-hidden-icon');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            // Toggle password visibility
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            
            visibleIcon?.classList.toggle('hidden', isPassword);
            hiddenIcon?.classList.toggle('hidden', !isPassword);
            togglePassword.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            togglePassword.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
        });
    }

    // Autofill handling
    function handleAutofill() {
        const inputs = document.querySelectorAll('#email, #password');
        inputs.forEach(input => {
            // Check for autofill
            setTimeout(() => {
                const isAutofilled = input.matches(':-webkit-autofill') || 
                                   input.matches(':autofill') ||
                                   getComputedStyle(input).backgroundColor !== 'rgba(0, 0, 0, 0)';
                
                if (isAutofilled) {
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    if (isDarkMode) {
                        input.style.backgroundColor = '#000000';
                        input.style.color = 'rgb(255 255 255)';
                    } else {
                        input.style.backgroundColor = 'rgb(249 250 251)';
                        input.style.color = 'rgb(17 24 39)';
                    }
                }
            }, 100);
        });
    }

    // Initialize autofill handling
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/auth/login.blade.php ENDPATH**/ ?>