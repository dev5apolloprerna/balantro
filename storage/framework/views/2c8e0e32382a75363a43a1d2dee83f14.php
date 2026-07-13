<footer
    class="bg-balantro-secondary text-black pt-24 pb-8 border-t border-black/10 relative overflow-hidden z-10 font-sans">
    <!-- Footer Background Animations -->
    <!-- Particle Canvas -->
    <canvas id="canvas-footer" class="absolute inset-0 w-full h-full z-0 opacity-40"></canvas>
    <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.3] mix-blend-overlay"
        style="
        background-image: url(&quot;https://grainy-gradients.vercel.app/noise.svg&quot;);
      ">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-x-8 gap-y-16 mb-20">
            <!-- COLUMN 1: BRAND, POSITIONING & SOCIAL -->
            <div class="lg:col-span-3 flex flex-col" data-aos="fade-up" data-aos-delay="100">
                <a href="<?php echo e(route('homeindex')); ?>"
                    class="inline-flex items-center gap-2 mb-4 text-3xl font-bold font-display tracking-tight text-black drop-shadow-md">
                    BALANTRO<span class="text-black drop-shadow-[0_0_10px_rgba(34,211,238,0.5)]">.</span>
                </a>
                <p class="text-black font-bold text-sm mb-4 leading-relaxed">
                    Accounting & Compliance Backend for Indian Businesses
                </p>
                <p class="text-black text-sm mb-6 leading-relaxed">
                    BALANTRO helps businesses build financial discipline through
                    structured accounting, planned compliance, and responsible
                    execution.<br /><br />
                    <span class="text-black font-bold">We don’t just file returns.<br />We run the backend.</span>
                </p>

                <!-- Trust Indicators -->
                <div class="space-y-2 mb-8 border-l-2 border-black/10 pl-4 py-1">
                    <div class="text-xs text-black flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-black shadow-sm" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Managed by experienced professionals
                    </div>
                    <div class="text-xs text-black flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-black shadow-sm" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        MSME-focused delivery model
                    </div>
                    <div class="text-xs text-black flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-black shadow-sm" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Process-driven, not person-dependent
                    </div>
                </div>

                <!-- Contact -->
                <div class="text-black text-sm leading-relaxed mb-8 space-y-2">
                    <p class="flex items-center gap-3 hover:text-black transition-colors cursor-pointer">
                        <span class="text-lg">📧</span> support@balantro.in
                    </p>
                    <p class="flex items-center gap-3 hover:text-black transition-colors cursor-pointer">
                        <span class="text-lg">📞</span> +91 XXXXX XXXXX
                    </p>
                    <p class="flex items-center gap-3">
                        <span class="text-lg">📍</span> India (Serving businesses across
                        India)
                    </p>
                </div>

                <div>
                    <h4 class="text-[10px] font-bold text-black uppercase tracking-widest mb-4">
                        Follow Us
                    </h4>
                    <div class="flex gap-4">
                        <a href="#" class="text-black hover:scale-110 transition-all duration-300"
                            aria-label="LinkedIn">
                            <svg class="w-5 h-5 drop-shadow-md" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                            </svg>
                        </a>
                        <a href="#" class="text-black hover:scale-110 transition-all duration-300"
                            aria-label="X (Twitter)">
                            <svg class="w-5 h-5 drop-shadow-md" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                        <a href="#" class="text-black hover:scale-110 transition-all duration-300"
                            aria-label="YouTube">
                            <svg class="w-5 h-5 drop-shadow-md" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                        </a>
                        <a href="#" class="text-black hover:scale-110 transition-all duration-300"
                            aria-label="Instagram">
                            <svg class="w-5 h-5 drop-shadow-md" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- COLUMN 2: SERVICES -->
            <div class="lg:col-span-2" data-aos="fade-up" data-aos-delay="200">
                <h4 class="font-bold text-black mb-6 uppercase tracking-widest text-[11px] opacity-90 drop-shadow-md">
                    Services
                </h4>
                <ul class="space-y-5">
                    <li>
                        <a href="#"
                            class="text-black hover:translate-x-1 transition-all block text-sm font-medium">Virtual
                            Accounting</a>
                        <span class="text-[11px] text-black/50 block mt-1">End-to-end accounting & bookkeeping</span>
                    </li>
                    <li>
                        <a href="#"
                            class="text-black hover:translate-x-1 transition-all block text-sm font-medium">Compliance
                            Management</a>
                        <span class="text-[11px] text-black/50 block mt-1">GST, Income Tax & statutory filings</span>
                    </li>
                    <li>
                        <a href="#"
                            class="text-black hover:translate-x-1 transition-all block text-sm font-medium">Payroll
                            Services</a>
                        <span class="text-[11px] text-black/50 block mt-1">Payroll processing & statutory
                            compliance</span>
                    </li>
                    <li>
                        <a href="#"
                            class="text-black hover:translate-x-1 transition-all block text-sm font-medium">Audit
                            Support</a>
                        <span class="text-[11px] text-black block mt-1">Coordination, preparation & assistance</span>
                    </li>
                </ul>
                <div class="mt-8 border-t border-black/10 pt-6">
                    <div class="relative group -mx-3 px-3 py-3 rounded-xl hover:bg-black/5 transition-colors">
                        <p class="text-[11px] text-black font-medium italic leading-relaxed relative z-10">
                            One backend.<br />Multiple services.<br />Single
                            responsibility.
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2" data-aos="fade-up" data-aos-delay="300">
                <h4 class="font-bold text-black mb-6 uppercase tracking-widest text-[11px] opacity-90 drop-shadow-md">
                    Platform & Process
                </h4>
                <ul class="space-y-3 text-sm mb-8">
                    <li>
                        <a href="#" class="text-black hover:translate-x-1 transition-all block py-1">Features</a>
                    </li>
                    <li>
                        <a href="#" class="text-black hover:translate-x-1 transition-all block py-1">How We
                            Work</a>
                    </li>
                    <li>
                        <a href="#" class="text-black hover:translate-x-1 transition-all block py-1">Review &
                            Control System</a>
                    </li>
                    <li>
                        <a href="#" class="text-black hover:translate-x-1 transition-all block py-1">Reporting &
                            Insights</a>
                    </li>
                    <li>
                        <a href="#" class="text-black hover:translate-x-1 transition-all block py-1">Security &
                            Confidentiality</a>
                    </li>
                </ul>
                <div
                    class="p-4 rounded-xl bg-black/5 border border-black/10 backdrop-blur-sm shadow-[0_4px_30px_rgba(0,0,0,0.05)] hover:bg-black/10 transition-colors">
                    <p class="text-[11px] text-black leading-relaxed font-medium">
                        Technology-supported.<br />Expert-controlled.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2" data-aos="fade-up" data-aos-delay="400">
                <h4 class="font-bold text-black mb-6 uppercase tracking-widest text-[11px] opacity-90 drop-shadow-md">
                    Company
                </h4>
                <ul class="space-y-3 text-sm mb-8">
                    <li>
                        <a href="#" class="text-black hover:translate-x-1 transition-all block py-1">About
                            BALANTRO</a>
                    </li>
                    <li>
                        <a href="#" class="text-black hover:translate-x-1 transition-all block py-1">Our
                            Philosophy</a>
                    </li>
                    <li>
                        <a href="#" class="text-black hover:translate-x-1 transition-all block py-1">Our
                            Team</a>
                    </li>
                    <li>
                        <a href="#" class="text-black hover:translate-x-1 transition-all block py-1">Careers</a>
                    </li>
                    <li>
                        <a href="#" class="text-black hover:translate-x-1 transition-all block py-1">Contact
                            Us</a>
                    </li>
                </ul>
                <div class="mb-10 text-[11px] text-black font-medium leading-relaxed border-l-2 border-black/20 pl-3">
                    Built like a consulting firm.<br />Executed like an operations
                    team.
                </div>

                <h4 class="font-bold text-black mb-6 uppercase tracking-widest text-[11px] opacity-90 drop-shadow-md">
                    Legal & Policies
                </h4>
                <ul class="space-y-3 text-xs text-black">
                    <li>
                        <a href="#" class="hover:text-black hover:translate-x-1 transition-all block">Terms of
                            Service</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-black hover:translate-x-1 transition-all block">Privacy
                            Policy</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-black hover:translate-x-1 transition-all block">Data
                            Security Policy</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-black hover:translate-x-1 transition-all block">IT &
                            Confidentiality Policy</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-black hover:translate-x-1 transition-all block">Regulatory
                            Disclosures</a>
                    </li>
                </ul>
            </div>

            <div class="lg:col-span-3 flex flex-col" data-aos="fade-up" data-aos-delay="500">
                <h4 class="font-bold text-black mb-6 uppercase tracking-widest text-[11px] opacity-90 drop-shadow-md">
                    Resources
                </h4>
                <ul class="space-y-3 text-sm mb-6">
                    <li>
                        <a href="<?php echo e(route('insights')); ?>"
                            class="text-black hover:translate-x-1 transition-all block py-1">Insights & Blogs</a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('guides')); ?>"
                            class="text-black hover:translate-x-1 transition-all block py-1">Practical Guides</a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('faqs')); ?>"
                            class="text-black hover:translate-x-1 transition-all block py-1">FAQs</a>
                    </li>
                    <li>
                        <a href="#" class="text-black hover:translate-x-1 transition-all block py-1">Compliance
                            Updates</a>
                    </li>
                    <li>
                        <a href="#"
                            class="text-black hover:translate-x-1 transition-all block py-1">Glossary</a>
                    </li>
                </ul>
                <div
                    class="mb-10 px-4 py-3 rounded-xl bg-black/5 border border-black/10 inline-block shadow-[inset_0_1px_1px_rgba(0,0,0,0.05)] text-left">
                    <p class="text-[11px] text-black font-bold tracking-wide">
                        Education-first. Sales later.
                    </p>
                </div>

                <div class="mt-auto relative z-20">
                    <h4
                        class="font-bold text-black mb-4 uppercase tracking-widest text-[11px] opacity-90 drop-shadow-md">
                        Stay Updated
                    </h4>
                    <p class="text-sm text-black mb-4 leading-relaxed font-medium">
                        Practical insights on accounting, compliance, and business
                        discipline.
                    </p>
                    <form class="flex flex-col gap-3 group" onsubmit="event.preventDefault()">
                        <div class="relative">
                            <input type="email" placeholder="Email Address" required
                                class="w-full bg-black/5 border border-black/10 rounded-xl px-4 py-3.5 text-sm text-black placeholder-black/30 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)]" />
                        </div>
                        <button type="submit"
                            class="w-full bg-black text-white font-bold text-sm px-4 py-3.5 rounded-xl hover:bg-black/80 transition-all shadow-[0_10px_20px_-10px_rgba(0,0,0,0.2)] hover:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.3)] transform hover:-translate-y-0.5">
                            Subscribe
                        </button>
                        <p
                            class="text-[10px] text-black mt-2 flex items-center justify-center gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                            No spam. Only clarity.
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <!-- MASSIVE BRAND TEXT (ROBINHOOD STYLE) -->
        <div class="w-full flex justify-center items-center mt-8 md:mt-16 pointer-events-none select-none relative z-0 px-2 sm:px-4 pb-2 md:pb-4"
            aria-hidden="true">
            <span
                class="text-[16vw] leading-none font-display font-black text-black/[0.04] tracking-tight uppercase whitespace-nowrap transition-transform duration-1000 hover:scale-[1.02]"
                data-aos="fade-up" data-aos-duration="1200" data-aos-offset="0">
                BALANTRO
            </span>
        </div>
        <!-- FOOTER BOTTOM STRIP -->
        <div
            class="pt-8 border-t border-black/10 flex flex-col md:flex-row justify-between items-center gap-6 relative z-20">
            <div class="order-2 md:order-1 text-center md:text-left">
                <p class="text-xs text-black font-medium">
                    &copy; 2026 BALANTRO. All rights reserved.
                </p>
            </div>
            <div class="order-1 md:order-2">
                <p
                    class="text-[11px] md:text-xs text-black font-bold tracking-widest uppercase flex flex-col sm:flex-row gap-2 sm:gap-4 items-center">
                    <span class="drop-shadow-sm">Financial clarity.</span>
                    <span class="hidden sm:block text-black">|</span>
                    <span class="drop-shadow-sm">Compliance discipline.</span>
                    <span class="hidden sm:block text-black">|</span>
                    <span class="drop-shadow-sm">Business control.</span>
                </p>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/includes/footer.blade.php ENDPATH**/ ?>