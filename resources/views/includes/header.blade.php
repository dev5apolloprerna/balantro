{{-- <a href="{{ route('index') }}">Home</a>
<a href="{{ route('features') }}">Features</a>
<a href="{{ route('services') }}">Services</a>
<a href="{{ route('company') }}">Company</a>
<a href="{{ route('resources') }}">Resources</a> --}}
<nav id="main-nav"
    class="fixed w-full z-50 transition-all duration-300 backdrop-blur-md bg-balantro-navy/50 border-b border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-24">
            <div class="flex items-center gap-3 cursor-pointer">
                <img src="{{ asset('images/logo-white.png') }}" alt="Balantro" class="h-8 w-auto hidden dark:block"
                    onerror="
      this.style.display = 'none';
      this.nextElementSibling.style.display = 'flex';
    " />
                <img src="{{ asset('images/logo-white.png') }}" alt="Balantro" class="h-8 w-auto block dark:hidden"
                    onerror="this.src = 'images/logo-white.png'" />
                <div
                    class="hidden h-8 w-auto flex items-center font-display font-bold text-2xl tracking-tight text-white">
                    Balantro<span class="text-balantro-secondary">.</span>
                </div>
            </div>

            <div
                class="hidden md:flex items-center space-x-1 bg-white/5 p-1 rounded-full border border-white/5 backdrop-blur-sm">
                <a href="{{ route('homeindex') }}"
                    class="px-6 py-2 text-sm font-medium text-white bg-white/10 transition-all rounded-full">Home</a>
                <a href="{{ route('features') }}"
                    class="px-6 py-2 text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-all rounded-full">Features</a>
                <a href="{{ route('services') }}"
                    class="px-6 py-2 text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-all rounded-full">Services</a>
                <a href="{{ route('company') }}"
                    class="px-6 py-2 text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-all rounded-full">Company</a>
                <a href="{{ route('resources') }}"
                    class="px-6 py-2 text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-all rounded-full">Resources</a>
            </div>

            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('login') }}"
                    class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Log
                    in</a>
                <a href="#"
                    class="px-6 py-2.5 rounded-full bg-gradient-to-r from-balantro-secondary to-balantro-primary text-balantro-navy font-semibold text-sm transition-all hover:shadow-[0_0_20px_rgba(34,211,238,0.4)] hover:brightness-110">
                    Get Started
                </a>
            </div>

            <!-- Mobile Navigation Toggle -->
            <div class="md:hidden flex items-center pr-2">
                <button id="mobile-menu-btn"
                    class="p-2 text-slate-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/20 rounded-md bg-white/5">
                    <svg id="menu-icon-bars" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="menu-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Container -->
    <div id="mobile-menu"
        class="hidden md:hidden absolute top-full left-0 w-full bg-[#02040a]/98 backdrop-blur-2xl border-b border-white/10 shadow-2xl"
        style="height: calc(100vh - 80px)">
        <div class="px-4 pt-6 pb-10 space-y-4">
            <a href="{{ route('homeindex') }}"
                class="block px-4 py-3 rounded-xl text-lg font-medium text-white bg-white/5">Home</a>
            <a href="{{ route('features') }}"
                class="block px-4 py-3 rounded-xl text-lg font-medium text-slate-300">Features</a>
            <a href="{{ route('services') }}"
                class="block px-4 py-3 rounded-xl text-lg font-medium text-slate-300">Services</a>
            <a href="{{ route('company') }}"
                class="block px-4 py-3 rounded-xl text-lg font-medium text-slate-300">Company</a>
            <a href="{{ route('resources') }}"
                class="block px-4 py-3 rounded-xl text-lg font-medium text-slate-300">Resources</a>
            <div class="mt-8 pt-8 border-t border-white/10 flex flex-col gap-4">
                <a href="{{ route('login') }}" class="block text-center text-base font-medium text-slate-300">Log
                    in</a>
                <a href="#"
                    class="block px-6 py-4 rounded-full bg-gradient-to-r from-balantro-secondary to-balantro-primary text-balantro-navy font-bold text-center text-lg transition-all shadow-lg">
                    Get Started
                </a>
            </div>
        </div>
    </div>
</nav>
