@extends('auth.layouts.app')

@section('title', 'Verify Email')

@section('content')
<section class="bg-white dark:bg-black flex min-h-[calc(100vh-64px)] items-center justify-center px-4">
    <div class="w-full max-w-md rounded-2xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-gray-800 dark:bg-gray-950">
        <div class="text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-cyan-100 text-cyan-600 dark:bg-cyan-950 dark:text-cyan-300">
                <iconify-icon icon="mage:email" class="text-3xl"></iconify-icon>
            </div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Verify your email</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Enter the 6-digit code sent to <strong>{{ $email }}</strong>.</p>
        </div>

        @if (session('status'))
            <div class="mt-5 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('registration.otp.verify') }}" class="mt-6">
            @csrf
            <label for="otp" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Verification code</label>
            <input id="otp" name="otp" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" required autofocus
                class="mt-2 h-14 w-full rounded-lg border border-neutral-300 bg-gray-50 text-center text-2xl tracking-[0.5em] text-gray-900 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            @error('otp')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <button type="submit" class="mt-6 h-12 w-full rounded-lg border border-cyan-400/50 bg-gradient-to-r from-cyan-500 to-cyan-400 font-medium text-white transition hover:opacity-90">Verify and create account</button>
        </form>

        <form method="POST" action="{{ route('registration.otp.resend') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-sm font-medium text-cyan-600 hover:underline dark:text-cyan-300">Resend code</button>
        </form>
    </div>
</section>
@endsection