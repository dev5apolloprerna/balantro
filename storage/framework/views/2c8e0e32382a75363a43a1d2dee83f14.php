<footer
    class="bg-balantro-secondary text-black pt-8 md:pt-12 pb-6 border-t border-black/10 relative overflow-hidden z-10 font-sans min-h-screen flex flex-col justify-between">
    <!-- Footer Background Animations -->
    <!-- Particle Canvas -->
    <canvas id="canvas-footer" class="absolute inset-0 w-full h-full z-0 opacity-40" width="788" height="880"></canvas>
    <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.3] mix-blend-overlay" style="
          background-image: url(&quot;https://grainy-gradients.vercel.app/noise.svg&quot;);
        "></div>

    <!-- Top Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full pt-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-x-8 gap-y-8 lg:gap-y-0 items-start py-4">
            <!-- COLUMN 1: BRAND & POSITIONING -->
            <div class="lg:col-span-4 flex flex-col" data-aos="fade-up" data-aos-delay="100">
                <a href="<?php echo e(route('homeindex')); ?>"
                    class="inline-flex items-center gap-2 mb-4 text-3xl font-bold font-display tracking-tight text-black drop-shadow-md">
                    <span class="text-amber-600">•</span> BALANTRO<span class="text-black">.</span>
                </a>
                <p class="text-black text-sm mb-6 leading-relaxed">
                    Intelligent technology and experienced professionals managing your accounting, compliance & financial
                    reporting so you always know where your business stands.
                </p>
                <p class="text-black text-sm mb-6 leading-relaxed italic">
                    <span class="font-bold">We don't just file returns.</span> <span class="font-serif italic text-black/85">We
                        build financial systems that scale.</span>
                </p>

                <!-- Trust Indicators -->
                <ul class="space-y-3 pl-1">
                    <li class="text-xs text-black flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-black/60"></span>
                        Managed by experienced professionals
                    </li>
                    <li class="text-xs text-black flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-black/60"></span>
                        Technology-enabled workflows
                    </li>
                    <li class="text-xs text-black flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-black/60"></span>
                        Process-driven, not person-dependent
                    </li>
                </ul>
            </div>

            <!-- COLUMN 2: SERVICES -->
            <div class="lg:col-span-3" data-aos="fade-up" data-aos-delay="200">
                <h4 class="font-bold text-black mb-6 uppercase tracking-widest text-[11px] opacity-90 drop-shadow-md">
                    Services
                </h4>
                <ul class="space-y-5">
                    <li>
                        <span class="text-black block text-sm font-bold">Accounting</span>
                        <span class="text-[11px] text-black/75 block mt-1">Complete accounting operations, managed end to
                            end</span>
                    </li>
                    <li>
                        <span class="text-black block text-sm font-bold">Compliance Management</span>
                        <span class="text-[11px] text-black/75 block mt-1">GST, Income Tax, TDS, payroll & statutory
                            compliance</span>
                    </li>
                    <li>
                        <span class="text-black block text-sm font-bold">Financial Reports & Insights</span>
                        <span class="text-[11px] text-black/75 block mt-1">Decision-ready reports, dashboards & business
                            insights</span>
                    </li>
                </ul>
                <div class="mt-8 border-t border-black/10 pt-6">
                    <div class="relative group -mx-3 px-3 py-3 rounded-xl hover:bg-black/5 transition-colors">
                        <p class="text-[11px] text-black font-medium italic leading-relaxed relative z-10">
                            One backend.<br />Multiple services.<br />Single responsibility.
                        </p>
                    </div>
                </div>
            </div>

            <!-- COLUMN 3: COMPANY -->
            <div class="lg:col-span-2" data-aos="fade-up" data-aos-delay="300">
                <h4 class="font-bold text-black mb-6 uppercase tracking-widest text-[11px] opacity-90 drop-shadow-md">
                    Company
                </h4>
                <ul class="space-y-3 text-sm">
                    <li>
                        <a href="<?php echo e(route('company')); ?>" class="text-black hover:translate-x-1 transition-all block py-1 font-medium">About
                            Us</a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('company')); ?>#team"
                            class="text-black hover:translate-x-1 transition-all block py-1 font-medium">Our Team</a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('company')); ?>#how-we-work"
                            class="text-black hover:translate-x-1 transition-all block py-1 font-medium">How We Work</a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('insights')); ?>"
                            class="text-black hover:translate-x-1 transition-all block py-1 font-medium">Insights & Blogs</a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('faqs')); ?>" class="text-black hover:translate-x-1 transition-all block py-1 font-medium">FAQs</a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('services')); ?>"
                            class="text-black hover:translate-x-1 transition-all block py-1 font-medium">Pricing <sup
                                class="text-[9px] font-bold">+</sup></a>
                    </li>
                </ul>
            </div>

            <!-- COLUMN 4: CONTACT & STAY UPDATED -->
            <div class="lg:col-span-3 flex flex-col" data-aos="fade-up" data-aos-delay="400">
                <h4 class="font-bold text-black mb-6 uppercase tracking-widest text-[11px] opacity-90 drop-shadow-md">
                    Contact
                </h4>

                <div class="text-black text-sm leading-relaxed mb-6 space-y-4">
                    <div>
                        <p class="text-[10px] font-bold text-black uppercase tracking-widest mb-1 opacity-70">Email</p>
                        <a href="mailto:hello@balantro.com" class="text-sm font-bold hover:underline">hello@balantro.com</a>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-black uppercase tracking-widest mb-1 opacity-70">Phone</p>
                        <p class="text-sm font-bold">+91 XXXXX XXXXX</p>
                    </div>
                </div>

                <div class="mt-auto relative z-20">
                    <h4 class="font-bold text-black mb-3 uppercase tracking-widest text-[11px] opacity-90 drop-shadow-md">
                        Stay Updated
                    </h4>
                    <p class="text-xs text-black/80 mb-4 leading-relaxed">
                        Practical insights. Regulatory updates. Better financial decisions.
                    </p>
                    <form class="flex flex-col gap-3 group" onsubmit="event.preventDefault()">
                        <div class="flex gap-2">
                            <input type="email" placeholder="you@company.com" required
                                class="flex-grow bg-black/5 border border-black/10 rounded-xl px-4 py-3 text-xs text-black placeholder-black/35 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)]" />
                            <button type="submit"
                                class="bg-black text-white font-bold text-xs px-5 py-3 rounded-xl hover:bg-black/80 transition-all shadow-[0_10px_20px_-10px_rgba(0,0,0,0.2)] hover:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.3)] transform hover:-translate-y-0.5 whitespace-nowrap">
                                Subscribe
                            </button>
                        </div>
                        <p class="text-[10px] text-black/70 mt-1">
                            No spam. Only valuable insights for business owners.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MASSIVE BRAND TEXT (ROBINHOOD STYLE) -->
    <div
        class="w-full flex-grow flex justify-center items-center pointer-events-none select-none relative z-0 overflow-hidden py-4"
        aria-hidden="true">
        <span
            class="text-[17vw] leading-[0.6] font-display font-black text-black/[0.30] tracking-normal uppercase whitespace-nowrap transition-transform duration-1000 hover:scale-[1.02]"
            data-aos="fade-up" data-aos-duration="1200" data-aos-offset="0">
            BALANTRO
        </span>
    </div>

    <!-- Legal Links and Bottom Strip Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full pb-2">
        <div class="w-full relative z-20">
            <!-- Legal Links Row (above final footer line) -->
            <div class="flex gap-6 mb-4 text-[11px] font-bold text-black/90">
                <a href="#" class="hover:underline">Terms of Service <sup class="text-[8px] font-bold">+</sup></a>
                <a href="<?php echo e(route('privacypolicy')); ?>" class="hover:underline">Privacy Policy <sup class="text-[8px] font-bold">+</sup></a>
                <a href="#" class="hover:underline">Data Security <sup class="text-[8px] font-bold">+</sup></a>
            </div>

            <!-- FOOTER BOTTOM STRIP -->
            <div class="pt-6 border-t border-black/10 flex flex-col md:flex-row justify-between items-center gap-4 w-full">
                <div class="order-3 md:order-1 text-center md:text-left">
                    <p class="text-xs text-black/80 font-medium">
                        &copy; 2026 BALANTRO Technologies Pvt. Ltd. All rights reserved.
                    </p>
                </div>
                <div class="order-1 md:order-2 text-center">
                    <p class="text-xs md:text-sm font-serif italic font-semibold text-black/90">
                        Beyond Bookkeeping. Built for Better Business Decisions.
                    </p>
                </div>
                <div class="order-2 md:order-3">
                    <div class="flex gap-3">
                        <!-- LinkedIn -->
                        <a href="#"
                            class="w-9 h-9 rounded-full border border-black/20 flex items-center justify-center text-black hover:bg-black/5 transition-all hover:scale-110"
                            aria-label="LinkedIn">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                            </svg>
                        </a>
                        <!-- Instagram -->
                        <a href="#"
                            class="w-9 h-9 rounded-full border border-black/20 flex items-center justify-center text-black hover:bg-black/5 transition-all hover:scale-110"
                            aria-label="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        <!-- X -->
                        <a href="#"
                            class="w-9 h-9 rounded-full border border-black/20 flex items-center justify-center text-black hover:bg-black/5 transition-all hover:scale-110"
                            aria-label="X (Twitter)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                        <!-- YouTube -->
                        <a href="#"
                            class="w-9 h-9 rounded-full border border-black/20 flex items-center justify-center text-black hover:bg-black/5 transition-all hover:scale-110"
                            aria-label="YouTube">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/includes/footer.blade.php ENDPATH**/ ?>