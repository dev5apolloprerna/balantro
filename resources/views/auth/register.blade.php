@extends('auth.layouts.app')

@section('title', 'Sign Up')

@section('content')
<section class="bg-white dark:bg-[rgb(39,49,66)] flex flex-wrap min-h-screen theme-transition">
    <div class="lg:w-1/2 lg:block hidden">
        <div class="flex flex-col">
            <img src="{{ asset('assets/images/auth-img.png') }}" alt="Auth Image" class="max-w-full h-auto shadow-lg">
        </div>
    </div>
    <div class="font-sans w-full lg:w-1/2 py-8 px-4 sm:px-6 flex flex-col justify-center">
        <div class="w-full max-w-md mx-auto lg:max-w-[464px] px-4 sm:px-6">
            <div class="text-center">
                <a href="/" class="mb-6 block max-w-[200px] sm:max-w-[290px] mx-auto lg:mx-0">
                    <!-- Light logo -->
                    <img src="{{ asset('assets/images/light-logo.svg') }}" alt="Balantro" class="w-full block dark:hidden">
                    <!-- Dark logo -->
                    <img src="{{ asset('assets/images/dark-logo.svg') }}" alt="Balantro" class="w-full hidden dark:block">
                </a>
                <p class="mb-6 text-secondary-light text-base sm:text-lg">Create your account</p>
                
                <!-- Theme Toggle Button -->
                <!-- <div class="flex justify-center mb-4">
                    <button id="theme-toggle" type="button" class="rounded-full p-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-300">
                        <iconify-icon icon="heroicons:sun-20-solid" class="w-5 h-5 text-yellow-500 block dark:hidden"></iconify-icon>
                        <iconify-icon icon="heroicons:moon-20-solid" class="w-5 h-5 text-indigo-400 hidden dark:block"></iconify-icon>
                        <span class="sr-only">Toggle theme</span>
                    </button>
                </div> -->
                
                @if ($errors->any())
                    <div class="mb-4 flex items-start gap-3 w-full rounded-lg px-4 py-3 text-base sm:text-lg text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20">
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
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <!-- Name Field -->
                <div class="relative mb-4 sm:mb-6">
                    <div class="icon-field relative">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 flex text-xl text-neutral-500 dark:text-white h-[26px] items-center pointer-events-none">
                            <iconify-icon icon="f7:person" class="flex items-center"></iconify-icon>
                        </span>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Full Name" class="form-control h-[48px] sm:h-[55px] ps-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg w-full text-sm sm:text-base text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    @error('name')
                        <p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email Field -->
                <div class="relative mb-4 sm:mb-6">
                    <div class="icon-field relative">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 flex text-xl text-neutral-500 dark:text-white h-[26px] items-center pointer-events-none">
                            <iconify-icon icon="mage:email" class="flex items-center"></iconify-icon>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email" class="form-control h-[48px] sm:h-[55px] ps-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg w-full text-sm sm:text-base text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    @error('email')
                        <p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password Field -->
                <div class="mb-4 sm:mb-6 relative">
                    <div class="icon-field relative mt-2">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none text-xl text-neutral-500 dark:text-white">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Password" class="form-control h-[48px] sm:h-[56px] ps-11 pe-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg w-full text-gray-900 dark:text-white text-sm sm:text-base focus:ring-primary-500 focus:border-primary-500">
                        <span class="toggle-password absolute end-4 top-1/2 -translate-y-1/2 text-secondary-light mt-[-2px] cursor-pointer" data-toggle="#password">
                            <iconify-icon icon="ri:eye-line" class="text-xl"></iconify-icon>
                        </span>
                    </div>
                    @error('password')
                        <p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password Confirmation Field -->
                <div class="mb-4 sm:mb-6 relative">
                    <div class="icon-field relative mt-2">
                        <span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none text-xl text-neutral-500 dark:text-white">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password" class="form-control h-[48px] sm:h-[56px] ps-11 pe-11 border border-neutral-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg w-full text-gray-900 dark:text-white text-sm sm:text-base focus:ring-primary-500 focus:border-primary-500">
                        <span class="toggle-password absolute end-4 top-1/2 -translate-y-1/2 text-secondary-light mt-[-2px] cursor-pointer" data-toggle="#password_confirmation">
                            <iconify-icon icon="ri:eye-line" class="text-xl"></iconify-icon>
                        </span>
                    </div>
                    @error('password_confirmation')
                        <p class="form-error text-xs sm:text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" class="mt-6 sm:mt-8 w-full h-11 sm:h-12 bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm sm:text-base px-5 py-2.5 text-center text-white dark:focus:ring-primary-800 transition-colors duration-300">
                    Sign Up
                </button>
                
                <div class="mt-6 sm:mt-8 text-center text-sm">
                    <p class="mb-0 text-gray-600 dark:text-gray-400">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 font-semibold hover:underline ml-1">Sign in</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
