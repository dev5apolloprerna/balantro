@extends('auth.layouts.app')
@section('field_validation_only', true)

@section('content')
<section class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-10 sm:py-14">
    <div class="w-full max-w-md sm:max-w-lg">
        <div class="mb-6 text-center">
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                Reset your password
            </h1>
            <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-300">
                Set a new password for your Balantro account.
            </p>
        </div>

        <div
            class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/60 shadow-xl backdrop-blur supports-backdrop-blur:backdrop-blur">
            <div class="p-5 sm:p-6">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email --}}
                    <label for="email" class="sr-only">Email</label>
                    <div class="relative">
                        <span
                            class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-neutral-500 dark:text-neutral-300">
                            <iconify-icon icon="mage:email" class="h-5 w-5"></iconify-icon>
                        </span>
                        <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}"
                            required autocomplete="email" placeholder="Email" @class([ 'block w-full rounded-lg ps-10 pe-3 py-3.5' , 'bg-white dark:bg-gray-800' , 'border border-neutral-300 dark:border-gray-700' , 'text-sm sm:text-base text-gray-900 dark:text-white' , 'placeholder:text-gray-400 dark:placeholder:text-gray-500' , 'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500' , 'transition' ,
                            ])
                            aria-invalid="@error('email') true @else false @enderror"
                            aria-describedby="@error('email') email-error @enderror" />
                    </div>
                    @error('email')
                    <p id="email-error" class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">
                        {{ $message }}
                    </p>
                    @enderror

                    {{-- New password --}}
                    <div class="mt-4">
                        <label for="password" class="sr-only">New password</label>
                        <div class="relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-neutral-500 dark:text-neutral-300">
                                <iconify-icon icon="heroicons:key-20-solid" class="h-5 w-5"></iconify-icon>
                            </span>
                            <input type="password" id="password" name="password" required minlength="8"
                                pattern="(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}"
                                title="Use at least 8 characters, including a letter, a number, and a symbol."
                                autocomplete="new-password" placeholder="New password" @class([ 'block w-full rounded-lg ps-10 pe-12 py-3.5' , 'bg-white dark:bg-gray-800' , 'border border-neutral-300 dark:border-gray-700' , 'text-sm sm:text-base text-gray-900 dark:text-white' , 'placeholder:text-gray-400 dark:placeholder:text-gray-500' , 'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500' , 'transition' ,
                                ])
                                aria-invalid="@error('password') true @else false @enderror"
                                aria-describedby="@error('password') password-error @enderror" />
                            <button type="button"
                                class="password-visibility-toggle absolute inset-y-0 end-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                data-target="password" aria-label="Show password" aria-pressed="false">
                                <svg class="eye-open h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="eye-closed hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 4.24A10.4 10.4 0 0112 4c5 0 9.27 3.11 11 7.5a12.7 12.7 0 01-2.1 3.5M6.61 6.61A12.7 12.7 0 001 11.5C2.73 15.89 7 19 12 19a10.6 10.6 0 005.39-1.39" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                        <p id="password-error" class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Use at least 8 characters. Mix letters,
                            numbers, and symbols.</p>
                    </div>

                    {{-- Confirm password --}}
                    <div class="mt-4">
                        <label for="password_confirmation" class="sr-only">Confirm new password</label>
                        <div class="relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-neutral-500 dark:text-neutral-300">
                                <iconify-icon icon="heroicons:check-badge-20-solid" class="h-5 w-5"></iconify-icon>
                            </span>
                            <input type="password" id="password_confirmation"
                                name="password_confirmation" required minlength="8" autocomplete="new-password"
                                placeholder="Confirm new password"
                                class="block w-full rounded-lg ps-10 pe-12 py-3.5 bg-white dark:bg-gray-800 border border-neutral-300 dark:border-gray-700 text-sm sm:text-base text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" />
                            <button type="button"
                                class="password-visibility-toggle absolute inset-y-0 end-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                data-target="password_confirmation" aria-label="Show password confirmation" aria-pressed="false">
                                <svg class="eye-open h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="eye-closed hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 4.24A10.4 10.4 0 0112 4c5 0 9.27 3.11 11 7.5a12.7 12.7 0 01-2.1 3.5M6.61 6.61A12.7 12.7 0 001 11.5C2.73 15.89 7 19 12 19a10.6 10.6 0 005.39-1.39" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                        <p id="password-confirmation-error" class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="mt-6 inline-flex w-full items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-60 disabled:cursor-not-allowed transition">
                        Reset Password
                    </button>

                    <div class="mt-5 text-center">
                        <a href="{{ route('login') }}"
                            class="text-primary-600 dark:text-primary-400 hover:underline text-sm sm:text-base">
                            Back to Sign In
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Optional: small reassurance text --}}
        <p class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
            If you didn’t request a password reset, you can safely ignore this page.
        </p>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.password-visibility-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const input = document.getElementById(toggle.dataset.target);
                if (!input) return;

                const showPassword = input.type === 'password';
                input.type = showPassword ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', showPassword ? 'true' : 'false');
                toggle.setAttribute('aria-label', showPassword ?
                    `Hide ${input.id === 'password' ? 'password' : 'password confirmation'}` :
                    `Show ${input.id === 'password' ? 'password' : 'password confirmation'}`);
                toggle.querySelector('.eye-open').classList.toggle('hidden', showPassword);
                toggle.querySelector('.eye-closed').classList.toggle('hidden', !showPassword);
            });
        });
    });
</script>
@endsection