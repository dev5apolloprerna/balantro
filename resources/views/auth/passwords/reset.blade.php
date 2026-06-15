@extends('layouts.mailer')

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

            {{-- Top-level errors summary (optional but helpful) --}}
            @if ($errors->any())
                <div
                    class="mb-4 flex gap-3 rounded-xl px-4 py-3 text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20">
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

            <div
                class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/60 shadow-xl backdrop-blur supports-backdrop-blur:backdrop-blur">
                <div class="p-5 sm:p-6">
                    <form method="POST" action="{{ route('password.update') }}" x-data="{ sh1: false, sh2: false }" novalidate>
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
                                required autocomplete="email" placeholder="Email" @class([
                                    'block w-full rounded-lg ps-10 pe-3 py-3.5',
                                    'bg-white dark:bg-gray-800',
                                    'border border-neutral-300 dark:border-gray-700',
                                    'text-sm sm:text-base text-gray-900 dark:text-white',
                                    'placeholder:text-gray-400 dark:placeholder:text-gray-500',
                                    'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                                    'transition',
                                ])
                                aria-invalid="@error('email') true @else false @enderror"
                                aria-describedby="@error('email') email-error @enderror" />
                        </div>
                        @error('email')
                            <p id="email-error" class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">
                                {{ $message }}</p>
                        @enderror

                        {{-- New password --}}
                        <div class="mt-4">
                            <label for="password" class="sr-only">New password</label>
                            <div class="relative">
                                <span
                                    class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-neutral-500 dark:text-neutral-300">
                                    <iconify-icon icon="heroicons:key-20-solid" class="h-5 w-5"></iconify-icon>
                                </span>
                                <input :type="sh1 ? 'text' : 'password'" id="password" name="password" required
                                    autocomplete="new-password" placeholder="New password" @class([
                                        'block w-full rounded-lg ps-10 pe-12 py-3.5',
                                        'bg-white dark:bg-gray-800',
                                        'border border-neutral-300 dark:border-gray-700',
                                        'text-sm sm:text-base text-gray-900 dark:text-white',
                                        'placeholder:text-gray-400 dark:placeholder:text-gray-500',
                                        'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                                        'transition',
                                    ])
                                    aria-invalid="@error('password') true @else false @enderror"
                                    aria-describedby="@error('password') password-error @enderror" />
                                <button type="button"
                                    class="absolute inset-y-0 end-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                    @click="sh1 = !sh1" aria-label="Toggle password visibility">
                                    <iconify-icon :icon="sh1 ? 'heroicons:eye-slash-20-solid' : 'heroicons:eye-20-solid'"
                                        class="h-5 w-5"></iconify-icon>
                                </button>
                            </div>
                            @error('password')
                                <p id="password-error" class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">
                                    {{ $message }}</p>
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
                                <input :type="sh2 ? 'text' : 'password'" id="password_confirmation"
                                    name="password_confirmation" required autocomplete="new-password"
                                    placeholder="Confirm new password"
                                    class="block w-full rounded-lg ps-10 pe-12 py-3.5 bg-white dark:bg-gray-800 border border-neutral-300 dark:border-gray-700 text-sm sm:text-base text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition" />
                                <button type="button"
                                    class="absolute inset-y-0 end-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                    @click="sh2 = !sh2" aria-label="Toggle confirm password visibility">
                                    <iconify-icon :icon="sh2 ? 'heroicons:eye-slash-20-solid' : 'heroicons:eye-20-solid'"
                                        class="h-5 w-5"></iconify-icon>
                                </button>
                            </div>
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
@endsection
