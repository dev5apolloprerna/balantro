<style>
.dark .fa-sun {
    display: none !important;
}
.fa-moon {
    display: none;
}
.dark .fa-moon {
    display: block !important;
}
</style>

<header class="h-[57px] border-b border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-black">
    <div class="flex h-full min-w-0 items-center justify-between gap-2 bg-white px-3 dark:bg-black">
        <div class="flex min-w-0 items-center">
            <button class="mr-1 shrink-0 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 p-2 rounded-lg sm:mr-4"
                id="toggle-sidebar">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <h1 class="hidden truncate text-lg font-semibold text-gray-700 dark:text-gray-300 sm:block">Welcome
            @if (auth()->user()->role == \App\Models\User::ROLES['client'])
                {{ auth()->user()->short_name ?? auth()->user()->name }}
            @else
                {{ auth()->user()->name }}
            @endif
            !</h1>
        </div>

    

        <div class="flex shrink-0 items-center gap-2 sm:gap-4">
            <!-- Theme Toggle -->
            <button id="theme-toggle" type="button"
                class="flex items-center justify-center p-2 rounded-lg text-gray-700 dark:text-gray-300  border border-gray-200 dark:border-gray-800
                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee] 
                                dark:hover:border-[#fbbf24]

                                hover:shadow-[0_0_15px_#22d3ee]
                                dark:hover:shadow-[0_0_15px_#fbbf24]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                {{-- <iconify-icon icon="heroicons:sun-20-solid" class="w-5 h-5 text-yellow-500 block dark:hidden">
                </iconify-icon> --}}
                <i class="fa-solid fa-sun w-5 h-5 text-yellow-500 block dark:hidden"></i>
                <i class="fa-solid fa-moon w-5 h-5 text-indigo-400 hidden dark:block"></i>
                {{-- <iconify-icon icon="heroicons:moon-20-solid" class="w-5 h-5 text-indigo-400 hidden dark:block">
                </iconify-icon> --}}
            </button>

            <!-- User Menu -->
            <div class="relative"  x-data="{ open: false }"
    x-on:close-profile-dropdown.window="open = false">
                <button id="user-menu-btn"
                    class="relative flex h-10 w-11 items-center justify-center rounded-lg border border-gray-200 bg-white px-2 dark:border-gray-800 dark:bg-black hover:bg-gray-100 dark:hover:bg-gray-900 md:w-52 md:px-8">
                    {{-- <div class="h-8 w-8 rounded-full bg-primary-100 dark:bg-gray-900 flex items-center justify-center">
                        <i class="fas fa-user text-primary-600 dark:text-primary-400"></i>
                    </div> --}}
                    <span class="hidden w-full truncate text-center font-medium dark:text-white md:block">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 md:absolute md:right-3"></i>
                </button>

                <div id="user-menu"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-black rounded-lg shadow-lg border border-gray-200 dark:border-gray-800 hidden z-50">
                    <ul class="py-1 text-gray-700 dark:text-gray-300">
                        @if (auth()->user()->role == \App\Models\User::ROLES['client'])
                            <li>
                                <a href="{{ route('profile.show', Auth::user()->id) }}"
                                    class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2 dark:hover:bg-gray-900 bg-white dark:bg-black">
                                    {{-- <i class="fas fa-user-alt w-4 h-4 mr-2"></i> --}}
                                    <img src="{{ asset('assets/images/profile.png') }}" class="h-5 w-5">
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile.documents') }}"
                                    class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2 dark:hover:bg-gray-900 bg-white dark:bg-black">
                                    {{-- <i class="fas fa-user-alt w-4 h-4 mr-2"></i> --}}
                                    <img src="{{ asset('assets/images/document.png') }}" class="h-5 w-5">
                                    My Documents
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('profile.userProfileEdit') }}"
                                    class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2 dark:hover:bg-gray-900 bg-white dark:bg-black">
                                    {{-- <i class="fas fa-user-alt w-4 h-4 mr-2"></i> --}}
                                    <img src="{{ asset('assets/images/profile.png') }}" class="h-5 w-5">
                                    Profile
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('profile.change_password') }}"
                                class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2 dark:hover:bg-gray-900 bg-white dark:bg-black">
                                {{-- <i class="fas fa-user-alt w-4 h-4 mr-2"></i> --}}
                                <img src="{{ asset('assets/images/change-password.png') }}" class="h-5 w-5">
                                Change Password
                            </a>
                        </li>
                        @if (auth()->user()->role == \App\Models\User::ROLES['client'])
                        <!-- <li>
                            <a href="{{ route('gst.setting') }}"
                            class="flex items-center px-4 py-2 hover:bg-gray-100 gap-2 dark:hover:bg-gray-900 bg-white dark:bg-black">
                            <img src="{{ asset('assets/images/GST_setting.png') }}" class="h-5 w-5">
                                GST Settings
                            </a>
                        </li> -->
                        @endif
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center px-4 py-2 text-left gap-2 hover:bg-gray-100 dark:hover:bg-gray-900 bg-white dark:bg-black">
                                    {{-- <i class="fas fa-sign-out-alt w-4 h-4 mr-2"></i> --}}
                                    <img src="{{ asset('assets/images/logout.png') }}" class="h-5 w-5">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
