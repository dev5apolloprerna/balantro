@extends('layouts.front')

@section('title', 'Resources')

@section('content')

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
            <div class="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-balantro-primary/20 rounded-full blur-[120px] pointer-events-none animate-pulse"
                style="animation-duration: 4s; z-index: 2"></div>
            <div class="absolute bottom-1/4 right-1/4 translate-x-1/4 translate-y-1/4 w-[600px] h-[600px] bg-balantro-secondary/15 rounded-full blur-[150px] pointer-events-none animate-pulse"
                style="animation-duration: 6s; animation-delay: 2s; z-index: 2"></div>
            <div
                class="absolute bottom-0 left-0 right-0 h-48 bg-gradient-to-t from-[#02040a] to-transparent z-10 pointer-events-none">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 flex flex-col items-center text-center">
            <div
                class="inline-flex items-center gap-2 mb-6 animate-[fadeInUp_0.8s_ease-out_forwards] border border-white/10 bg-white/5 backdrop-blur-xl px-4 py-2 rounded-full shadow-[0_0_30px_rgba(34,211,238,0.15)] cursor-default">
                <span class="text-xs uppercase tracking-[0.2em] text-white font-medium">📚 RESOURCES</span>
            </div>

            <h1
                class="font-display text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-6 leading-[1.05] text-white opacity-0 animate-[fadeInUp_0.8s_ease-out_0.2s_forwards] max-w-5xl mx-auto">
                Insights for Business Owners <br class="hidden sm:block" />
                <span class="relative inline-block mt-2">
                    <span
                        class="absolute -inset-2 bg-gradient-to-r from-balantro-primary via-[#a78bfa] to-balantro-secondary blur-2xl opacity-40"></span>
                    <span
                        class="relative text-transparent bg-clip-text bg-gradient-to-r from-white via-blue-100 to-white drop-shadow-sm">Who
                        Care About Control</span>
                </span>
            </h1>

            <p
                class="text-lg md:text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed font-light opacity-0 animate-[fadeInUp_0.8s_ease-out_0.4s_forwards] mb-12 drop-shadow-md">
                Practical thinking on accounting, compliance, and financial discipline.
            </p>

            <!-- 3 CONTENT PILLARS -->
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-6 opacity-0 animate-[fadeInUp_0.8s_ease-out_0.6s_forwards] w-full max-w-6xl mx-auto">

                <a href="insights.html"
                    class="group relative flex flex-col p-8 rounded-3xl bg-white/[0.03] border border-white/10 backdrop-blur-md hover:bg-white/[0.06] hover:border-balantro-primary/30 transition-all duration-300 text-left overflow-hidden h-full min-h-[250px]">
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="text-[10px] font-bold tracking-widest uppercase text-balantro-primary mb-3">Clear
                            thinking. Practical perspective.</div>
                        <h3 class="text-2xl font-display font-bold text-white mb-2">Insights & Blogs</h3>
                        <p class="text-slate-400 text-sm mb-6 flex-grow">Short reads on accounting, compliance, and
                            business discipline — written for owners, not accountants.</p>
                        <div
                            class="font-medium text-balantro-primary flex items-center group-hover:translate-x-2 transition-transform duration-300">
                            Read Insights <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="guides.html"
                    class="group relative flex flex-col p-8 rounded-3xl bg-white/[0.03] border border-white/10 backdrop-blur-md hover:bg-white/[0.06] hover:border-[#34d399]/30 transition-all duration-300 text-left overflow-hidden h-full min-h-[250px]">
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="text-[10px] font-bold tracking-widest uppercase text-[#34d399] mb-3">Less theory. More
                            clarity.</div>
                        <h3 class="text-2xl font-display font-bold text-white mb-2">Practical Guides</h3>
                        <p class="text-slate-400 text-sm mb-6 flex-grow">Step-by-step guides to help you understand GST,
                            accounting, and compliance in real-world terms.</p>
                        <div
                            class="font-medium text-[#34d399] flex items-center group-hover:translate-x-2 transition-transform duration-300">
                            Explore Guides <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="faqs.html"
                    class="group relative flex flex-col p-8 rounded-3xl bg-white/[0.03] border border-white/10 backdrop-blur-md hover:bg-white/[0.06] hover:border-[#fbbf24]/30 transition-all duration-300 text-left overflow-hidden h-full min-h-[250px]">
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="text-[10px] font-bold tracking-widest uppercase text-[#fbbf24] mb-3">Straight answers.
                        </div>
                        <h3 class="text-2xl font-display font-bold text-white mb-2">FAQs</h3>
                        <p class="text-slate-400 text-sm mb-6 flex-grow">Clear responses to common questions business
                            owners actually ask.</p>
                        <div
                            class="font-medium text-[#fbbf24] flex items-center group-hover:translate-x-2 transition-transform duration-300">
                            View FAQs <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <div class="w-full relative z-10">

        <!-- CONTENT PHILOSOPHY -->
        <section class="inner-section-vh py-24 relative overflow-hidden bg-[#0a0f1c]">
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
        <section class="inner-section-vh py-24 relative bg-[#02040a]">
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
            <source src="images/Texture_1_Mobile.webm" type="video/webm" />
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
    <script src="js/nav-scroll.js"></script>

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



@endsection
