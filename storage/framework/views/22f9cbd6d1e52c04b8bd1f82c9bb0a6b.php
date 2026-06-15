<?php $__env->startSection('title', 'Services'); ?>

<?php $__env->startSection('content'); ?>

    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
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
            <div
                class="absolute bottom-0 left-0 right-0 h-48 bg-gradient-to-t from-[#02040a] to-transparent z-10 pointer-events-none">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 flex flex-col items-center text-center">
            <!-- Floating Badge -->
            <div
                class="inline-flex items-center gap-2 mb-4 animate-[fadeInUp_0.8s_ease-out_forwards] border border-white/10 bg-white/5 backdrop-blur-xl px-4 py-2 rounded-full shadow-[0_0_30px_rgba(34,211,238,0.15)] hover:bg-white/10 hover:border-white/20 transition-colors cursor-default">
                <span class="text-xs uppercase tracking-[0.2em] text-white font-medium">✨ SERVICES</span>
            </div>

            <!-- Main Headline -->
            <h1
                class="font-display text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-3 leading-[1.05] text-white opacity-0 animate-[fadeInUp_0.8s_ease-out_0.2s_forwards] max-w-6xl mx-auto">
                Services Designed Around <br class="hidden sm:block" />
                <span class="relative inline-block mt-2">
                    <span
                        class="absolute -inset-2 bg-gradient-to-r from-balantro-primary via-[#a78bfa] to-balantro-secondary blur-2xl opacity-40"></span>
                    <span
                        class="relative text-transparent bg-clip-text bg-gradient-to-r from-white via-blue-100 to-white drop-shadow-sm">How
                        Businesses Actually Operate</span>
                </span>
            </h1>

            <!-- Subtitle -->
            <p
                class="text-lg md:text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed font-light opacity-0 animate-[fadeInUp_0.8s_ease-out_0.4s_forwards] mb-8 drop-shadow-md">
                Choose the backend support your business needs — we’ll handle the
                rest.
            </p>

            <!-- SERVICE SELECTION CARDS -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-4 opacity-0 animate-[fadeInUp_0.8s_ease-out_0.6s_forwards] w-full max-w-4xl mx-auto">
                <a href="#virtual-accounting"
                    class="group relative flex flex-col p-5 rounded-3xl bg-white/[0.03] border border-white/10 backdrop-blur-md hover:bg-white/[0.06] hover:border-balantro-primary/30 transition-all duration-300 text-left overflow-hidden h-full min-h-[220px]">
                    <div class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat opacity-10 group-hover:opacity-20 transition-opacity duration-500 mix-blend-screen"
                        style="
                background-image: url(&quot;images/feature_accounting_1772037717788.png&quot;);
              ">
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-[#0a0f1c]/80 via-transparent to-balantro-primary/10 z-0">
                    </div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="text-[10px] font-bold tracking-widest uppercase text-balantro-primary mb-2">
                            Primary Service
                        </div>
                        <h3 class="text-2xl font-display font-bold text-white mb-2">
                            Virtual Accounting
                        </h3>
                        <p class="text-slate-400 text-sm mb-3 flex-grow">
                            Your complete accounting & financial backend — managed end to
                            end.
                        </p>
                        <div
                            class="font-medium text-balantro-primary flex items-center group-hover:translate-x-2 transition-transform duration-300">
                            Explore Virtual Accounting
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="#compliance-payroll"
                    class="group relative flex flex-col p-5 rounded-3xl bg-white/[0.03] border border-white/10 backdrop-blur-md hover:bg-white/[0.06] hover:border-[#34d399]/30 transition-all duration-300 text-left overflow-hidden h-full min-h-[220px]">
                    <div class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat opacity-10 group-hover:opacity-20 transition-opacity duration-500 mix-blend-screen"
                        style="
                background-image: url(&quot;images/feature_compliance_1772037926108.png&quot;);
              ">
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-br from-[#0a0f1c]/80 via-transparent to-[#34d399]/10 z-0">
                    </div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="text-[10px] font-bold tracking-widest uppercase text-[#34d399] mb-2">
                            Add-on / Parallel Service
                        </div>
                        <h3 class="text-2xl font-display font-bold text-white mb-2">
                            Compliance & Payroll
                        </h3>
                        <p class="text-slate-400 text-sm mb-3 flex-grow">
                            Statutory compliance and payroll — handled systematically.
                        </p>
                        <div
                            class="font-medium text-[#34d399] flex items-center group-hover:translate-x-2 transition-transform duration-300">
                            Explore Compliance & Payroll
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

    <!-- MAIN SERVICES SECTIONS -->
    <div class="w-full relative z-10">
        <!-- SECTION 1: VIRTUAL ACCOUNTING -->
        <section id="virtual-accounting" class="inner-section-vh py-24 relative overflow-hidden bg-[#0a0f1c]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row gap-16 lg:gap-24 items-start">
                    <div class="w-full lg:w-1/2" data-aos="fade-right">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-balantro-primary/10 border border-balantro-primary/20 text-balantro-primary text-xs font-bold uppercase tracking-widest mb-6">
                            <span class="w-2 h-2 rounded-full bg-balantro-primary animate-pulse"></span>
                            Primary Service
                        </div>
                        <h2 class="text-4xl md:text-5xl font-display font-bold text-white mb-6 leading-tight">
                            Virtual Accounting
                        </h2>
                        <h3 class="text-xl text-slate-300 font-medium mb-6">
                            Your Accounting Backbone. Fully Managed.
                        </h3>
                        <p class="text-slate-400 text-lg leading-relaxed mb-8">
                            Virtual Accounting is BALANTRO’s core offering.
                            <br /><br />
                            We become your outsourced accounting department, responsible for
                            structure, accuracy, and continuity.
                        </p>

                        <div
                            class="bg-[#0a0f1c] border border-white/5 rounded-2xl p-8 mb-8 shadow-xl hover:shadow-[0_0_40px_rgba(14,165,233,0.15)] transition-shadow duration-500">
                            <h4
                                class="text-balantro-primary font-bold uppercase tracking-wider text-sm mb-6 flex items-center gap-2">
                                <span class="text-xl">✅</span> What We Do
                            </h4>
                            <ul class="space-y-4">
                                <li class="flex items-start gap-4 text-slate-300">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-balantro-primary shrink-0"></div>
                                    Day-to-day accounting & bookkeeping
                                </li>
                                <li class="flex items-start gap-4 text-slate-300">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-balantro-primary shrink-0"></div>
                                    Transaction classification & posting
                                </li>
                                <li class="flex items-start gap-4 text-slate-300">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-balantro-primary shrink-0"></div>
                                    Monthly closing & reconciliation
                                </li>
                                <li class="flex items-start gap-4 text-slate-300">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-balantro-primary shrink-0"></div>
                                    Structured chart of accounts
                                </li>
                                <li class="flex items-start gap-4 text-slate-300">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-balantro-primary shrink-0"></div>
                                    Ongoing review & corrections
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="w-full lg:w-1/2 flex flex-col gap-6" data-aos="fade-left">
                        <div
                            class="bg-gradient-to-br from-white/[0.05] to-transparent border border-white/10 rounded-3xl p-8 backdrop-blur-md hover:bg-white/[0.08] hover:border-white/20 transition-all duration-300 relative group overflow-hidden">
                            <!-- Image Background -->
                            <div class="absolute inset-0 z-0">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-[#0a0f1c] via-[#0a0f1c]/90 to-transparent z-10 transition-opacity duration-500 group-hover:opacity-80">
                                </div>
                                <img src="<?php echo e(asset('images/feature_reporting_1772038110744.png')); ?>"
                                    alt="Reporting Features"
                                    class="w-full h-full object-cover opacity-30 group-hover:scale-105 group-hover:opacity-40 transition-all duration-700 blur-[2px] group-hover:blur-0 mix-blend-screen" />
                            </div>
                            <!-- Content Area -->
                            <div class="relative z-10">
                                <h4 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
                                    <span class="text-xl">💡</span> Why It Matters
                                </h4>
                                <p class="text-slate-400 text-sm leading-relaxed mb-6 italic">
                                    Most accounting problems don’t show immediately — they
                                    surface when decisions are taken on incorrect or incomplete
                                    data.
                                </p>
                                <div class="space-y-3">
                                    <div class="text-sm font-semibold text-white mb-2">
                                        Virtual Accounting ensures:
                                    </div>
                                    <div class="flex items-center gap-3 text-slate-300 text-sm">
                                        <svg class="w-5 h-5 text-balantro-secondary" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Consistency month after month
                                    </div>
                                    <div class="flex items-center gap-3 text-slate-300 text-sm">
                                        <svg class="w-5 h-5 text-balantro-secondary" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        No dependency on individuals
                                    </div>
                                    <div class="flex items-center gap-3 text-slate-300 text-sm">
                                        <svg class="w-5 h-5 text-balantro-secondary" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        A reliable financial base for compliance & reporting
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gradient-to-br from-white/[0.05] to-transparent border border-white/10 rounded-3xl p-8 backdrop-blur-md hover:bg-white/[0.08] hover:border-white/20 transition-all duration-300 relative group overflow-hidden">
                            <!-- Image Background -->
                            <div class="absolute inset-0 z-0">
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-[#0a0f1c] via-[#0a0f1c]/90 to-transparent z-10 transition-opacity duration-500 group-hover:opacity-80">
                                </div>
                                <img src="<?php echo e(asset('images/deliverables_goal.png')); ?>" alt="Deliverables & Goals"
                                    class="w-full h-full object-cover opacity-30 group-hover:scale-105 group-hover:opacity-40 transition-all duration-700 blur-[2px] group-hover:blur-0 mix-blend-screen" />
                            </div>
                            <!-- Content Area -->
                            <div class="relative z-10">
                                <h4 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
                                    <span class="text-xl">🎯</span> Outcome for You
                                </h4>
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div class="flex items-center gap-2 text-balantro-secondary text-sm font-medium">
                                        ✔ Clean & structured books
                                    </div>
                                    <div class="flex items-center gap-2 text-balantro-secondary text-sm font-medium">
                                        ✔ Reliable numbers
                                    </div>
                                    <div class="flex items-center gap-2 text-balantro-secondary text-sm font-medium">
                                        ✔ Confidence in decisions
                                    </div>
                                    <div class="flex items-center gap-2 text-balantro-secondary text-sm font-medium">
                                        ✔ Scales with your business
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-balantro-primary/10 border border-balantro-primary/20 rounded-3xl p-8 mt-2 shadow-[inset_0_0_20px_rgba(14,165,233,0.1)]">
                            <h4
                                class="text-balantro-primary font-bold text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                                <span class="text-lg">💰</span> Pricing Logic
                            </h4>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span
                                    class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-300">Business
                                    size</span>
                                <span
                                    class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-300">Monthly
                                    transaction volume</span>
                                <span
                                    class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-300">Complexity
                                    of operations</span>
                            </div>
                            <p class="text-sm font-medium text-white mb-6">
                                We don’t sell packages. We design engagement models.
                            </p>
                            <a href="#"
                                class="inline-flex w-full sm:w-auto items-center justify-center px-6 py-3 rounded-full bg-balantro-primary hover:bg-balantro-primary/90 text-white font-semibold text-sm transition-all shadow-[0_0_15px_rgba(14,165,233,0.4)] hover:shadow-[0_0_25px_rgba(14,165,233,0.6)]">
                                Talk to Our Team for Virtual Accounting
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- DIVIDER -->
        <div class="w-full h-px bg-gradient-to-r from-transparent via-white/10 to-transparent bg-[#02040a]"></div>

        <!-- SECTION 2: COMPLIANCE & PAYROLL -->
        <section id="compliance-payroll" class="inner-section-vh py-24 relative overflow-hidden bg-[#02040a]">
            <div
                class="absolute right-0 top-1/2 w-[600px] h-[600px] bg-[#34d399]/10 rounded-full blur-[120px] pointer-events-none transform -translate-y-1/2">
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row-reverse gap-16 lg:gap-24 items-start">
                    <div class="w-full lg:w-1/2" data-aos="fade-left">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#34d399]/10 border border-[#34d399]/20 text-[#34d399] text-xs font-bold uppercase tracking-widest mb-6">
                            <span class="w-2 h-2 rounded-full bg-[#34d399] animate-pulse"></span>
                            Parallel Service
                        </div>
                        <h2 class="text-4xl md:text-5xl font-display font-bold text-white mb-6 leading-tight">
                            Compliance & Payroll
                        </h2>
                        <h3 class="text-xl text-slate-300 font-medium mb-6">
                            Because Deadlines Should Never Control Your Business.
                        </h3>
                        <p class="text-slate-400 text-lg leading-relaxed mb-8">
                            This service focuses on statutory compliance and payroll
                            operations, managed through defined timelines and checks.
                        </p>

                        <div
                            class="bg-[#0a0f1c] border border-white/5 rounded-2xl p-8 mb-8 shadow-xl hover:shadow-[0_0_40px_rgba(52,211,153,0.15)] transition-shadow duration-500">
                            <h4
                                class="text-[#34d399] font-bold uppercase tracking-wider text-sm mb-6 flex items-center gap-2">
                                <span class="text-xl">✅</span> What We Do
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <h5 class="text-white font-semibold mb-4 text-sm border-b border-white/10 pb-2">
                                        Compliance Management
                                    </h5>
                                    <ul class="space-y-3">
                                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                                            <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] shrink-0"></div>
                                            GST returns & reconciliation
                                        </li>
                                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                                            <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] shrink-0"></div>
                                            Income tax filings
                                        </li>
                                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                                            <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] shrink-0"></div>
                                            Statutory compliance tracking
                                        </li>
                                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                                            <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] shrink-0"></div>
                                            Audit coordination & notices
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <h5 class="text-white font-semibold mb-4 text-sm border-b border-white/10 pb-2">
                                        Payroll Management
                                    </h5>
                                    <ul class="space-y-3">
                                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                                            <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] shrink-0"></div>
                                            Monthly payroll processing
                                        </li>
                                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                                            <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] shrink-0"></div>
                                            Salary structuring support
                                        </li>
                                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                                            <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] shrink-0"></div>
                                            Statutory deductions & filings
                                        </li>
                                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                                            <div class="mt-1.5 w-1.5 h-1.5 rounded-full bg-[#34d399] shrink-0"></div>
                                            Payroll reports
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-1/2 flex flex-col gap-6" data-aos="fade-right">
                        <div
                            class="bg-gradient-to-br from-white/[0.05] to-transparent border border-white/10 rounded-3xl p-8 backdrop-blur-md hover:bg-white/[0.08] hover:border-white/20 transition-all duration-300 relative group overflow-hidden">
                            <!-- Image Background -->
                            <div class="absolute inset-0 z-0">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-[#0a0f1c] via-[#0a0f1c]/90 to-transparent z-10 transition-opacity duration-500 group-hover:opacity-80">
                                </div>
                                <img src="<?php echo e(asset('images/feature_compliance_1772037926108.png')); ?>"
                                    alt="Compliance Features"
                                    class="w-full h-full object-cover opacity-30 group-hover:scale-105 group-hover:opacity-40 transition-all duration-700 blur-[2px] group-hover:blur-0 mix-blend-screen" />
                            </div>
                            <!-- Content Area -->
                            <div class="relative z-10">
                                <h4 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
                                    <span class="text-xl">💡</span> Why It Matters
                                </h4>
                                <p class="text-slate-400 text-sm leading-relaxed mb-6 italic">
                                    Compliance failures don’t just cause penalties — they create
                                    stress, disruption, and reputational risk. Payroll errors
                                    affect trust internally.
                                </p>
                                <div class="space-y-3">
                                    <div class="text-sm font-semibold text-white mb-2">
                                        This service ensures:
                                    </div>
                                    <div class="flex items-center gap-3 text-slate-300 text-sm">
                                        <svg class="w-5 h-5 text-[#34d399]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Predictability
                                    </div>
                                    <div class="flex items-center gap-3 text-slate-300 text-sm">
                                        <svg class="w-5 h-5 text-[#34d399]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Timeliness
                                    </div>
                                    <div class="flex items-center gap-3 text-slate-300 text-sm">
                                        <svg class="w-5 h-5 text-[#34d399]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Accuracy
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gradient-to-br from-white/[0.05] to-transparent border border-white/10 rounded-3xl p-8 backdrop-blur-md hover:bg-white/[0.08] hover:border-white/20 transition-all duration-300 relative group overflow-hidden">
                            <!-- Image Background -->
                            <div class="absolute inset-0 z-0">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-[#0a0f1c] via-[#0a0f1c]/90 to-transparent z-10 transition-opacity duration-500 group-hover:opacity-80">
                                </div>
                                <img src="<?php echo e(asset('images/feature_review_1772038721625.png')); ?>" alt="Compliance Review"
                                    class="w-full h-full object-cover opacity-30 group-hover:scale-105 group-hover:opacity-40 transition-all duration-700 blur-[2px] group-hover:blur-0 mix-blend-screen" />
                            </div>
                            <!-- Content Area -->
                            <div class="relative z-10">
                                <h4 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
                                    <span class="text-xl">🎯</span> Outcome for You
                                </h4>
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div class="flex items-center gap-2 text-[#6ee7b7] text-sm font-medium">
                                        ✔ No missed deadlines
                                    </div>
                                    <div class="flex items-center gap-2 text-[#6ee7b7] text-sm font-medium">
                                        ✔ Stress-free payroll cycles
                                    </div>
                                    <div class="flex items-center gap-2 text-[#6ee7b7] text-sm font-medium">
                                        ✔ Quiet compliance
                                    </div>
                                    <div class="flex items-center gap-2 text-[#6ee7b7] text-sm font-medium">
                                        ✔ Peace of mind
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-[#34d399]/10 border border-[#34d399]/20 rounded-3xl p-8 mt-2 shadow-[inset_0_0_20px_rgba(52,211,153,0.1)]">
                            <h4
                                class="text-[#34d399] font-bold text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                                <span class="text-lg">💰</span> Pricing Logic
                            </h4>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span
                                    class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-300">Number
                                    of compliances</span>
                                <span
                                    class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-300">Employee
                                    count</span>
                                <span
                                    class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-300">Filing
                                    frequency</span>
                            </div>
                            <p class="text-sm font-medium text-white mb-6">
                                Transparent. Structured. Predictable.
                            </p>
                            <a href="#"
                                class="inline-flex w-full sm:w-auto items-center justify-center px-6 py-3 rounded-full bg-[#34d399] hover:bg-[#34d399]/90 text-black font-bold text-sm transition-all shadow-[0_0_15px_rgba(52,211,153,0.4)] hover:shadow-[0_0_25px_rgba(52,211,153,0.6)]">
                                Talk to Our Team for Compliance Pricing
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- OPTIONAL SECTION: HOW CLIENTS USUALLY ENGAGE -->
        <section class="inner-section-vh py-24 relative overflow-hidden bg-[#0a0f1c]">
            <div class="absolute w-full h-px top-0 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

            <!-- Animated Background Blur -->
            <div
                class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-balantro-primary/5 rounded-[100%] blur-[120px] pointer-events-none">
            </div>

            <div
                class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center border border-white/5 rounded-[3rem] p-12 lg:p-16 bg-white/[0.02] backdrop-blur-sm shadow-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#fbbf24]/10 border border-[#fbbf24]/20 text-[#fbbf24] text-xs font-bold uppercase tracking-widest mb-6"
                    data-aos="fade-up">
                    <span class="w-2 h-2 rounded-full bg-[#fbbf24] animate-pulse"></span>
                    Engagement Model
                </div>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-16" data-aos="fade-up"
                    data-aos-delay="100">
                    How Most Clients Work With BALANTRO
                </h2>

                <div class="relative max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    <!-- Process Step Line (Desktop) -->
                    <div
                        class="hidden md:block absolute left-[27px] top-6 bottom-6 w-0.5 bg-gradient-to-b from-[#fbbf24] via-balantro-primary to-[#34d399] opacity-50">
                    </div>

                    <div class="space-y-12">
                        <div class="flex flex-col md:flex-row gap-6 md:gap-8 items-start relative group">
                            <div
                                class="w-14 h-14 rounded-2xl bg-[#0a0f1c] border border-[#fbbf24]/50 shadow-[0_0_20px_rgba(251,191,36,0.3)] shrink-0 flex items-center justify-center text-[#fbbf24] font-display font-bold text-xl z-10 transition-transform group-hover:scale-110 group-hover:bg-[#fbbf24]/20">
                                1
                            </div>
                            <div
                                class="text-left bg-white/[0.03] border border-white/5 rounded-2xl p-6 flex-grow hover:bg-white/[0.06] hover:border-[#fbbf24]/30 transition-all duration-300 shadow-lg relative overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-[#fbbf24]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>
                                <h4 class="text-xl font-bold text-white mb-2 relative z-10">
                                    Start with Virtual Accounting
                                </h4>
                                <p class="text-slate-400 text-sm leading-relaxed relative z-10">
                                    Establish a solid financial backbone, correct past data, and
                                    bring predictability to tracking and structure of your
                                    financial reports.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row gap-6 md:gap-8 items-start relative group">
                            <div
                                class="w-14 h-14 rounded-2xl bg-[#0a0f1c] border border-balantro-primary/50 shadow-[0_0_20px_rgba(14,165,233,0.3)] shrink-0 flex items-center justify-center text-balantro-primary font-display font-bold text-xl z-10 transition-transform group-hover:scale-110 group-hover:bg-balantro-primary/20">
                                2
                            </div>
                            <div
                                class="text-left bg-white/[0.03] border border-white/5 rounded-2xl p-6 flex-grow hover:bg-white/[0.06] hover:border-balantro-primary/30 transition-all duration-300 shadow-lg relative overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-balantro-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>
                                <h4 class="text-xl font-bold text-white mb-2 relative z-10">
                                    Add Compliance & Payroll
                                </h4>
                                <p class="text-slate-400 text-sm leading-relaxed relative z-10">
                                    As the business settles into a rhythm, let our teams handle
                                    statutory filing deadlines seamlessly alongside your
                                    accounting base.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row gap-6 md:gap-8 items-start relative group">
                            <div
                                class="w-14 h-14 rounded-2xl bg-[#0a0f1c] border border-[#34d399]/50 shadow-[0_0_20px_rgba(52,211,153,0.3)] shrink-0 flex items-center justify-center text-[#34d399] font-display font-bold text-xl z-10 transition-transform group-hover:scale-110 group-hover:bg-[#34d399]/20">
                                3
                            </div>
                            <div
                                class="text-left bg-white/[0.03] border border-white/5 rounded-2xl p-6 flex-grow hover:bg-white/[0.06] hover:border-[#34d399]/30 transition-all duration-300 shadow-lg relative overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-[#34d399]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>
                                <h4 class="text-xl font-bold text-white mb-2 relative z-10">
                                    Scale Engagement
                                </h4>
                                <p class="text-slate-400 text-sm leading-relaxed relative z-10">
                                    Grow the team handling your backend directly as your
                                    transaction volume and complexities increase.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-16 text-center" data-aos="fade-up" data-aos-delay="300">
                        <p
                            class="text-lg font-medium text-balantro-secondary bg-balantro-secondary/10 inline-block px-8 py-4 rounded-2xl border border-balantro-secondary/20 shadow-[0_0_30px_rgba(34,211,238,0.15)] flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            One backend. Multiple services. Single responsibility.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FINAL CTA STRIP -->
        <section class="inner-section-vh py-32 mt-0 relative overflow-hidden bg-[#02040a]">
            <!-- Intense Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#0c1328] via-[#051421] to-[#02040a] z-0"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(34,211,238,0.1),transparent_60%)] z-0 animate-pulse"
                style="animation-duration: 5s"></div>
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-30 z-0"></div>

            <div class="max-w-4xl mx-auto px-4 text-center relative z-10 w-full">
                <h2 class="font-display text-4xl md:text-6xl font-bold text-white mb-8 leading-tight drop-shadow-2xl"
                    data-aos="fade-up">
                    Not Sure What You Need? <br />
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-balantro-secondary to-balantro-primary animate-gradient-text">We're
                        Here to Help.</span>
                </h2>
                <p class="text-lg md:text-xl text-slate-300 mb-14 max-w-3xl mx-auto leading-relaxed" data-aos="fade-up"
                    data-aos-delay="100">
                    Talk to our team — we’ll help you choose the right engagement model.
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
    </div>

    <!-- JOIN THE NEW GENERATION SECTION -->
    <section class="inner-section-vh relative py-32 flex-col w-full overflow-hidden">
        <!-- Background Video -->
        <video id="new-gen-video" loop muted playsinline preload="none"
            class="absolute inset-0 w-full h-full object-cover z-0 opacity-80 mix-blend-screen"
            style="pointer-events: none">
            <source src="images/Dotcom_Crypto_Animation_WEB.webm" type="video/webm" />
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
    <script src="<?php echo e(asset('js/magic-button.js')); ?>"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.front', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/frontend/services.blade.php ENDPATH**/ ?>