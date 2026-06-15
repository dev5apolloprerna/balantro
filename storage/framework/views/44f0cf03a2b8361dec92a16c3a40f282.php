<?php $__env->startSection('title', 'Company'); ?>

<?php $__env->startSection('content'); ?>

    
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
                <span class="text-xs uppercase tracking-[0.2em] text-white font-medium">✨ COMPANY</span>
            </div>

            <h1
                class="font-display text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-6 leading-[1.05] text-white opacity-0 animate-[fadeInUp_0.8s_ease-out_0.2s_forwards] max-w-5xl mx-auto">
                Built on Thinking. <br class="hidden sm:block" />
                <span class="relative inline-block mt-2">
                    <span
                        class="absolute -inset-2 bg-gradient-to-r from-balantro-primary via-[#a78bfa] to-balantro-secondary blur-2xl opacity-40"></span>
                    <span
                        class="relative text-transparent bg-clip-text bg-gradient-to-r from-white via-blue-100 to-white drop-shadow-sm">Driven
                        by Discipline.</span>
                </span>
            </h1>

            <p
                class="text-lg md:text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed font-light opacity-0 animate-[fadeInUp_0.8s_ease-out_0.4s_forwards] mb-12 drop-shadow-md">
                We don’t sell services.
                <span class="text-white font-medium">We build financial systems that last.</span>
            </p>

            <!-- 3 CLICKABLE PILLARS -->
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-6 opacity-0 animate-[fadeInUp_0.8s_ease-out_0.6s_forwards] w-full max-w-6xl mx-auto">
                <a href="#about-balantro"
                    class="group relative flex flex-col p-8 rounded-3xl bg-white/[0.03] border border-white/10 backdrop-blur-md hover:bg-white/[0.06] hover:border-balantro-primary/30 transition-all duration-300 text-left overflow-hidden h-full min-h-[250px]">
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="text-[10px] font-bold tracking-widest uppercase text-balantro-primary mb-3">
                            Our philosophy, belief, and approach
                        </div>
                        <h3 class="text-2xl font-display font-bold text-white mb-2">
                            About BALANTRO
                        </h3>
                        <p class="text-slate-400 text-sm mb-6 flex-grow">
                            Why we exist, how we think, and what guides every decision we
                            make.
                        </p>
                        <div
                            class="font-medium text-balantro-primary flex items-center group-hover:translate-x-2 transition-transform duration-300">
                            Explore Our Philosophy
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="#our-team"
                    class="group relative flex flex-col p-8 rounded-3xl bg-white/[0.03] border border-white/10 backdrop-blur-md hover:bg-white/[0.06] hover:border-[#34d399]/30 transition-all duration-300 text-left overflow-hidden h-full min-h-[250px]">
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="text-[10px] font-bold tracking-widest uppercase text-[#34d399] mb-3">
                            People behind the process
                        </div>
                        <h3 class="text-2xl font-display font-bold text-white mb-2">
                            Our Team
                        </h3>
                        <p class="text-slate-400 text-sm mb-6 flex-grow">
                            Experienced partners. Trained teams. Clear accountability.
                        </p>
                        <div
                            class="font-medium text-[#34d399] flex items-center group-hover:translate-x-2 transition-transform duration-300">
                            Meet the Team
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="#how-we-work"
                    class="group relative flex flex-col p-8 rounded-3xl bg-white/[0.03] border border-white/10 backdrop-blur-md hover:bg-white/[0.06] hover:border-[#fbbf24]/30 transition-all duration-300 text-left overflow-hidden h-full min-h-[250px]">
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="text-[10px] font-bold tracking-widest uppercase text-[#fbbf24] mb-3">
                            A process-led delivery model
                        </div>
                        <h3 class="text-2xl font-display font-bold text-white mb-2">
                            How We Work
                        </h3>
                        <p class="text-slate-400 text-sm mb-6 flex-grow">
                            Structured execution designed for consistency and control.
                        </p>
                        <div
                            class="font-medium text-[#fbbf24] flex items-center group-hover:translate-x-2 transition-transform duration-300">
                            See How We Work
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <div class="w-full relative z-10">
        <!-- ABOUT BALANTRO -->
        <section id="about-balantro" class="inner-section-vh py-32 relative overflow-hidden bg-[#0a0f1c]">
            <!-- Animated Floating Background Glows -->
            <style>
                @keyframes driftAndFloat {

                    0%,
                    100% {
                        transform: translate(0px, 0px) scale(1);
                    }

                    33% {
                        transform: translate(60px, -40px) scale(1.1);
                    }

                    66% {
                        transform: translate(-40px, 50px) scale(0.9);
                    }
                }

                .glow-drift-1 {
                    animation: driftAndFloat 18s ease-in-out infinite;
                }

                .glow-drift-2 {
                    animation: driftAndFloat 22s ease-in-out infinite reverse;
                }

                .glow-drift-3 {
                    animation: driftAndFloat 26s ease-in-out infinite;
                }
            </style>

            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <div
                    class="absolute top-[10%] left-[5%] w-[400px] h-[400px] bg-balantro-primary/15 rounded-full blur-[120px] glow-drift-1">
                </div>
                <div
                    class="absolute bottom-[10%] right-[5%] w-[500px] h-[500px] bg-balantro-secondary/15 rounded-full blur-[140px] glow-drift-2">
                </div>
                <div
                    class="absolute top-[40%] left-[50%] -translate-x-1/2 w-[450px] h-[450px] bg-emerald-500/10 rounded-full blur-[140px] glow-drift-3">
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center mb-20 max-w-3xl mx-auto" data-aos="fade-up">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-balantro-secondary/30 bg-balantro-secondary/10 text-balantro-secondary text-xs font-semibold tracking-wider uppercase mb-6 shadow-[0_0_15px_rgba(34,211,238,0.2)]">
                        Who We Are
                    </div>
                    <h2 class="text-4xl md:text-6xl font-display font-bold text-white mb-8 leading-tight">
                        About
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-balantro-secondary to-balantro-primary drop-shadow-sm">BALANTRO</span>
                    </h2>
                    <h3 class="text-2xl text-white font-medium mb-6">
                        Strong Businesses Are Built on Strong Financial Systems
                    </h3>
                    <div class="text-slate-400 text-lg leading-relaxed space-y-4">
                        <p>
                            BALANTRO was founded with a clear belief — most businesses don’t
                            suffer from lack of effort,
                            <strong class="text-white font-semibold">they suffer from lack of financial structure.</strong>
                        </p>
                        <p>
                            We exist to bring discipline, clarity, and continuity to the
                            accounting and compliance backbone of Indian businesses.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                    <!-- Our Philosophy (Main Featured Column) -->
                    <div class="lg:col-span-6 flex flex-col" data-aos="fade-right">
                        <div
                            class="bg-gradient-to-br from-[#02040a] to-[#0a0f1c] border border-white/10 rounded-[2rem] p-10 flex-grow shadow-2xl relative overflow-hidden group">
                            <div
                                class="absolute inset-0 bg-balantro-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                            </div>
                            <h4 class="text-balantro-primary font-display font-bold text-2xl mb-8 flex items-center gap-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Our Philosophy
                            </h4>

                            <div class="space-y-6">
                                <div
                                    class="flex items-center p-4 rounded-xl bg-white/5 border border-white/5 group-hover:bg-white/10 transition-colors">
                                    <div
                                        class="w-12 h-12 rounded-full bg-balantro-primary/20 flex items-center justify-center text-balantro-primary shrink-0 mr-4 font-bold">
                                        1
                                    </div>
                                    <div class="text-lg text-white font-medium">
                                        Structure over shortcuts
                                    </div>
                                </div>
                                <div
                                    class="flex items-center p-4 rounded-xl bg-white/5 border border-white/5 group-hover:bg-white/10 transition-colors">
                                    <div
                                        class="w-12 h-12 rounded-full bg-balantro-primary/20 flex items-center justify-center text-balantro-primary shrink-0 mr-4 font-bold">
                                        2
                                    </div>
                                    <div class="text-lg text-white font-medium">
                                        Process over people-dependence
                                    </div>
                                </div>
                                <div
                                    class="flex items-center p-4 rounded-xl bg-white/5 border border-white/5 group-hover:bg-white/10 transition-colors">
                                    <div
                                        class="w-12 h-12 rounded-full bg-balantro-primary/20 flex items-center justify-center text-balantro-primary shrink-0 mr-4 font-bold">
                                        3
                                    </div>
                                    <div class="text-lg text-white font-medium">
                                        Clarity over complexity
                                    </div>
                                </div>
                                <div
                                    class="flex items-center p-4 rounded-xl bg-white/5 border border-white/5 group-hover:bg-white/10 transition-colors">
                                    <div
                                        class="w-12 h-12 rounded-full bg-balantro-primary/20 flex items-center justify-center text-balantro-primary shrink-0 mr-4 font-bold">
                                        4
                                    </div>
                                    <div class="text-lg text-white font-medium">
                                        Responsibility over mere execution
                                    </div>
                                </div>
                            </div>

                            <div class="mt-10 pt-8 border-t border-white/10 relative z-10">
                                <div class="inline-flex items-start gap-3">
                                    <div class="text-balantro-secondary text-2xl leading-none">
                                        "
                                    </div>
                                    <p class="text-slate-300 italic font-medium text-lg leading-relaxed">
                                        We believe accounting is not a year-end activity. It is a
                                        continuous business function.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Secondary Columns (Belief & Approach) -->
                    <div class="lg:col-span-6 flex flex-col gap-8" data-aos="fade-left">
                        <!-- Our Belief -->
                        <div
                            class="bg-white/[0.03] border border-white/5 rounded-[2rem] p-8 backdrop-blur-md flex-grow hover:bg-white/[0.06] hover:border-white/10 transition-all duration-300">
                            <h4 class="text-white font-display font-bold text-xl mb-6 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 rounded-full bg-balantro-secondary/20 flex items-center justify-center text-balantro-secondary text-sm">💡</span>
                                Our Belief
                            </h4>
                            <ul class="space-y-4">
                                <li class="flex items-start gap-4">
                                    <svg class="w-6 h-6 text-balantro-secondary shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-slate-300 text-base">Accounting should support decisions,
                                        <strong class="text-white">not just compliance</strong></span>
                                </li>
                                <li class="flex items-start gap-4">
                                    <svg class="w-6 h-6 text-balantro-secondary shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-slate-300 text-base">Compliance should run quietly in the
                                        background</span>
                                </li>
                                <li class="flex items-start gap-4">
                                    <svg class="w-6 h-6 text-balantro-secondary shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-slate-300 text-base">Systems should scale as businesses scale</span>
                                </li>
                                <li class="flex items-start gap-4">
                                    <svg class="w-6 h-6 text-balantro-secondary shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-slate-300 text-base">Clients deserve visibility, not surprises</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Our Approach -->
                        <div
                            class="bg-white/[0.03] border border-white/5 rounded-[2rem] p-8 backdrop-blur-md flex-grow hover:bg-white/[0.06] hover:border-white/10 transition-all duration-300">
                            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                                <h4 class="text-white font-display font-bold text-xl flex items-center gap-3">
                                    <span
                                        class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-sm">🎯</span>
                                    Our Approach
                                </h4>
                                <div
                                    class="text-[10px] font-bold tracking-widest uppercase text-emerald-400 border border-emerald-400/20 px-2 py-1 rounded-md bg-emerald-400/10">
                                    Methodology
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                <div
                                    class="flex items-center gap-3 text-slate-300 p-3 rounded-lg bg-black/40 border border-white/5">
                                    <span class="text-emerald-400 font-bold shrink-0">➔</span>
                                    <span class="text-sm font-medium">We design the backend</span>
                                </div>
                                <div
                                    class="flex items-center gap-3 text-slate-300 p-3 rounded-lg bg-black/40 border border-white/5">
                                    <span class="text-emerald-400 font-bold shrink-0">➔</span>
                                    <span class="text-sm font-medium">We manage execution</span>
                                </div>
                                <div
                                    class="flex items-center gap-3 text-slate-300 p-3 rounded-lg bg-black/40 border border-white/5">
                                    <span class="text-emerald-400 font-bold shrink-0">➔</span>
                                    <span class="text-sm font-medium">We take responsibility</span>
                                </div>
                                <div
                                    class="flex items-center gap-3 text-slate-300 p-3 rounded-lg bg-black/40 border border-white/5">
                                    <span class="text-emerald-400 font-bold shrink-0">➔</span>
                                    <span class="text-sm font-medium">Relationship-wise focus</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="w-full h-px bg-gradient-to-r from-transparent via-white/10 to-transparent bg-[#02040a]"></div>

        <!-- OUR TEAM -->
        <section id="our-team" class="inner-section-vh py-24 relative overflow-hidden bg-[#02040a]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <h2 class="text-4xl md:text-5xl font-display font-bold text-white mb-6" data-aos="fade-up">
                    Our Team
                </h2>
                <h3 class="text-xl text-slate-300 font-medium mb-12" data-aos="fade-up" data-aos-delay="100">
                    Experience at the Top. Discipline Across the Team.
                </h3>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto mb-16" data-aos="fade-up" data-aos-delay="150">
                    Behind every clean report and timely filing is a team that follows
                    structure and accountability.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left max-w-5xl mx-auto">
                    <div class="bg-white/[0.02] border border-white/5 rounded-3xl p-8 hover:bg-white/[0.04] transition-colors"
                        data-aos="fade-up" data-aos-delay="200">
                        <h4 class="text-2xl font-bold text-white mb-2">Partners</h4>
                        <p class="text-[#34d399] text-sm uppercase tracking-wide font-bold mb-6">
                            Leadership & Accountability
                        </p>
                        <p class="text-slate-400 mb-6">
                            BALANTRO is led by experienced professionals who provide:
                        </p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center gap-2 text-slate-300">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#34d399]"></div>
                                Strategic oversight
                            </li>
                            <li class="flex items-center gap-2 text-slate-300">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#34d399]"></div>
                                Final review and control
                            </li>
                            <li class="flex items-center gap-2 text-slate-300">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#34d399]"></div>
                                Long-term continuity
                            </li>
                        </ul>
                        <div class="p-4 bg-[#34d399]/10 border border-[#34d399]/20 rounded-xl text-center">
                            <p class="text-white font-medium italic">
                                They don’t just advise —<br />they remain accountable.
                            </p>
                        </div>
                    </div>

                    <div class="bg-white/[0.02] border border-white/5 rounded-3xl p-8 hover:bg-white/[0.04] transition-colors"
                        data-aos="fade-up" data-aos-delay="300">
                        <h4 class="text-2xl font-bold text-white mb-2">Core Team</h4>
                        <p class="text-balantro-primary text-sm uppercase tracking-wide font-bold mb-6">
                            Execution with Precision
                        </p>
                        <p class="text-slate-400 mb-6">
                            Our core team consists of trained professionals handling:
                        </p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center gap-2 text-slate-300">
                                <div class="w-1.5 h-1.5 rounded-full bg-balantro-primary"></div>
                                Accounting workflows
                            </li>
                            <li class="flex items-center gap-2 text-slate-300">
                                <div class="w-1.5 h-1.5 rounded-full bg-balantro-primary"></div>
                                Compliance execution
                            </li>
                            <li class="flex items-center gap-2 text-slate-300">
                                <div class="w-1.5 h-1.5 rounded-full bg-balantro-primary"></div>
                                Reviews and reconciliations
                            </li>
                        </ul>
                        <div class="p-4 bg-white/5 border border-white/10 rounded-xl">
                            <p class="text-slate-300 text-sm flex justify-around">
                                <span>✔ Each role is defined.</span>
                                <span>✔ Each task is tracked.</span>
                                <span>✔ Each outcome is reviewed.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-16 inline-block" data-aos="fade-up">
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-[0.2em]">
                        Quiet professionals. Serious work.
                    </p>
                </div>
            </div>
        </section>

        <div class="w-full h-px bg-gradient-to-r from-transparent via-white/10 to-transparent bg-[#0a0f1c]"></div>

        <!-- HOW WE WORK -->
        <section id="how-we-work" class="inner-section-vh py-32 relative overflow-hidden bg-[#0a0f1c]">
            <div class="w-full px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#fbbf24]/10 border border-[#fbbf24]/20 text-[#fbbf24] text-xs font-bold uppercase tracking-widest mb-6"
                    data-aos="fade-up">
                    Our Delivery Model
                </div>
                <h2 class="text-4xl md:text-6xl font-display font-bold text-white mb-6" data-aos="fade-up">
                    How We Work
                </h2>
                <h3 class="text-xl md:text-2xl text-slate-300 font-medium mb-6" data-aos="fade-up">
                    A Systematic, Repeatable Delivery Model
                </h3>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto mb-20" data-aos="fade-up">
                    Consistency does not come from talent alone. It comes from process.
                    <br />
                    BALANTRO operates on a structured delivery model designed to ensure
                    reliability — month after month.
                </p>

                <div class="relative w-full max-w-7xl mx-auto text-left" data-aos="fade-up" data-aos-delay="100">
                    <!-- Connecting Line -->
                    <div
                        class="hidden lg:block absolute top-[27px] left-20 right-20 h-[1px] bg-gradient-to-r from-transparent via-[#fbbf24]/30 to-transparent z-0">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 relative z-10">
                        <!-- Step 1 -->
                        <div
                            class="flex flex-col gap-6 relative group transform hover:-translate-y-2 transition-transform duration-500">
                            <div
                                class="w-14 h-14 rounded-2xl bg-[#0a0f1c] border border-[#fbbf24]/30 shrink-0 flex items-center justify-center text-[#fbbf24] font-display font-bold text-xl transition-all duration-300 group-hover:scale-110 group-hover:shadow-[0_0_20px_rgba(251,191,36,0.3)] mx-auto shadow-lg relative z-10">
                                1
                            </div>
                            <div
                                class="bg-white/[0.03] border border-white/5 rounded-3xl p-8 flex-grow hover:bg-white/[0.06] hover:border-[#fbbf24]/30 transition-all duration-300 text-center h-full hover:shadow-2xl">
                                <h4 class="text-xl font-display font-bold text-white mb-3">
                                    Understand
                                </h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    We study your business model, transaction flow, and
                                    compliance needs.
                                </p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div
                            class="flex flex-col gap-6 relative group transform hover:-translate-y-2 transition-transform duration-500 lg:mt-8">
                            <div
                                class="w-14 h-14 rounded-2xl bg-[#0a0f1c] border border-[#fbbf24]/30 shrink-0 flex items-center justify-center text-[#fbbf24] font-display font-bold text-xl transition-all duration-300 group-hover:scale-110 group-hover:shadow-[0_0_20px_rgba(251,191,36,0.3)] mx-auto shadow-lg relative z-10">
                                2
                            </div>
                            <div
                                class="bg-white/[0.03] border border-white/5 rounded-3xl p-8 flex-grow hover:bg-white/[0.06] hover:border-[#fbbf24]/30 transition-all duration-300 text-center h-full hover:shadow-2xl">
                                <h4 class="text-xl font-display font-bold text-white mb-3">
                                    Design
                                </h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    We set up accounting structures, document flows, and control
                                    points.
                                </p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div
                            class="flex flex-col gap-6 relative group transform hover:-translate-y-2 transition-transform duration-500 lg:mt-16">
                            <div
                                class="w-14 h-14 rounded-2xl bg-[#0a0f1c] border border-[#fbbf24]/30 shrink-0 flex items-center justify-center text-[#fbbf24] font-display font-bold text-xl transition-all duration-300 group-hover:scale-110 group-hover:shadow-[0_0_20px_rgba(251,191,36,0.3)] mx-auto shadow-lg relative z-10">
                                3
                            </div>
                            <div
                                class="bg-white/[0.03] border border-white/5 rounded-3xl p-8 flex-grow hover:bg-white/[0.06] hover:border-[#fbbf24]/30 transition-all duration-300 text-center h-full hover:shadow-2xl">
                                <h4 class="text-xl font-display font-bold text-white mb-3">
                                    Execute
                                </h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    Daily accounting and compliance handled by trained teams.
                                </p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div
                            class="flex flex-col gap-6 relative group transform hover:-translate-y-2 transition-transform duration-500 lg:mt-8">
                            <div
                                class="w-14 h-14 rounded-2xl bg-[#0a0f1c] border border-[#fbbf24]/30 shrink-0 flex items-center justify-center text-[#fbbf24] font-display font-bold text-xl transition-all duration-300 group-hover:scale-110 group-hover:shadow-[0_0_20px_rgba(251,191,36,0.3)] mx-auto shadow-lg relative z-10">
                                4
                            </div>
                            <div
                                class="bg-white/[0.03] border border-white/5 rounded-3xl p-8 flex-grow hover:bg-white/[0.06] hover:border-[#fbbf24]/30 transition-all duration-300 text-center h-full hover:shadow-2xl">
                                <h4 class="text-xl font-display font-bold text-white mb-3">
                                    Review
                                </h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    Multi-level checks with supervisor and partner oversight.
                                </p>
                            </div>
                        </div>

                        <!-- Step 5 -->
                        <div
                            class="flex flex-col gap-6 relative group transform hover:-translate-y-2 transition-transform duration-500">
                            <div
                                class="w-14 h-14 rounded-2xl bg-[#0a0f1c] border border-[#fbbf24]/30 shrink-0 flex items-center justify-center text-[#fbbf24] font-display font-bold text-xl transition-all duration-300 group-hover:scale-110 group-hover:shadow-[0_0_20px_rgba(251,191,36,0.3)] mx-auto shadow-lg relative z-10">
                                5
                            </div>
                            <div
                                class="bg-white/[0.03] border border-white/5 rounded-3xl p-8 flex-grow hover:bg-white/[0.06] hover:border-[#fbbf24]/30 transition-all duration-300 text-center h-full hover:shadow-2xl">
                                <h4 class="text-xl font-display font-bold text-white mb-3">
                                    Report
                                </h4>
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    Clear, simple financial updates delivered regularly.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-24 p-px bg-gradient-to-r from-transparent via-[#fbbf24]/30 to-transparent rounded-3xl max-w-4xl mx-auto"
                        data-aos="fade-up" data-aos-delay="200">
                        <div class="bg-black/50 backdrop-blur-xl rounded-3xl p-8 text-center border border-white/5">
                            <h4 class="text-white font-bold text-xl mb-6 text-[#fbbf24]">
                                What This Means for Clients
                            </h4>
                            <div class="flex flex-wrap justify-center gap-4 text-sm font-medium">
                                <div
                                    class="px-5 py-2.5 rounded-full bg-white/5 border border-white/10 text-slate-300 hover:text-white hover:border-[#fbbf24]/50 transition-colors flex items-center gap-2">
                                    <span class="text-[#fbbf24]">✔</span> No dependency on
                                    individuals
                                </div>
                                <div
                                    class="px-5 py-2.5 rounded-full bg-white/5 border border-white/10 text-slate-300 hover:text-white hover:border-[#fbbf24]/50 transition-colors flex items-center gap-2">
                                    <span class="text-[#fbbf24]">✔</span> No last-minute chaos
                                </div>
                                <div
                                    class="px-5 py-2.5 rounded-full bg-white/5 border border-white/10 text-slate-300 hover:text-white hover:border-[#fbbf24]/50 transition-colors flex items-center gap-2">
                                    <span class="text-[#fbbf24]">✔</span> No surprises
                                </div>
                                <div
                                    class="px-5 py-2.5 rounded-full bg-white/5 border border-white/10 text-slate-300 hover:text-white hover:border-[#fbbf24]/50 transition-colors flex items-center gap-2">
                                    <span class="text-[#fbbf24]">✔</span> Full visibility and
                                    control
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CLOSING STATEMENT & CTA STRIP -->
        <section
            class="inner-section-vh py-32 relative overflow-hidden flex flex-col items-center justify-center border-t border-white/5 bg-[#02040a]">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0c1328] via-[#051421] to-[#02040a] z-0"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(34,211,238,0.1),transparent_60%)] z-0 animate-pulse"
                style="animation-duration: 5s"></div>

            <div class="max-w-4xl mx-auto px-4 text-center relative z-10 w-full mb-16">
                <h2 class="font-display text-4xl md:text-6xl font-bold text-white mb-8 leading-tight drop-shadow-2xl"
                    data-aos="fade-up">
                    Built Like a Consulting Firm. <br />
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-balantro-secondary to-balantro-primary animate-gradient-text">Executed
                        Like an Operations Team.</span>
                </h2>
                <p class="text-lg md:text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed" data-aos="fade-up"
                    data-aos-delay="100">
                    BALANTRO combines structured thinking with disciplined execution to
                    run your financial backend responsibly.
                </p>
            </div>

            <div class="max-w-4xl mx-auto px-4 text-center relative z-10 w-full CTA-STRIP">
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center" data-aos="fade-up"
                    data-aos-delay="200">
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
    <!-- JOIN THE NEW GENERATION SECTION -->
    <section class="inner-section-vh relative py-32 flex-col w-full overflow-hidden">
        <!-- Background Video -->
        <video id="new-gen-video" loop muted playsinline preload="none"
            class="absolute inset-0 w-full h-full object-cover z-0 opacity-80 mix-blend-screen"
            style="pointer-events: none">
            <source src="images/Texture_1_Desktop.webm" type="video/webm" />
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
        document.addEventListener("DOMContentLoaded", () => {
            const btn = document.getElementById("mobile-menu-btn");
            const menu = document.getElementById("mobile-menu");
            const iconBars = document.getElementById("menu-icon-bars");
            const iconClose = document.getElementById("menu-icon-close");

            if (btn && menu) {
                btn.addEventListener("click", () => {
                    menu.classList.toggle("hidden");
                    iconBars.classList.toggle("hidden");
                    iconClose.classList.toggle("hidden");
                });
            }
        });
    </script>
    <script src="js/magic-button.js"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.front', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\frontend copy\company.blade.php ENDPATH**/ ?>