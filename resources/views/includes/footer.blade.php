<footer class="bg-balantro-secondary text-black pt-8 pb-6 border-t border-black/10 relative overflow-hidden z-10 font-sans">
    <canvas id="canvas-footer" class="absolute inset-0 w-full h-full z-0 opacity-30"></canvas>
    <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.25] mix-blend-overlay"
        style="background-image: url(&quot;https://grainy-gradients.vercel.app/noise.svg&quot;);"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">


    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-x-8 gap-y-12 mb-6">
            <div class="lg:col-span-4" data-aos="fade-up" data-aos-delay="100">
                <a href="{{ route('homeindex') }}" class="inline-flex items-center gap-2 mb-5 text-3xl font-bold font-display tracking-tight text-black">
                    <span class="text-orange-500 text-2xl leading-none">•</span>
                    BALANTRO<span class="text-black">.</span>
                </a>
                <p class="text-black text-base leading-relaxed max-w-sm mb-5">
                    Intelligent technology and experienced professionals managing your accounting, compliance & financial reporting so you always know where your business stands.
                </p>
                <p class="text-black text-sm leading-relaxed italic max-w-md mb-4">
                    <strong>We don’t just file returns.</strong> We build financial systems that scale.
                </p>

                <ul class="space-y-2 text-sm text-black list-disc pl-5">
                    <li>Managed by experienced professionals</li>
                    <li>Technology-enabled workflows</li>
                    <li>Process-driven, not person-dependent</li>
                </ul>
            </div>

            <div class="lg:col-span-3" data-aos="fade-up" data-aos-delay="200">
                <h4 class="font-bold text-black mb-5 uppercase tracking-widest text-[11px]">Services</h4>
                <ul class="space-y-4">
                    <li>
                        <a href="{{ route('services') }}" class="text-black hover:translate-x-1 transition-all block text-sm font-bold">Accounting</a>
                        <span class="text-[11px] text-black/70 block mt-1 max-w-xs">Complete accounting operations, managed end to end</span>
                    </li>
                    <li>
                         <a href="{{ route('services') }}" class="text-black hover:translate-x-1 transition-all block text-sm font-bold">Compliance Management</a>
                        <span class="text-[11px] text-black/70 block mt-1 max-w-xs">GST, Income Tax, TDS, payroll & statutory compliance</span>
                    </li>
                    <li>
                        <a href="{{ route('services') }}" class="text-black hover:translate-x-1 transition-all block text-sm font-bold">Financial Reports & Insights</a>
                        <span class="text-[11px] text-black/70 block mt-1 max-w-xs">Decision-ready reports, dashboards & business insights</span>
                    </li>
                </ul>
                 <div class="mt-5 border-t border-black/10 pt-5">
                    <p class="text-[12px] text-black font-medium italic leading-relaxed">
                        One backend.<br />Multiple services.<br />Single responsibility.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2" data-aos="fade-up" data-aos-delay="300">
                <h4 class="font-bold text-black mb-5 uppercase tracking-widest text-[11px]">Company</h4>
                <ul class="space-y-4 text-sm">
                    <li><a href="{{ route('company') }}" class="text-black hover:translate-x-1 transition-all block">About Us</a></li>
                    <li><a href="{{ route('company') }}" class="text-black hover:translate-x-1 transition-all block">Our Team</a></li>
                    <li><a href="{{ route('company') }}" class="text-black hover:translate-x-1 transition-all block">How We Work</a></li>
                    <li><a href="{{ route('insights') }}" class="text-black hover:translate-x-1 transition-all block">Insights & Blogs</a></li>
                    <li><a href="{{ route('faqs') }}" class="text-black hover:translate-x-1 transition-all block">FAQs</a></li>
                    <li><a href="#" class="text-black hover:translate-x-1 transition-all block">Pricing +</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3" data-aos="fade-up" data-aos-delay="400">
                <h4 class="font-bold text-black mb-5 uppercase tracking-widest text-[11px]">Contact</h4>
                <div class="space-y-4 text-sm text-black mb-6">
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-black/70 mb-2">Email</p>
                        <a href="mailto:hello@balantro.com" class="font-bold hover:opacity-80">hello@balantro.com</a>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-black/70 mb-2">Phone</p>
                        <a href="tel:+91XXXXXXXXXX" class="font-bold hover:opacity-80">+91 XXXXX XXXXX</a>
                    </div>
                </div>
                <h4 class="font-bold text-black mb-4 uppercase tracking-widest text-[11px]">Stay Updated</h4>
                <p class="text-sm text-black/80 mb-5 leading-relaxed">Practical insights. Regulatory updates. Better financial decisions.</p>
                <form class="flex flex-col sm:flex-row gap-2" onsubmit="event.preventDefault()">
                    <input type="email" placeholder="you@company.com" required class="min-w-0 flex-1 bg-black/5 border border-black/10 rounded-xl px-4 py-3 text-sm text-black placeholder-black/30 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all" />
                    <button type="submit" class="bg-black text-white font-bold text-sm px-5 py-3 rounded-xl hover:bg-black/80 transition-all">Subscribe</button>
                </form>
                <p class="text-[10px] text-black/70 mt-4">No spam. Only valuable insights for business owners.</p>
            </div>
        </div>
        <div class="w-full flex justify-center items-center pointer-events-none select-none relative z-0" aria-hidden="true">
            <span class="text-[17vw] leading-[0.8] font-display font-black text-black/[0.20] tracking-tight uppercase whitespace-nowrap">BALANTRO</span>
        </div>
        <div class="pt-4 border-t border-black/10 flex flex-col md:flex-row justify-between items-center gap-6 relative z-20">
            <div class="flex flex-wrap justify-center md:justify-start gap-x-6 gap-y-2 text-xs font-bold order-1">
                <a href="#" class="hover:opacity-70">Terms of Service +</a>
                <a href="{{ route('privacypolicy') }}" class="hover:opacity-70">Privacy Policy +</a>
                <a href="#" class="hover:opacity-70">Data Security +</a>
            </div>
            <p class="text-xs text-black font-medium order-3 md:order-2">&copy; 2026 BALANTRO Technologies Pvt. Ltd. All rights reserved.</p>
            <div class="flex gap-3 order-2 md:order-3">
                <a href="#" class="w-9 h-9 rounded-full border border-black/20 flex items-center justify-center hover:bg-black hover:text-white transition-all" aria-label="LinkedIn">in</a>
                <a href="#" class="w-9 h-9 rounded-full border border-black/20 flex items-center justify-center hover:bg-black hover:text-white transition-all" aria-label="Instagram">◎</a>
                <a href="#" class="w-9 h-9 rounded-full border border-black/20 flex items-center justify-center hover:bg-black hover:text-white transition-all" aria-label="X">𝕏</a>
                <a href="#" class="w-9 h-9 rounded-full border border-black/20 flex items-center justify-center hover:bg-black hover:text-white transition-all" aria-label="YouTube">▶</a>
            </div>
        </div>
    </div>
</footer>