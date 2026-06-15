@extends('layouts.app')

@section('content')
    <div
        class="min-h-[80vh] flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-matblack-900 dark:to-matblack-800 transition-colors duration-300 theme-transition">
        <div class="text-center px-4 sm:px-6 lg:px-8">
            <h1 class="text-center font-bold mb-8 text-gray-800 dark:text-white theme-transition"
                style="font-size: clamp(2rem,1.2rem + 4vw,4.5rem);">
                Welcome to Balantro
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 theme-transition max-w-2xl mx-auto">
                Discover amazing features and seamless experience with our platform.
            </p>
        </div>
    </div>
@endsection
