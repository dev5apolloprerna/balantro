@extends('auth.layouts.app')

@section('title', 'Login')

@section('content')

    <style>
        /* Consolidated autofill fix */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus,
        .form-control:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px rgb(249 250 251) inset !important;
            -webkit-text-fill-color: rgb(17 24 39) !important;
            border-color: rgb(209 213 219) !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .form-control:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px rgb(249 250 251) inset, 0 0 0 2px rgb(59 130 246) !important;
            border-color: rgb(59 130 246) !important;
        }

        /* Dark mode autofill - Pure Black */
        .dark .form-control:-webkit-autofill,
        .dark .form-control:-webkit-autofill:hover,
        .dark .form-control:-webkit-autofill:focus,
        .dark .form-control:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #000000 inset !important;
            -webkit-text-fill-color: rgb(255 255 255) !important;
            border-color: rgb(55 65 81) !important;
        }

        .dark .form-control:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #000000 inset, 0 0 0 2px rgb(59 130 246) !important;
            border-color: rgb(59 130 246) !important;
        }
    </style>

    <section class="bg-white dark:bg-black flex flex-wrap min-h-[calc(100vh-64px)]">
        <div class="lg:w-1/2 lg:block hidden">
            <div class="flex flex-col">
                <img src="{{ asset('assets/images/auth-img.png') }}" alt="Auth Image" class="max-w-full h-auto shadow-lg">
            </div>
        </div>
        <div class="font-sans w-full lg:w-1/2 py-8 px-4 sm:px-6 flex flex-col justify-center dark:bg-black">
            <div class="w-full max-w-md mx-auto lg:max-w-[464px] px-4 sm:px-6">
                <div class="text-center">
                    <a href="/" class="mb-6 block max-w-[200px] sm:max-w-[290px] mx-auto lg:mx-0"
                        style="display: inline-block;">
                        <!-- Light logo -->
                        <img src="{{ asset('assets/images/light-logo.svg') }}" alt="Balantro" class="h-8 block dark:hidden">
                        <!-- Dark logo -->
                        <img src="{{ asset('assets/images/dark-logo.svg') }}" alt="Balantro" class="h-8 hidden dark:block">
                    </a>
                    <p class="mb-6 text-secondary-light text-base sm:text-lg">Sign in to your account</p>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div
                            class="mb-4 flex items-start gap-3 w-full rounded-lg px-4 py-3 text-base sm:text-lg text-green-600 dark:text-green-400 bg-green-50 dark:bg-gray-900">
                            <div class="pt-0.5">
                                <iconify-icon icon="heroicons:check-circle" class="w-5 h-5"></iconify-icon>
                            </div>
                            <div class="flex-1 leading-relaxed">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div
                            class="mb-4 flex items-start gap-3 w-full rounded-lg px-4 py-3 text-base sm:text-lg text-red-600 dark:text-red-400 bg-red-50 dark:bg-gray-900">
                            <div class="pt-0.5">
                                <iconify-icon icon="heroicons:exclamation-circle" class="w-5 h-5"></iconify-icon>
                            </div>
                            <div class="flex-1 leading-relaxed">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Rest of your form content remains exactly the same -->
                <form method="POST" id="addLoginForm" action="{{ route('login') }}">
                    @csrf
                    <div class="relative mb-4 sm:mb-6">
                        <div class="icon-field relative">
                            <span
                                class="absolute start-4 top-1/2 -translate-y-1/2 flex text-xl text-neutral-500 dark:text-white h-[26px] items-center pointer-events-none">
                                <iconify-icon icon="mage:email" class="flex items-center"></iconify-icon>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus autocomplete="email" placeholder="Email"
                                class="form-control h-[48px] sm:h-[55px] ps-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-lg w-full text-sm sm:text-base text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        @error('email')
                            <p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror
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
								class="form-control h-[48px] sm:h-[56px] ps-11 pe-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-lg w-full text-gray-900 dark:text-white text-sm sm:text-base focus:ring-primary-500 focus:border-primary-500">
							<span class="toggle-password absolute end-4 top-1/2 -translate-y-1/2 text-secondary-light mt-[-2px] cursor-pointer">
								<!-- Eye Icon - will be toggled via JavaScript -->
								<svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
								</svg>
							</span>
						</div>
						@error('password')
							<p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
						@enderror
					</div>

                    <div class="mt-5 sm:mt-7">
                        <div class="flex justify-between items-center w-full gap-4">
                            <div class="flex items-center shrink-0">
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="cursor-pointer rounded border-neutral-300 dark:border-gray-700 text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:bg-gray-900">
                                <label for="remember_me"
                                    class="ps-2 text-neutral-600 dark:text-gray-300 text-sm sm:text-base whitespace-nowrap">Remember
                                    me</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-primary-600 dark:text-primary-400 font-medium hover:underline text-sm sm:text-base whitespace-nowrap text-right">Forgot
                                    your password?</a>
                            @endif
                        </div>
                    </div>

                    <button type="submit"
                        class="mt-6 sm:mt-8 w-full h-11 sm:h-12 bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm sm:text-base px-5 py-2.5 text-center text-white dark:focus:ring-primary-800 transition-colors duration-300">
                        Sign in
                    </button>
                </form>

                <div class="mt-6 sm:mt-8 text-center text-sm">
                    <p class="mb-0 text-gray-600 dark:text-gray-400">
                        Don't have an account?
                        <a href="{{ route('register') }}"
                            class="text-primary-600 dark:text-primary-400 font-semibold hover:underline ml-1">Sign up</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            // Toggle password visibility
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            
            // Toggle eye icon using inline SVG paths
            if (eyeIcon) {
                if (isPassword) {
                    // Show eye-off icon
                    eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m9.018 9.018l3.211 3.211M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    `;
                } else {
                    // Show eye icon
                    eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    `;
                }
            }
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
@endsection
