<?php $__env->startSection('title', 'FAQs'); ?>

<?php $__env->startSection('content'); ?>

    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                        display: ["Outfit", "sans-serif"],
                    },
                    colors: {
                        balantro: {
                            navy: "#02040a",
                            primary: "#0EA5E9",
                            secondary: "#22D3EE",
                            glow: "#1d4ed8",
                        },
                    },
                    animation: {
                        "fade-in-up": "fadeInUp 0.8s ease-out forwards",
                    },
                    keyframes: {
                        fadeInUp: {
                            "0%": {
                                opacity: 0,
                                transform: "translateY(20px)"
                            },
                            "100%": {
                                opacity: 1,
                                transform: "translateY(0)"
                            },
                        },
                    },
                },
            },
        };
    </script>
    <link href="css/style.css" rel="stylesheet" />
    <style>
        .feature-card {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .feature-card:hover {
            background: rgba(15, 23, 42, 0.7);
            border-color: rgba(34, 211, 238, 0.3);
            box-shadow: 0 10px 40px -10px rgba(34, 211, 238, 0.15);
            transform: translateY(-5px);
        }

        .flow-icon {
            background: linear-gradient(135deg,
                    rgba(2, 4, 10, 1) 0%,
                    rgba(15, 23, 42, 1) 100%);
        }
    </style>




    <!-- PAGE HERO SECTION -->
    <section class="inner-hero-vh relative overflow-hidden">
        <div class="absolute inset-0 z-0 bg-[#02040a]">
            <!-- Animated Grid Background -->
            <div class="hero-grid-bg">
                <div class="hero-grid-lines"></div>
                <div class="hero-grid-beam"></div>
                <div class="hero-grid-scanline"></div>
                <div class="hero-grid-corner-tr"></div>
                <div class="hero-grid-corner-bl"></div>
                <div class="hero-grid-mask"></div>
            </div>
            <!-- Animated Glows (kept above grid) -->
            <div class="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#fbbf24]/20 rounded-full blur-[120px] pointer-events-none animate-pulse"
                style="animation-duration: 4s; z-index: 2"></div>
            <div
                class="absolute bottom-0 left-0 right-0 h-48 bg-gradient-to-t from-[#02040a] to-transparent z-10 pointer-events-none">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 flex flex-col items-center text-center">
            <div
                class="inline-flex items-center gap-2 mb-6 animate-[fadeInUp_0.8s_ease-out_forwards] border border-white/10 bg-white/5 backdrop-blur-xl px-4 py-2 rounded-full shadow-[0_0_30px_rgba(251,191,36,0.15)] cursor-default">
                <span class="text-xs uppercase tracking-[0.2em] text-white font-medium">🔵 FAQs</span>
            </div>

            <h1
                class="font-display text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-6 leading-[1.05] text-white opacity-0 animate-[fadeInUp_0.8s_ease-out_0.2s_forwards] max-w-5xl mx-auto">
                Questions Business <br class="hidden sm:block" />
                <span class="relative inline-block mt-2">
                    <span
                        class="absolute -inset-2 bg-gradient-to-r from-[#fbbf24] via-amber-400 to-[#f59e0b] blur-2xl opacity-40"></span>
                    <span
                        class="relative text-transparent bg-clip-text bg-gradient-to-r from-white via-yellow-100 to-white drop-shadow-sm">Owners
                        Ask Us Most</span>
                </span>
            </h1>

            <p
                class="text-lg md:text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed font-light opacity-0 animate-[fadeInUp_0.8s_ease-out_0.4s_forwards] mb-8 drop-shadow-md">
                Straight answers — no jargon, no sales talk. If a question matters to clients, we answer it openly.
            </p>
        </div>
    </section>

    <!-- CONTENT SECTION -->
    <div class="w-full relative z-10">
        <section class="inner-section-vh py-12 relative overflow-hidden">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <div class="space-y-16">

                    <div data-aos="fade-up">
                        <h3 class="text-2xl font-display font-bold text-[#fbbf24] mb-8 border-b border-white/10 pb-4">
                            Getting Started</h3>
                        <div class="space-y-6">

                            <div
                                class="bg-white/[0.02] border border-white/10 rounded-2xl p-6 hover:bg-white/[0.04] transition-colors">
                                <h4 class="text-lg font-bold text-white mb-3">Do I need Virtual Accounting?</h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    If you struggle with maintaining clean books, experience delays in monthly closing, or
                                    feel uncertain about your financial accuracy despite having a team, virtual accounting
                                    provides the structure and oversight you need.
                                </p>
                            </div>

                            <div
                                class="bg-white/[0.02] border border-white/10 rounded-2xl p-6 hover:bg-white/[0.04] transition-colors">
                                <h4 class="text-lg font-bold text-white mb-3">Can I switch from my current CA?</h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    Yes. The transition is designed to be seamless. We handle the handover process, data
                                    migration, and initial auditing to ensure continuity without disrupting your ongoing
                                    business operations.
                                </p>
                            </div>

                        </div>
                    </div>

                    <div data-aos="fade-up" data-aos-delay="100">
                        <h3 class="text-2xl font-display font-bold text-[#fbbf24] mb-8 border-b border-white/10 pb-4">
                            Accounting & Compliance</h3>
                        <div class="space-y-6">

                            <div
                                class="bg-white/[0.02] border border-white/10 rounded-2xl p-6 hover:bg-white/[0.04] transition-colors">
                                <h4 class="text-lg font-bold text-white mb-3">How often are accounts updated?</h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    Accounts are updated continuously as transactions occur. Detailed reconciliation and the
                                    formalized monthly close process happen on a fixed schedule, ensuring you have accurate
                                    data within the first week of every new month.
                                </p>
                            </div>

                            <div
                                class="bg-white/[0.02] border border-white/10 rounded-2xl p-6 hover:bg-white/[0.04] transition-colors">
                                <h4 class="text-lg font-bold text-white mb-3">Who reviews my work?</h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    Our process involves multiple tiers. Automated systems handle data entry and initial
                                    checks, while dedicated accounting professionals and senior domain experts review the
                                    books for compliance, strategy, and accuracy.
                                </p>
                            </div>

                        </div>
                    </div>

                    <div data-aos="fade-up" data-aos-delay="200">
                        <h3 class="text-2xl font-display font-bold text-[#fbbf24] mb-8 border-b border-white/10 pb-4">
                            Pricing & Engagement</h3>
                        <div class="space-y-6">

                            <div
                                class="bg-white/[0.02] border border-white/10 rounded-2xl p-6 hover:bg-white/[0.04] transition-colors">
                                <h4 class="text-lg font-bold text-white mb-3">How is pricing decided?</h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    Pricing is based on the scale and complexity of your operations. We assess factors like
                                    monthly transaction volume, compliance requirements, and business size to provide a
                                    transparent, fixed monthly retainer.
                                </p>
                            </div>

                            <div
                                class="bg-white/[0.02] border border-white/10 rounded-2xl p-6 hover:bg-white/[0.04] transition-colors">
                                <h4 class="text-lg font-bold text-white mb-3">Can services scale with my business?</h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    Absolutely. Our engagement models are elastic. Whether you're upgrading to more complex
                                    compliance reporting or expanding transaction volume, our backend scales up to support
                                    your growing needs without needing to hire a completely new internal team.
                                </p>
                            </div>

                        </div>
                    </div>

                    <div data-aos="fade-up" data-aos-delay="300">
                        <h3 class="text-2xl font-display font-bold text-[#fbbf24] mb-8 border-b border-white/10 pb-4">Data
                            & Security</h3>
                        <div class="space-y-6">

                            <div
                                class="bg-white/[0.02] border border-white/10 rounded-2xl p-6 hover:bg-white/[0.04] transition-colors">
                                <h4 class="text-lg font-bold text-white mb-3">Is my data secure?</h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    Yes. We use enterprise-grade cloud platforms for accounting and document management. All
                                    data is encrypted, strictly access-controlled, and backed up continuously to ensure no
                                    data loss or unauthorized exposure.
                                </p>
                            </div>

                            <div
                                class="bg-white/[0.02] border border-white/10 rounded-2xl p-6 hover:bg-white/[0.04] transition-colors">
                                <h4 class="text-lg font-bold text-white mb-3">Who can access my information?</h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    Access is provisioned on a strict role-based policy. Only the assigned account managers
                                    and authorized reviewers handling your file can access your data. We adhere to rigorous
                                    internal IT and confidentiality policies.
                                </p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- CONTENT PHILOSOPHY -->
        <section class="inner-section-vh py-24 relative overflow-hidden bg-white/[0.02]">
            <div class="absolute w-full h-px top-0 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10" data-aos="fade-up">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-balantro-primary/10 border border-balantro-primary/20 text-balantro-primary text-xs font-bold uppercase tracking-widest mb-6">
                    <span class="w-2 h-2 rounded-full bg-balantro-primary animate-pulse"></span>
                    Content Philosophy
                </div>
                <h2 class="text-3xl md:text-5xl font-display font-bold text-white mb-6">Built for Learning. Trusted for
                    Execution.</h2>
                <p class="text-xl text-slate-400 mb-10 max-w-2xl mx-auto">Our content reflects how we work — clear
                    thinking, disciplined processes, and responsible advice.</p>
            </div>
        </section>

        <!-- FINAL CTA STRIP -->
        <section class="inner-section-vh py-24 relative">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-aos="fade-up">
                <h2 class="text-3xl md:text-5xl font-display font-bold text-white mb-6">Want Clarity on Your Own Business?
                </h2>
                <p class="text-xl text-slate-400 mb-10">Talk to our team — no pressure, just perspective.</p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center CTA-STRIP">
                    <a href="#"
                        class="w-full sm:w-auto px-12 py-5 rounded-full bg-white text-balantro-navy font-bold text-lg transition-all hover:bg-slate-200 hover:scale-105 shadow-[0_0_30px_rgba(255,255,255,0.2)]">
                        Talk to Our Team
                    </a>
                    <a href="#"
                        class="w-full sm:w-auto px-12 py-5 rounded-full text-white font-medium text-lg flex items-center justify-center gap-3 hover:bg-white/10 border border-white/20 transition-all backdrop-blur-sm">
                        📞 Call Now
                    </a>
                </div>
            </div>
        </section>
    </div>
    </div>
    <!-- JOIN THE NEW GENERATION SECTION -->
    <section class="inner-section-vh relative py-32 flex-col w-full overflow-hidden">
        <!-- Background Video -->
        <video id="new-gen-video" loop muted playsinline preload="none"
            class="absolute inset-0 w-full h-full object-cover z-0 opacity-80 mix-blend-screen"
            style="pointer-events: none">
            <source src="images/Dotcom_NewGeneration_Animation_WEB.webm" type="video/webm" />
        </video>

        <!-- Gradient Overlay for smooth start/end blending -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#02040a] via-transparent to-[#02040a] z-0 pointer-events-none">
        </div>

        <!-- Content -->
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto flex flex-col items-center">
            <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-10 tracking-tight drop-shadow-2xl"
                data-aos="fade-up">
                Join the new generation of accounting
            </h2>
            <a href="#" data-aos="fade-up" data-aos-delay="100"
                class="px-10 py-5 rounded-full bg-gradient-to-r from-balantro-secondary to-balantro-primary text-balantro-navy font-bold text-lg transition-all hover:shadow-[0_0_30px_rgba(34,211,238,0.5)] hover:brightness-110 hover:scale-105 inline-block">
                Get Started
            </a>
        </div>
    </section>

    <!-- Script for lazy loading the new generation video -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const video = document.getElementById("new-gen-video");
            if ("IntersectionObserver" in window) {
                const observer = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) {
                                video.play();
                            } else {
                                if (!video.paused) {
                                    video.pause();
                                }
                            }
                        });
                    }, {
                        rootMargin: "0px 0px 200px 0px"
                    },
                );
                observer.observe(video);
            } else {
                video.play();
            }
        });
    </script>

    <!-- GLOBAL FOOTER -->

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        class ParticleNetwork {
            constructor(canvasId, options = {}) {
                this.canvas = document.getElementById(canvasId);
                if (!this.canvas) return;
                this.ctx = this.canvas.getContext("2d");
                this.particles = [];
                this.options = Object.assign({
                        particleColor: "rgba(34, 211, 238, 1)", // Cyan
                        lineColor: "rgba(34, 211, 238, 1)",
                        particleAmount: 60,
                        defaultSpeed: 0.5,
                        variantSpeed: 0.5,
                        defaultRadius: 2,
                        variantRadius: 2,
                        linkRadius: 150,
                    },
                    options,
                );

                this.resizeReset = this.resizeReset.bind(this);
                this.animationLoop = this.animationLoop.bind(this);

                this.resizeReset();
                this.init();
                window.addEventListener("resize", this.resizeReset);
                requestAnimationFrame(this.animationLoop);
            }

            resizeReset() {
                this.w = this.canvas.width = this.canvas.offsetWidth;
                this.h = this.canvas.height = this.canvas.offsetHeight;
            }

            init() {
                for (let i = 0; i < this.options.particleAmount; i++) {
                    this.particles.push(new Particle(this.w, this.h, this.options));
                }
            }

            animationLoop() {
                this.ctx.clearRect(0, 0, this.w, this.h);
                this.drawScene();
                requestAnimationFrame(this.animationLoop);
            }

            drawScene() {
                this.drawLine();
                this.drawParticle();
            }

            drawParticle() {
                for (let i = 0; i < this.particles.length; i++) {
                    this.particles[i].update();
                    this.particles[i].draw(this.ctx);
                }
            }

            drawLine() {
                for (let i = 0; i < this.particles.length; i++) {
                    for (let j = i; j < this.particles.length; j++) {
                        let distance = this.checkDistance(
                            this.particles[i],
                            this.particles[j],
                        );
                        let opacity = 1 - distance / this.options.linkRadius;
                        if (opacity > 0) {
                            this.ctx.lineWidth = 0.5;
                            this.ctx.strokeStyle = this.options.lineColor.replace(
                                "1)",
                                `${opacity})`,
                            );
                            this.ctx.beginPath();
                            this.ctx.moveTo(this.particles[i].x, this.particles[i].y);
                            this.ctx.lineTo(this.particles[j].x, this.particles[j].y);
                            this.ctx.stroke();
                        }
                    }
                }
            }

            checkDistance(p1, p2) {
                return Math.sqrt(Math.pow(p1.x - p2.x, 2) + Math.pow(p1.y - p2.y, 2));
            }
        }

        class Particle {
            constructor(w, h, options) {
                this.w = w;
                this.h = h;
                this.options = options;
                this.x = Math.random() * w;
                this.y = Math.random() * h;
                this.speed =
                    options.defaultSpeed + Math.random() * options.variantSpeed;
                this.directionAngle = Math.floor(Math.random() * 360);
                this.color = options.particleColor;
                this.radius =
                    options.defaultRadius + Math.random() * options.variantRadius;
                this.vector = {
                    x: Math.cos(this.directionAngle) * this.speed,
                    y: Math.sin(this.directionAngle) * this.speed,
                };
            }

            update() {
                this.border();
                this.x += this.vector.x;
                this.y += this.vector.y;
            }

            border() {
                if (this.x >= this.w || this.x <= 0) {
                    this.vector.x *= -1;
                }
                if (this.y >= this.h || this.y <= 0) {
                    this.vector.y *= -1;
                }
                if (this.x > this.w) this.x = this.w;
                if (this.x < 0) this.x = 0;
                if (this.y > this.h) this.y = this.h;
                if (this.y < 0) this.y = 0;
            }

            draw(ctx) {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.closePath();
                ctx.fillStyle = this.color;
                ctx.fill();
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            AOS.init({
                duration: 800,
                once: true,
                offset: 100,
                easing: "ease-out-cubic",
            });
            new ParticleNetwork("canvas-footer", {
                particleColor: "rgba(14, 165, 233, 0.4)",
                lineColor: "rgba(14, 165, 233, 0.6)",
                particleAmount: 60,
                linkRadius: 150,
            });
        });
    </script>
    <script src="mouse_glow.js"></script>

    <script>
        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const iconBars = document.getElementById('menu-icon-bars');
            const iconClose = document.getElementById('menu-icon-close');

            if (btn && menu) {
                btn.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                    iconBars.classList.toggle('hidden');
                    iconClose.classList.toggle('hidden');
                });
            }
        });
    </script>
    <script src="js/magic-button.js"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.front', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\frontend\faqs.blade.php ENDPATH**/ ?>