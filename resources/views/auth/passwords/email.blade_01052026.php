@extends('auth.layouts.app')

@section('title', 'Forgot Password')

@section('content')
    <section class="min-h-screen bg-white dark:bg-[rgb(39,49,66)] theme-transition grid grid-cols-1 lg:grid-cols-2">
        <!-- Left image (large screens) -->
        <div class="relative hidden lg:block">
            <img src="{{ asset('assets/images/auth-img.png') }}" alt="Illustration related to secure access"
                class="absolute inset-0 h-full w-full object-cover shadow-lg" loading="eager" />
        </div>

        <!-- Right content -->
        <div class="flex items-center justify-center py-10 sm:py-12 px-4 sm:px-6">
            <div class="w-full max-w-md lg:max-w-[464px]">
                <div class="text-center lg:text-left">
                    <a href="/" class="mb-6 block w-[200px] sm:w-[290px] mx-auto lg:mx-0">
                        <!-- Light logo -->
                        <img src="{{ asset('assets/images/light-logo.svg') }}" alt="Balantro"
                            class="w-full block dark:hidden">
                        <!-- Dark logo -->
                        <img src="{{ asset('assets/images/dark-logo.svg') }}" alt="Balantro"
                            class="w-full hidden dark:block">
                    </a>

                    <h1 class="mb-2 text-2xl font-semibold text-gray-900 dark:text-white">
                        Forgot your password?
                    </h1>
                    <p class="mb-6 text-sm sm:text-base text-gray-600 dark:text-gray-300">
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
                            class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-neutral-500 dark:text-neutral-300">
                            <iconify-icon icon="mage:email" class="h-5 w-5"></iconify-icon>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            autocomplete="email" placeholder="Email" @class([
                                'block w-full rounded-lg',
                                'bg-white dark:bg-gray-800',
                                'border border-neutral-300 dark:border-gray-700',
                                'ps-10 pe-3 py-3.5',
                                'text-sm sm:text-base text-gray-900 dark:text-white',
                                'placeholder:text-gray-400 dark:placeholder:text-gray-500',
                                'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500',
                                'transition',
                            ])
                            aria-invalid="@error('email') true @else false @enderror"
                            aria-describedby="email-help @error('email') email-error @enderror" />
                    </div>
                    <div id="email-help" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        We’ll never share your email.
                    </div>
                    {{-- @error('email')
                        <p id="email-error" class="mt-1 text-xs sm:text-sm text-red-600 dark:text-red-400">{{ $message }}
                        </p>
                    @enderror --}}

                    <button type="submit"
                        class="mt-5 sm:mt-6 inline-flex w-full items-center justify-center rounded-lg px-5 py-3
                           text-sm font-medium text-white
                           bg-primary-600 hover:bg-primary-700
                           focus:outline-none focus:ring-2 focus:ring-primary-500
                           disabled:opacity-60 disabled:cursor-not-allowed transition">
                        Send reset password instructions
                    </button>

                    <div class="mt-5 sm:mt-6 text-center">
                        <a href="{{ route('login') }}"
                            class="text-primary-600 dark:text-primary-400 hover:underline text-sm sm:text-base">
                            Back to Sign In
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </section>
@endsection
