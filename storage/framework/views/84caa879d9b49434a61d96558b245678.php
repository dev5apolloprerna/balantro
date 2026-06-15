<?php $__env->startSection('title', 'Features'); ?>

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
        <!-- Background Elements -->
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
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-[#a78bfa]/10 rounded-[100%] blur-[120px] pointer-events-none transform -rotate-12"
                style="z-index: 2"></div>

            <!-- Fade out gradient at bottom -->
            <div
                class="absolute bottom-0 left-0 right-0 h-48 bg-gradient-to-t from-[#02040a] to-transparent z-10 pointer-events-none">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 flex flex-col items-center text-center">
            <!-- Floating Badge -->
            <div
                class="inline-flex items-center gap-2 mb-6 animate-[fadeInUp_0.8s_ease-out_forwards] border border-white/10 bg-white/5 backdrop-blur-xl px-4 py-2 rounded-full shadow-[0_0_30px_rgba(34,211,238,0.15)] hover:bg-white/10 hover:border-white/20 transition-colors cursor-default">
                <span class="relative flex h-2.5 w-2.5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-balantro-secondary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-balantro-secondary"></span>
                </span>
                <span class="text-xs uppercase tracking-[0.2em] text-white font-medium pl-1">Platform Features</span>
            </div>

            <!-- Main Headline -->
            <h1
                class="font-display text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-6 leading-[1.05] text-white opacity-0 animate-[fadeInUp_0.8s_ease-out_0.2s_forwards] max-w-5xl mx-auto">
                Discipline Built Into Your <br class="hidden sm:block" />
                <span class="relative inline-block mt-2">
                    <span
                        class="absolute -inset-2 bg-gradient-to-r from-balantro-primary via-[#a78bfa] to-balantro-secondary blur-2xl opacity-40"></span>
                    <span
                        class="relative text-transparent bg-clip-text bg-gradient-to-r from-white via-blue-100 to-white drop-shadow-sm">Financial
                        Backend</span>
                </span>
            </h1>

            <!-- Subtitle -->
            <p
                class="text-lg md:text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed font-light opacity-0 animate-[fadeInUp_0.8s_ease-out_0.4s_forwards] mb-8 drop-shadow-md">
                Structured processes. Clear accountability. Consistent outcomes.
                Everything you need to scale your money management with zero friction.
            </p>

            <!-- Action Area / Stats Grid -->
            <div
                class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 opacity-0 animate-[fadeInUp_0.8s_ease-out_0.6s_forwards] w-full max-w-4xl mx-auto">
                <div
                    class="flex items-center justify-center sm:justify-start gap-4 p-5 rounded-2xl bg-white/[0.03] border border-white/5 backdrop-blur-md hover:bg-white/[0.06] transition-colors group">
                    <div
                        class="w-12 h-12 rounded-xl bg-balantro-primary/10 flex items-center justify-center text-balantro-secondary text-2xl border border-balantro-primary/20 group-hover:scale-110 transition-transform">
                        ⚡
                    </div>
                    <div class="text-left">
                        <div class="text-white font-semibold text-sm tracking-wide">
                            Automated Workflows
                        </div>
                        <div class="text-slate-400 text-xs mt-0.5">Zero manual entry</div>
                    </div>
                </div>
                <div
                    class="flex items-center justify-center sm:justify-start gap-4 p-5 rounded-2xl bg-white/[0.03] border border-white/5 backdrop-blur-md hover:bg-white/[0.06] transition-colors group">
                    <div
                        class="w-12 h-12 rounded-xl bg-[#a78bfa]/10 flex items-center justify-center text-[#c4b5fd] text-2xl border border-[#a78bfa]/20 group-hover:scale-110 transition-transform">
                        🛡️
                    </div>
                    <div class="text-left">
                        <div class="text-white font-semibold text-sm tracking-wide">
                            Bank-Grade Security
                        </div>
                        <div class="text-slate-400 text-xs mt-0.5">
                            End-to-end encrypted
                        </div>
                    </div>
                </div>
                <div
                    class="flex items-center justify-center sm:justify-start gap-4 p-5 rounded-2xl bg-white/[0.03] border border-white/5 backdrop-blur-md hover:bg-white/[0.06] transition-colors group">
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-300 text-2xl border border-emerald-500/20 group-hover:scale-110 transition-transform">
                        📊
                    </div>
                    <div class="text-left">
                        <div class="text-white font-semibold text-sm tracking-wide">
                            Real-time Insights
                        </div>
                        <div class="text-slate-400 text-xs mt-0.5">
                            Instant financial clarity
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN FEATURES GRID -->
    <section class="flex flex-col w-full overflow-hidden relative z-10">
        <!-- FEATURE 1 -->
        <div class="inner-section-vh relative flex flex-col lg:flex-row group w-full bg-[#0a0f1c]" data-aos="fade-up">
            <!-- Image (Absolute on Desktop to fill half viewport) -->
            <div class="lg:absolute lg:top-0 lg:bottom-0 lg:left-0 lg:w-1/2 h-[400px] lg:h-auto z-0 overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-transparent via-[#0a0f1c]/40 to-[#0a0f1c] z-10 opacity-60 lg:opacity-100 hidden lg:block">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#0a0f1c] via-transparent to-transparent z-10 lg:hidden">
                </div>
                <img src="<?php echo e(asset('images/feature_document_flow_1772037567784.png')); ?>" alt="Document Flow"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
            </div>

            <!-- Container for Text to align with standard max-w-7xl -->
            <div class="w-full max-w-7xl mx-auto flex flex-col lg:flex-row relative z-10">
                <div class="hidden lg:block lg:w-1/2"></div>
                <!-- Spacer for image -->
                <div
                    class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 md:p-16 lg:py-24 lg:pl-16 xl:pl-20">
                    <div
                        class="feature-card rounded-3xl p-8 md:p-10 transition-all duration-500 w-full lg:-ml-12 bg-[#02040a]/80 lg:bg-[#0a0f1c]/90 backdrop-blur-xl border border-white/5 shadow-2xl hover:shadow-[0_0_40px_rgba(34,211,238,0.2)] flex flex-col h-full transform lg:group-hover:-translate-x-4">
                        <div
                            class="uppercase tracking-widest text-balantro-secondary text-xs font-bold mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-balantro-secondary animate-pulse"></span>
                            1. DOCUMENT FLOW & MANAGEMENT
                        </div>
                        <h3
                            class="text-2xl md:text-3xl font-display font-bold text-white mb-3 leading-tight group-hover:text-balantro-secondary transition-colors relative z-10">
                            Documents In. Chaos Out.
                        </h3>
                        <p class="text-slate-400 text-base mb-8 relative z-10">
                            Collect, organise, and track documents from one central place.
                        </p>

                        <div class="bg-white/5 rounded-2xl p-6 mb-8 border border-white/5 flex-grow">
                            <ul class="space-y-4">
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-balantro-primary flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">App, Web & WhatsApp uploads</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-balantro-primary flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Auto-sorted by period & category</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-balantro-primary flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Missing document alerts</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-balantro-primary flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Clarification workflow</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-auto pt-2">
                            <div
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-balantro-secondary/10 border border-balantro-secondary/20 text-balantro-secondary text-sm font-semibold w-full sm:w-auto justify-center">
                                <span>📂</span> Nothing missed. Nothing delayed.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FEATURE 2 -->
        <div class="inner-section-vh relative flex flex-col lg:flex-row group w-full bg-[#02040a]" data-aos="fade-up">
            <!-- Image (Absolute on Desktop to fill half viewport) -->
            <div
                class="lg:absolute lg:top-0 lg:bottom-0 lg:right-0 lg:w-1/2 h-[400px] lg:h-auto z-0 overflow-hidden order-1 lg:order-none">
                <div
                    class="absolute inset-0 bg-gradient-to-l from-transparent via-[#02040a]/40 to-[#02040a] z-10 opacity-60 lg:opacity-100 hidden lg:block">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#02040a] via-transparent to-transparent z-10 lg:hidden">
                </div>
                <img src="<?php echo e(asset('images/feature_accounting_1772037717788.png')); ?>" alt="Accounting Workflow"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
            </div>

            <!-- Container for Text to align with standard max-w-7xl -->
            <div class="w-full max-w-7xl mx-auto flex flex-col lg:flex-row relative z-10 order-2 lg:order-none">
                <div
                    class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 md:p-16 lg:py-24 lg:pr-16 xl:pr-20">
                    <div
                        class="feature-card rounded-3xl p-8 md:p-10 transition-all duration-500 w-full lg:-mr-12 bg-[#02040a]/80 lg:bg-[#0a0f1c]/90 backdrop-blur-xl border border-white/5 shadow-2xl hover:shadow-[0_0_40px_rgba(167,139,250,0.2)] flex flex-col h-full transform lg:group-hover:translate-x-4">
                        <div
                            class="uppercase tracking-widest text-[#a78bfa] text-xs font-bold mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#a78bfa] animate-pulse"></span>
                            2. ACCOUNTING WORKFLOW
                        </div>
                        <h3
                            class="text-2xl md:text-3xl font-display font-bold text-white mb-3 leading-tight group-hover:text-[#a78bfa] transition-colors relative z-10">
                            Accounting That Follows a System.
                        </h3>
                        <p class="text-slate-400 text-base mb-8 relative z-10">
                            Every entry passes through a defined accounting flow.
                        </p>

                        <div class="bg-white/5 rounded-2xl p-6 mb-8 border border-white/5 flex-grow">
                            <ul class="space-y-4">
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#a78bfa] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Structured transaction classification</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#a78bfa] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Monthly closing discipline</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#a78bfa] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Built-in reconciliation</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#a78bfa] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Clean data flow into reports</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-auto pt-2">
                            <div
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#a78bfa]/10 border border-[#a78bfa]/20 text-[#c4b5fd] text-sm font-semibold w-full sm:w-auto justify-center">
                                <span>📊</span> Reliable numbers, every month.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:block lg:w-1/2"></div>
                <!-- Spacer for image -->
            </div>
        </div>

        <!-- FEATURE 3 -->
        <div class="inner-section-vh relative flex flex-col lg:flex-row group w-full bg-[#0a0f1c]" data-aos="fade-up">
            <!-- Image (Absolute on Desktop to fill half viewport) -->
            <div class="lg:absolute lg:top-0 lg:bottom-0 lg:left-0 lg:w-1/2 h-[400px] lg:h-auto z-0 overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-transparent via-[#0a0f1c]/40 to-[#0a0f1c] z-10 opacity-60 lg:opacity-100 hidden lg:block">
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-t from-[#0a0f1c] via-transparent to-transparent z-10 lg:hidden">
                </div>
                <img src="<?php echo e(asset('images/feature_compliance_1772037926108.png')); ?>" alt="Compliance Tracking"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
            </div>

            <!-- Container for Text to align with standard max-w-7xl -->
            <div class="w-full max-w-7xl mx-auto flex flex-col lg:flex-row relative z-10">
                <div class="hidden lg:block lg:w-1/2"></div>
                <!-- Spacer for image -->
                <div
                    class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 md:p-16 lg:py-24 lg:pl-16 xl:pl-20">
                    <div
                        class="feature-card rounded-3xl p-8 md:p-10 transition-all duration-500 w-full lg:-ml-12 bg-[#02040a]/80 lg:bg-[#0a0f1c]/90 backdrop-blur-xl border border-white/5 shadow-2xl hover:shadow-[0_0_40px_rgba(52,211,153,0.2)] flex flex-col h-full transform lg:group-hover:-translate-x-4">
                        <div
                            class="uppercase tracking-widest text-[#34d399] text-xs font-bold mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#34d399] animate-pulse"></span>
                            3. COMPLIANCE TRACKING
                        </div>
                        <h3
                            class="text-2xl md:text-3xl font-display font-bold text-white mb-3 leading-tight group-hover:text-[#34d399] transition-colors relative z-10">
                            Compliance Runs in the Background.
                        </h3>
                        <p class="text-slate-400 text-base mb-8 relative z-10">
                            Deadlines tracked. Filings planned. Status always visible.
                        </p>

                        <div class="bg-white/5 rounded-2xl p-6 mb-8 border border-white/5 flex-grow">
                            <ul class="space-y-4">
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">GST & tax timelines mapped</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Filing status tracking</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Acknowledgement storage</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Zero last-minute rush</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-auto pt-2">
                            <div
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#34d399]/10 border border-[#34d399]/20 text-[#6ee7b7] text-sm font-semibold w-full sm:w-auto justify-center">
                                <span>🧾</span> Predictable compliance.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FEATURE 4 -->
        <div class="inner-section-vh relative flex flex-col lg:flex-row group w-full bg-[#02040a]" data-aos="fade-up">
            <!-- Image (Absolute on Desktop to fill half viewport) -->
            <div
                class="lg:absolute lg:top-0 lg:bottom-0 lg:right-0 lg:w-1/2 h-[400px] lg:h-auto z-0 overflow-hidden order-1 lg:order-none">
                <div
                    class="absolute inset-0 bg-gradient-to-l from-transparent via-[#02040a]/40 to-[#02040a] z-10 opacity-60 lg:opacity-100 hidden lg:block">
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-t from-[#02040a] via-transparent to-transparent z-10 lg:hidden">
                </div>
                <img src="<?php echo e(asset('images/feature_review_1772038721625.png')); ?>" alt="Review System"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
            </div>

            <!-- Container for Text to align with standard max-w-7xl -->
            <div class="w-full max-w-7xl mx-auto flex flex-col lg:flex-row relative z-10 order-2 lg:order-none">
                <div
                    class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 md:p-16 lg:py-24 lg:pr-16 xl:pr-20">
                    <div
                        class="feature-card rounded-3xl p-8 md:p-10 transition-all duration-500 w-full lg:-mr-12 bg-[#02040a]/80 lg:bg-[#0a0f1c]/90 backdrop-blur-xl border border-white/5 shadow-2xl hover:shadow-[0_0_40px_rgba(251,191,36,0.2)] flex flex-col h-full transform lg:group-hover:translate-x-4">
                        <div
                            class="uppercase tracking-widest text-[#fbbf24] text-xs font-bold mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#fbbf24] animate-pulse"></span>
                            4. REVIEW & CONTROL SYSTEM
                        </div>
                        <h3
                            class="text-2xl md:text-3xl font-display font-bold text-white mb-3 leading-tight group-hover:text-[#fbbf24] transition-colors relative z-10">
                            Checked. Reviewed. Accountable.
                        </h3>
                        <p class="text-slate-400 text-base mb-8 relative z-10">
                            Multi-level review ensures accuracy before finalisation.
                        </p>

                        <div class="bg-white/5 rounded-2xl p-6 mb-8 border border-white/5 flex-grow">
                            <ul class="space-y-4">
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#fbbf24] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Task-based checkpoints</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#fbbf24] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Supervisor verification</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#fbbf24] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Partner oversight</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#fbbf24] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Clear responsibility mapping</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-auto pt-2">
                            <div
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#fbbf24]/10 border border-[#fbbf24]/20 text-[#fcd34d] text-sm font-semibold w-full sm:w-auto justify-center">
                                <span>✅</span> Errors caught early.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:block lg:w-1/2"></div>
                <!-- Spacer for image -->
            </div>
        </div>

        <!-- FEATURE 5 -->
        <div class="inner-section-vh relative flex flex-col lg:flex-row group w-full bg-[#0a0f1c]" data-aos="fade-up">
            <!-- Image (Absolute on Desktop to fill half viewport) -->
            <div class="lg:absolute lg:top-0 lg:bottom-0 lg:left-0 lg:w-1/2 h-[400px] lg:h-auto z-0 overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-transparent via-[#0a0f1c]/40 to-[#0a0f1c] z-10 opacity-60 lg:opacity-100 hidden lg:block">
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-t from-[#0a0f1c] via-transparent to-transparent z-10 lg:hidden">
                </div>
                <img src="<?php echo e(asset('images/feature_reporting_1772038110744.png')); ?>" alt="Reporting and Insights"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
            </div>

            <!-- Container for Text to align with standard max-w-7xl -->
            <div class="w-full max-w-7xl mx-auto flex flex-col lg:flex-row relative z-10">
                <div class="hidden lg:block lg:w-1/2"></div>
                <!-- Spacer for image -->
                <div
                    class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 md:p-16 lg:py-24 lg:pl-16 xl:pl-20">
                    <div
                        class="feature-card rounded-3xl p-8 md:p-10 transition-all duration-500 w-full lg:-ml-12 bg-[#02040a]/80 lg:bg-[#0a0f1c]/90 backdrop-blur-xl border border-white/5 shadow-2xl hover:shadow-[0_0_40px_rgba(244,114,182,0.2)] flex flex-col h-full transform lg:group-hover:-translate-x-4">
                        <div
                            class="uppercase tracking-widest text-[#f472b6] text-xs font-bold mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#f472b6] animate-pulse"></span>
                            5. REPORTING & INSIGHTS
                        </div>
                        <h3
                            class="text-2xl md:text-3xl font-display font-bold text-white mb-3 leading-tight group-hover:text-[#f472b6] transition-colors relative z-10">
                            Reports You Actually Understand.
                        </h3>
                        <p class="text-slate-400 text-base mb-8 relative z-10">
                            Simple financial summaries built for business owners.
                        </p>

                        <div class="bg-white/5 rounded-2xl p-6 mb-8 border border-white/5 flex-grow">
                            <ul class="space-y-4">
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#f472b6] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Monthly P&L snapshots</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#f472b6] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Expense & cash flow view</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#f472b6] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Compliance status summary</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#f472b6] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Decision-friendly layout</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-auto pt-2">
                            <div
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#f472b6]/10 border border-[#f472b6]/20 text-[#f9a8d4] text-sm font-semibold w-full sm:w-auto justify-center">
                                <span>📈</span> Clarity, not confusion.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="inner-section-vh relative flex flex-col lg:flex-row group w-full bg-[#02040a]" data-aos="fade-up">
            <!-- Image (Absolute on Desktop to fill half viewport) -->
            <div
                class="lg:absolute lg:top-0 lg:bottom-0 lg:right-0 lg:w-1/2 h-[400px] lg:h-auto z-0 overflow-hidden order-1 lg:order-none">
                <div
                    class="absolute inset-0 bg-gradient-to-l from-transparent via-[#02040a]/40 to-[#02040a] z-10 opacity-60 lg:opacity-100 hidden lg:block">
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-t from-[#02040a] via-transparent to-transparent z-10 lg:hidden">
                </div>
                <img src="<?php echo e(asset('images/feature_security_1772038463975.png')); ?>" alt="Secure Access"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
            </div>

            <!-- Container for Text to align with standard max-w-7xl -->
            <div class="w-full max-w-7xl mx-auto flex flex-col lg:flex-row relative z-10 order-2 lg:order-none">
                <div
                    class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 md:p-16 lg:py-24 lg:pr-16 xl:pr-20">
                    <div
                        class="feature-card rounded-3xl p-8 md:p-10 transition-all duration-500 w-full lg:-mr-12 bg-[#02040a]/80 lg:bg-[#0a0f1c]/90 backdrop-blur-xl border border-white/5 shadow-2xl hover:shadow-[0_0_40px_rgba(96,165,250,0.2)] flex flex-col h-full transform lg:group-hover:translate-x-4">
                        <div
                            class="uppercase tracking-widest text-[#60a5fa] text-xs font-bold mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#60a5fa] animate-pulse"></span>
                            6. SECURE ACCESS & CONFIDENTIALITY
                        </div>
                        <h3
                            class="text-2xl md:text-3xl font-display font-bold text-white mb-3 leading-tight group-hover:text-[#60a5fa] transition-colors relative z-10">
                            Your Data. Fully Protected.
                        </h3>
                        <p class="text-slate-400 text-base mb-8 relative z-10">
                            Professional handling of sensitive financial information.
                        </p>

                        <div class="bg-white/5 rounded-2xl p-6 mb-8 border border-white/5 flex-grow">
                            <ul class="space-y-4">
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#60a5fa] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Role-based access</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#60a5fa] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Secure data storage</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#60a5fa] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Controlled sharing</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#60a5fa] flex-shrink-0"></div>
                                    <span class="text-slate-300 text-sm">Confidential by design</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-auto pt-2">
                            <div
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#60a5fa]/10 border border-[#60a5fa]/20 text-[#93c5fd] text-sm font-semibold w-full sm:w-auto justify-center">
                                <span>🔒</span> Peace of mind.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:block lg:w-1/2"></div>
                <!-- Spacer for image -->
            </div>
        </div>
    </section>

    <!-- FEATURE FLOW (Visual Section) -->
    <section class="inner-section-vh mt-0 relative overflow-hidden bg-[#0a0f1c]">
        <!-- Connecting Line Decorations -->
        <div class="absolute inset-0 flex justify-center pointer-events-none opacity-20">
            <div class="w-px h-full bg-gradient-to-b from-transparent via-white to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full text-center">
            <h2 class="font-display text-4xl md:text-5xl font-bold text-white mb-12 sm:mb-16" data-aos="fade-up">
                How It All Comes Together
            </h2>

            <div class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-10 lg:gap-14 mb-16 relative">
                <!-- Background Line for Desktop -->
                <div
                    class="hidden md:block absolute top-[40px] left-[10%] right-[10%] h-[2px] bg-gradient-to-r from-transparent via-white/20 to-transparent z-[-1]">
                </div>

                <!-- Step 1 -->
                <div class="flex flex-col items-center group" data-aos="zoom-in" data-aos-delay="100">
                    <div
                        class="flow-icon w-20 h-20 md:w-24 md:h-24 rounded-2xl border border-white/10 flex items-center justify-center mb-6 text-3xl shadow-xl group-hover:scale-110 group-hover:border-balantro-secondary/40 transition-all duration-300 relative">
                        📄
                        <!-- Connecting Line Mobile -->
                        <div class="block md:hidden absolute -bottom-6 left-1/2 w-0.5 h-4 bg-white/20"></div>
                    </div>
                    <div class="text-white font-medium text-sm md:text-lg tracking-wide">
                        Documents
                    </div>
                </div>

                <!-- Separator Desktop -->
                <div class="hidden md:flex text-slate-500 text-2xl animate-pulse">
                    →
                </div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center group" data-aos="zoom-in" data-aos-delay="200">
                    <div
                        class="flow-icon w-20 h-20 md:w-24 md:h-24 rounded-2xl border border-white/10 flex items-center justify-center mb-6 text-3xl shadow-xl group-hover:scale-110 group-hover:border-[#a78bfa]/40 transition-all duration-300 relative">
                        🧮
                        <!-- Connecting Line Mobile -->
                        <div class="block md:hidden absolute -bottom-6 left-1/2 w-0.5 h-4 bg-white/20"></div>
                    </div>
                    <div class="text-white font-medium text-sm md:text-lg tracking-wide">
                        Accounting
                    </div>
                </div>

                <!-- Separator Desktop -->
                <div class="hidden md:flex text-slate-500 text-2xl animate-pulse">
                    →
                </div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center group" data-aos="zoom-in" data-aos-delay="300">
                    <div
                        class="flow-icon w-20 h-20 md:w-24 md:h-24 rounded-2xl border border-white/10 flex items-center justify-center mb-6 text-3xl shadow-xl group-hover:scale-110 group-hover:border-[#fbbf24]/40 transition-all duration-300 relative">
                        ✅
                        <!-- Connecting Line Mobile -->
                        <div class="block md:hidden absolute -bottom-6 left-1/2 w-0.5 h-4 bg-white/20"></div>
                    </div>
                    <div class="text-white font-medium text-sm md:text-lg tracking-wide">
                        Review
                    </div>
                </div>

                <!-- Separator Desktop -->
                <div class="hidden md:flex text-slate-500 text-2xl animate-pulse">
                    →
                </div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center group" data-aos="zoom-in" data-aos-delay="400">
                    <div
                        class="flow-icon w-20 h-20 md:w-24 md:h-24 rounded-2xl border border-white/10 flex items-center justify-center mb-6 text-3xl shadow-xl group-hover:scale-110 group-hover:border-[#34d399]/40 transition-all duration-300 relative">
                        🧾
                        <!-- Connecting Line Mobile -->
                        <div class="block md:hidden absolute -bottom-6 left-1/2 w-0.5 h-4 bg-white/20"></div>
                    </div>
                    <div class="text-white font-medium text-sm md:text-lg tracking-wide">
                        Compliance
                    </div>
                </div>

                <!-- Separator Desktop -->
                <div class="hidden md:flex text-slate-500 text-2xl animate-pulse">
                    →
                </div>

                <!-- Step 5 -->
                <div class="flex flex-col items-center group" data-aos="zoom-in" data-aos-delay="500">
                    <div
                        class="flow-icon w-20 h-20 md:w-24 md:h-24 rounded-2xl border border-white/10 flex items-center justify-center mb-4 sm:mb-6 text-3xl shadow-xl group-hover:scale-110 group-hover:border-[#f472b6]/40 transition-all duration-300 relative">
                        📊
                    </div>
                    <div class="text-white font-medium text-sm md:text-lg tracking-wide">
                        Reports
                    </div>
                </div>
            </div>

            <div class="inline-flex items-center justify-center px-8 py-4 rounded-full bg-[#0a1122] border border-white/5 shadow-[0_0_30px_rgba(0,0,0,0.5)] backdrop-blur-md max-w-2xl mx-auto mb-4"
                data-aos="fade-up" data-aos-delay="600">
                <p class="text-slate-300 text-lg md:text-xl">
                    Handled by a system.
                    <span class="text-balantro-secondary font-semibold">Delivered by professionals.</span>
                </p>
            </div>
        </div>
    </section>

    <!-- CLOSING STATEMENT & CTA STRIP -->
    <section class="inner-section-vh py-32 mt-0 relative overflow-hidden">
        <!-- Intense Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#0c1328] via-[#051421] to-[#02040a] z-0"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(34,211,238,0.1),transparent_60%)] z-0 animate-pulse"
            style="animation-duration: 5s"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-30 z-0"></div>

        <div class="max-w-4xl mx-auto px-4 text-center relative z-10 w-full">
            <h2 class="font-display text-4xl md:text-6xl font-bold text-white mb-8 leading-tight drop-shadow-2xl"
                data-aos="fade-up">
                Not Just Features. <br />
                <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-balantro-secondary to-balantro-primary animate-gradient-text">Financial
                    Discipline.</span>
            </h2>
            <p class="text-lg md:text-xl text-slate-300 mb-14 max-w-3xl mx-auto leading-relaxed" data-aos="fade-up"
                data-aos-delay="100">
                BALANTRO is built to run your accounting and compliance backend —
                consistently, responsibly, and without chaos.
            </p>

            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center CTA-STRIP" data-aos="fade-up"
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

<?php echo $__env->make('layouts.front', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\frontend copy\features.blade.php ENDPATH**/ ?>