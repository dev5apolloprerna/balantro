@extends('layouts.super_admin')

@section('content')
<div class="lg:col-span-3">
    <div class="shadow p-3">

        <div class="mb-3 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Profile
                </h2>

                <p class="text-gray-600 dark:text-gray-400">
                    View your company profile information
                </p>
            </div>

            <a href="{{ route('profile.edit', $profile) }}"
                class="rounded-md border border-cyan-500 text-cyan-400 px-4 py-2 text-sm
                hover:bg-cyan-500 hover:text-white transition-all duration-300">
                Edit Profile
            </a>
        </div>

        <style>
            .erp-view-card {
                @apply rounded-2xl border border-cyan-400/10 bg-white/5 backdrop-blur-xl p-6;
            }

            .erp-view-title {
                @apply text-xl font-semibold text-cyan-400 border-b border-cyan-400/20 pb-3 mb-6;
            }

            .erp-view-label {
                @apply text-sm font-medium text-gray-900 dark:text-white;
            }

            .erp-view-value {
                @apply text-sm md:text-base dark:text-white text-gray-900 font-medium break-words;
            }
        </style>

        @php
            $profile = auth()->user()->profile;

            $defaultImage = $profile->gender == 'female'
                ? 'images/female.png'
                : 'images/male.png';
        @endphp

        <!-- Profile Image -->
        <div class="flex flex-col items-center mb-8">
            <img src="{{ $profile->profile_image ? asset($profile->profile_image) : asset($defaultImage) }}"
                class="w-36 h-36 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-lg">

            <h3 class="mt-4 text-xl font-bold dark:text-gray-100">
                {{ auth()->user()->name }}
            </h3>

            <p class="dark:text-gray-100 text-sm">
                {{ ucfirst($profile->business_type ?? '-') }}
            </p>
        </div>

        <div class="space-y-8">

            <!-- Basic Details -->
            <div class="rounded-2xl border border-cyan-400/10 bg-white/5 backdrop-blur-xl p-6">
                <h3 class="text-xl font-semibold text-cyan-400 border-b border-cyan-400/20 pb-3 mb-6">
                    Basic Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Legal Name</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ auth()->user()->name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Trade Name</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $profile->trade_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Short Name</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $profile->short_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Business Type</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">
                            {{ ucfirst(str_replace('_', ' ', $profile->business_type ?? '-')) }}
                        </p>
                    </div>

                </div>
            </div>

            <!-- Contact Details -->
            <div class="rounded-2xl border border-cyan-400/10 bg-white/5 backdrop-blur-xl p-6">
                <h3 class="text-xl font-semibold text-cyan-400 border-b border-cyan-400/20 pb-3 mb-6">
                    Contact Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Email</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ auth()->user()->email ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Alternative Email</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $profile->alternative_email ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Mobile No</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $profile->mobile_no ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Whatsapp No</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $profile->whatsapp_no ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Address Line 1</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $profile->address ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Address Line 2</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $profile->address_2 ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">City</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $cityName  ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">District</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $district_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">State</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">
                            {{ $stateName ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Pincode</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $profile->pincode ?? '-' }}</p>
                    </div>

                </div>
            </div>

            <!-- Statutory Details -->
            <div class="rounded-2xl border border-cyan-400/10 bg-white/5 backdrop-blur-xl p-6">
                <h3 class="text-xl font-semibold text-cyan-400 border-b border-cyan-400/20 pb-3 mb-6">
                    Statutory Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">PAN No</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $profile->pan_no ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">GST No</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $profile->gst_no ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">TAN No</p>
                        <p class="text-sm md:text-base dark:text-white text-gray-900 font-medium break-words">{{ $profile->TAN_no ?? '-' }}</p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection