@extends('layouts.front')

@section('title', 'Home')

@section('content')

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
                        "float-slow": "float 8s ease-in-out infinite",
                    },
                    keyframes: {
                        float: {
                            "0%, 100%": {
                                transform: "translateY(0)"
                            },
                            "50%": {
                                transform: "translateY(-15px)"
                            },
                        },
                    },
                },
            },
        };
    </script>
    <!-- Main Stack Wrapper for Scroll Transitions -->
    <div class="main-stack-wrapper">
        <section class="scroll-section flex-col relative">
            <!-- Parallax Background -->
            <div class="absolute inset-0 z-0 hero-parallax">
                <div class="absolute inset-0 video-overlay"></div>
            </div>

            <!-- Animated Grid Background -->
            <div class="hero-grid-bg">
                <!-- Scrolling grid lines -->
                <div class="hero-grid-lines"></div>
                <!-- Sweeping light beam -->
                <div class="hero-grid-beam"></div>
                <!-- Horizontal scanline -->
                <div class="hero-grid-scanline"></div>
                <!-- Corner glow accents -->
                <div class="hero-grid-corner-tr"></div>
                <div class="hero-grid-corner-bl"></div>
                <!-- Radial fade mask (must be last) -->
                <div class="hero-grid-mask"></div>
            </div>

            <!-- Ambient Orbs -->
            <div class="gradient-orb-1 opacity-40" style="z-index: 2"></div>
            <div class="gradient-orb-2 opacity-40" style="z-index: 2"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full animate-on-entry">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                    <!-- LEFT COLUMN: CONTENT -->
                    <div class="text-left order-2 md:order-1 lg:col-span-8">
                        <!-- Badge -->
                        <div class="inline-block mb-6 animate-fade-in-up">
                            <span data-aos="zoom-in"
                                class="py-1.5 px-4 rounded-full border border-balantro-secondary/30 bg-balantro-secondary/10 text-balantro-secondary text-xs font-semibold tracking-wider uppercase shadow-lg backdrop-blur animate-pulse">
                                Balantro 2.0
                            </span>
                        </div>

                        <h1
                            class="font-display text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-6 leading-[1.1] animate-fade-in-up delay-100">
                            Your Business Finances, <br />
                            <span class="title-gradient animate-gradient-text">Managed with Precision</span>
                            Not Promises
                        </h1>

                        <p
                            class="text-lg md:text-xl text-slate-300 mb-8 max-w-2xl leading-relaxed animate-fade-in-up delay-200">
                            <!-- Structured financial clarity delivered consistently. Not just software, but a complete system
                                                                        running your books. -->
                            Balantro is not just software. It's a complete financial
                            operating system — human experts, structured processes, and
                            intelligent technology — working together so your books are
                            always clean and your filings are always on time.
                        </p>

                        <div class="flex flex-row gap-4 justify-start items-center animate-fade-in-up delay-300">
                            <a href="#"
                                class="px-8 py-4 rounded-full bg-white text-balantro-navy font-bold text-base transition-all hover:bg-slate-200 hover:scale-105 shadow-[0_0_20px_rgba(255,255,255,0.2)]">
                                Talk to Team
                            </a>
                            <a href="#"
                                class="px-8 py-4 rounded-full pill-button text-white font-medium text-base flex items-center justify-center gap-2 group hover:gap-3 transition-all">
                                <svg class="w-5 h-5 text-balantro-secondary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Watch Demo
                            </a>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: VISUAL -->
                    <div
                        class="relative order-1 md:order-2 lg:col-span-4 flex justify-center md:justify-end perspective-container">
                        <!-- Circular Aura Animation (Centered on mobile/Right on Desktop) -->
                        <div
                            class="absolute top-1/2 left-1/2 md:left-[60%] -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] md:w-[600px] md:h-[600px] border border-white/5 rounded-full animate-[spin_20s_linear_infinite]">
                        </div>
                        <div
                            class="absolute top-1/2 left-1/2 md:left-[60%] -translate-x-1/2 -translate-y-1/2 w-[350px] h-[350px] md:w-[500px] md:h-[500px] border border-balantro-secondary/10 rounded-full animate-[spin_15s_linear_infinite_reverse]">
                        </div>
                        <div
                            class="absolute top-1/2 left-1/2 md:left-[60%] -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-balantro-primary/10 rounded-full blur-3xl animate-pulse">
                        </div>

                        <!-- Wrapper for Owl and Mobile -->
                        <div
                            class="relative w-[280px] h-[580px] md:w-[300px] md:h-[620px] flex items-center justify-center mx-auto md:mr-0 md:ml-auto">
                            <!-- MECHA OWL ELEMENT (SVG) -->
                            <div
                                class="hidden absolute inset-0 flex items-center justify-center z-20 animate-owl-sequence pointer-events-none">
                                <div class="owl-transform-wrapper drop-shadow-[0_0_30px_rgba(14,165,233,0.6)]">
                                    <svg width="200" height="200" viewBox="0 0 200 200" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <!-- Back Wings (Flapping) -->
                                        <g class="animate-svg-wing-left" transform-origin="100 100">
                                            <path d="M40 80 C20 60 10 90 20 120 C30 140 60 130 80 110"
                                                fill="url(#wingGradient)" stroke="#38bdf8" stroke-width="1"
                                                opacity="0.8" />
                                        </g>
                                        <g class="animate-svg-wing-right" transform-origin="100 100">
                                            <path d="M160 80 C180 60 190 90 180 120 C170 140 140 130 120 110"
                                                fill="url(#wingGradient)" stroke="#38bdf8" stroke-width="1"
                                                opacity="0.8" />
                                        </g>

                                        <!-- Body Shape -->
                                        <defs>
                                            <linearGradient id="bodyGradient" x1="100" y1="40" x2="100"
                                                y2="160" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#0f172a" />
                                                <stop offset="1" stop-color="#1e293b" />
                                            </linearGradient>
                                            <linearGradient id="wingGradient" x1="0" y1="0" x2="1"
                                                y2="1">
                                                <stop stop-color="#0284c7" stop-opacity="0.8" />
                                                <stop offset="1" stop-color="#0ea5e9" stop-opacity="0.4" />
                                            </linearGradient>
                                            <radialGradient id="eyeGlow" cx="0" cy="0" r="1"
                                                gradientUnits="userSpaceOnUse"
                                                gradientTransform="translate(90 90) rotate(90) scale(10)">
                                                <stop stop-color="#22d3ee" />
                                                <stop offset="1" stop-color="#22d3ee" stop-opacity="0" />
                                            </radialGradient>
                                        </defs>

                                        <!-- Main Body -->
                                        <path
                                            d="M100 160 C130 160 145 130 140 90 C135 60 120 40 100 40 C80 40 65 60 60 90 C55 130 70 160 100 160Z"
                                            fill="url(#bodyGradient)" stroke="#38bdf8" stroke-width="2" />

                                        <!-- Chest Plate Details -->
                                        <path d="M80 120 L100 140 L120 120" stroke="#38bdf8" stroke-width="1"
                                            fill="none" class="animate-pulse" />
                                        <path d="M85 110 L100 125 L115 110" stroke="#38bdf8" stroke-width="1"
                                            opacity="0.5" fill="none" />

                                        <!-- Head/Face -->
                                        <path d="M70 60 L60 40 L85 55" fill="#0f172a" stroke="#38bdf8"
                                            stroke-width="1" />
                                        <!-- Left Ear -->
                                        <path d="M130 60 L140 40 L115 55" fill="#0f172a" stroke="#38bdf8"
                                            stroke-width="1" />
                                        <!-- Right Ear -->

                                        <!-- Eyes -->
                                        <circle cx="85" cy="85" r="12" fill="#02040a" stroke="#38bdf8"
                                            stroke-width="2" />
                                        <circle cx="115" cy="85" r="12" fill="#02040a" stroke="#38bdf8"
                                            stroke-width="2" />

                                        <!-- Glowing Pupils -->
                                        <circle cx="85" cy="85" r="5" fill="#22d3ee"
                                            class="animate-cyber-blink" />
                                        <circle cx="115" cy="85" r="5" fill="#22d3ee"
                                            class="animate-cyber-blink" />

                                        <!-- Beak -->
                                        <path d="M100 100 L95 90 L105 90 Z" fill="#fbbf24" />
                                    </svg>
                                </div>
                            </div>

                            <!-- 3D Mobile Phone Container -->
                            <div
                                class="animate-mobile-sequence w-[260px] md:w-[300px] h-[540px] md:h-[620px] relative z-10">
                                <div class="mobile-3d-wrap">
                                    <div
                                        class="relative w-full h-full bg-black rounded-[3rem] border-[8px] border-slate-800 shadow-2xl overflow-hidden transform-style-3d">
                                        <!-- Screen Content -->
                                        <div
                                            class="relative h-full w-full bg-[#02040a] overflow-y-auto no-scrollbar pt-12 px-5 pb-6 font-sans animate-app-screen">
                                            <!-- App Header -->
                                            <div class="flex justify-between items-center mb-8">
                                                <h2 class="text-2xl font-semibold text-white tracking-tight">
                                                    Financials
                                                </h2>
                                                <div
                                                    class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-md">
                                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                    </svg>
                                                </div>
                                            </div>

                                            <!-- Section 1: Compliance Status (Modeled after Contracts) -->
                                            <div class="mb-8">
                                                <div class="flex justify-between items-center mb-4">
                                                    <h3 class="text-lg font-medium text-white">
                                                        Compliance
                                                    </h3>
                                                    <span
                                                        class="text-xs text-slate-500 cursor-pointer hover:text-balantro-secondary transition-colors">View
                                                        All</span>
                                                </div>

                                                <!-- Field Labels -->
                                                <div
                                                    class="grid grid-cols-[1fr,auto,auto,auto] gap-2 text-[10px] text-slate-500 uppercase tracking-wider mb-3 px-1">
                                                    <div class="text-left font-semibold">Type</div>
                                                    <div class="text-center w-[45px]">Score</div>
                                                    <div class="text-center w-[45px]">Var</div>
                                                    <div class="text-center w-[45px]">Total</div>
                                                </div>

                                                <!-- Row 1 -->
                                                <div
                                                    class="flex items-center justify-between mb-3 group cursor-pointer animate-mobile-row">
                                                    <div class="text-sm font-semibold text-white w-[60px] truncate">
                                                        GST Returns
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <div
                                                            class="h-8 w-[45px] rounded-full bg-white text-balantro-navy flex items-center justify-center text-[11px] font-bold shadow-lg shadow-white/10 hover:scale-105 transition-transform">
                                                            98%
                                                        </div>
                                                        <div
                                                            class="h-8 w-[45px] rounded-full bg-[#1C1C1E] border border-white/10 text-emerald-400 flex items-center justify-center text-[11px] font-medium hover:scale-105 transition-transform">
                                                            +2.4
                                                        </div>
                                                        <div
                                                            class="h-8 w-[45px] rounded-full bg-white text-balantro-navy flex items-center justify-center text-[11px] font-bold shadow-lg shadow-white/10 hover:scale-105 transition-transform">
                                                            4.5L
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Row 2 -->
                                                <div
                                                    class="flex items-center justify-between mb-3 group cursor-pointer animate-mobile-row">
                                                    <div class="text-sm font-semibold text-white w-[60px] truncate">
                                                        TDS Filed
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <div
                                                            class="h-8 w-[45px] rounded-full bg-[#1C1C1E] border border-white/10 text-slate-300 flex items-center justify-center text-[11px] font-medium hover:scale-105 transition-transform">
                                                            85%
                                                        </div>
                                                        <div
                                                            class="h-8 w-[45px] rounded-full bg-[#1C1C1E] border border-white/10 text-red-400 flex items-center justify-center text-[11px] font-medium hover:scale-105 transition-transform">
                                                            -1.2
                                                        </div>
                                                        <div
                                                            class="h-8 w-[45px] rounded-full bg-[#1C1C1E] border border-white/10 text-slate-300 flex items-center justify-center text-[11px] font-medium hover:scale-105 transition-transform">
                                                            1.2L
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section 2: Analytics (Modeled after Players) -->
                                            <div>
                                                <h3 class="text-lg font-medium text-white mb-4">
                                                    Analytics
                                                </h3>

                                                <!-- Tabs -->
                                                <div class="flex gap-2 overflow-x-auto no-scrollbar mb-6 pb-2 mask-linear">
                                                    <button
                                                        class="px-4 py-2 rounded-full bg-white text-balantro-navy text-xs font-bold whitespace-nowrap shadow-lg shadow-white/10 hover:scale-105 transition-transform">
                                                        Profit
                                                    </button>
                                                    <button
                                                        class="px-4 py-2 rounded-full bg-[#1C1C1E] border border-white/10 text-slate-400 text-xs font-medium whitespace-nowrap hover:bg-white/10 hover:text-white transition-all">
                                                        Revenue
                                                    </button>
                                                    <button
                                                        class="px-4 py-2 rounded-full bg-[#1C1C1E] border border-white/10 text-slate-400 text-xs font-medium whitespace-nowrap hover:bg-white/10 hover:text-white transition-all">
                                                        Burn
                                                    </button>
                                                    <button
                                                        class="px-4 py-2 rounded-full bg-[#1C1C1E] border border-white/10 text-slate-400 text-xs font-medium whitespace-nowrap hover:bg-white/10 hover:text-white transition-all">
                                                        Tax
                                                    </button>
                                                </div>

                                                <!-- Row 1 -->
                                                <div class="flex items-center justify-between mb-4 animate-mobile-row">
                                                    <div class="text-sm font-medium text-slate-400">
                                                        Q1 Growth
                                                    </div>
                                                    <div class="flex gap-3">
                                                        <div
                                                            class="group flex flex-col items-center justify-center w-[70px] h-[45px] rounded-2xl bg-slate-200 text-balantro-navy shadow-lg shadow-white/5 cursor-pointer hover:bg-white transition-colors">
                                                            <span class="text-xs font-bold">High</span>
                                                            <span class="text-[9px] opacity-60">Prob.</span>
                                                        </div>
                                                        <div
                                                            class="group flex flex-col items-center justify-center w-[70px] h-[45px] rounded-2xl bg-[#1C1C1E] border border-white/10 text-slate-400 cursor-pointer hover:bg-white/10 hover:border-white/20 transition-all">
                                                            <span
                                                                class="text-xs font-bold text-white group-hover:text-balantro-secondary transition-colors">12%</span>
                                                            <span class="text-[9px] opacity-40">Est.</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Row 2 -->
                                                <div class="flex items-center justify-between mb-4 animate-mobile-row">
                                                    <div class="text-sm font-medium text-slate-400">
                                                        Net Margin
                                                    </div>
                                                    <div class="flex gap-3">
                                                        <div
                                                            class="group flex flex-col items-center justify-center w-[70px] h-[45px] rounded-2xl bg-[#1C1C1E] border border-white/10 text-slate-400 cursor-pointer hover:bg-white/10 hover:border-white/20 transition-all">
                                                            <span
                                                                class="text-xs font-bold text-white group-hover:text-balantro-secondary transition-colors">Avg</span>
                                                            <span class="text-[9px] opacity-40">24%</span>
                                                        </div>
                                                        <div
                                                            class="group flex flex-col items-center justify-center w-[70px] h-[45px] rounded-2xl bg-[#1C1C1E] border border-white/10 text-slate-400 cursor-pointer hover:bg-white/10 hover:border-white/20 transition-all">
                                                            <span
                                                                class="text-xs font-bold text-white group-hover:text-balantro-secondary transition-colors">15%</span>
                                                            <span class="text-[9px] opacity-40">Target</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Row 3 -->
                                                <div class="flex items-center justify-between mb-4 animate-mobile-row">
                                                    <div class="text-sm font-medium text-slate-400">
                                                        Liability
                                                    </div>
                                                    <div class="flex gap-3">
                                                        <div
                                                            class="group flex flex-col items-center justify-center w-[70px] h-[45px] rounded-2xl bg-[#1C1C1E] border border-white/10 text-slate-400 cursor-pointer hover:bg-white/10 hover:border-white/20 transition-all">
                                                            <span
                                                                class="text-xs font-bold text-white group-hover:text-balantro-secondary transition-colors">Low</span>
                                                            <span class="text-[9px] opacity-40">Risk</span>
                                                        </div>
                                                        <div
                                                            class="group flex flex-col items-center justify-center w-[70px] h-[45px] rounded-2xl bg-[#1C1C1E] border border-white/10 text-slate-400 cursor-pointer hover:bg-white/10 hover:border-white/20 transition-all">
                                                            <span
                                                                class="text-xs font-bold text-white group-hover:text-balantro-secondary transition-colors">0%</span>
                                                            <span class="text-[9px] opacity-40">Misses</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Bottom Blur Gradient for scroll feel -->
                                            <div
                                                class="absolute bottom-0 left-0 w-full h-16 bg-gradient-to-t from-[#02040a] to-transparent pointer-events-none">
                                            </div>
                                        </div>
                                        <!-- Dynamic Pill -->
                                        <div
                                            class="absolute top-0 left-1/2 transform -translate-x-1/2 h-6 w-32 bg-black rounded-b-xl z-20">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce text-slate-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3">
                    </path>
                </svg>
            </div>
        </section>

        <!-- SECTION 2.5: INTELLIGENCE REVEAL HEADING -->
        <section id="section-intelligence-reveal" class="scroll-section flex-col relative bg-[#02040a]">
            <!-- Dynamic Background Elements -->
            <div class="absolute inset-0 z-0">
                <div
                    class="absolute top-1/4 left-1/4 w-96 h-96 bg-balantro-primary/10 blur-[140px] rounded-full animate-float-slow">
                </div>
                <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-balantro-secondary/10 blur-[160px] rounded-full animate-float-slow"
                    style="animation-delay: -3s"></div>
            </div>

            <div class="max-w-6xl mx-auto px-4 relative z-10 text-center">
                <div class="animate-on-entry">
                    <h2 class="font-display text-5xl md:text-7xl lg:text-8xl font-bold tracking-tighter leading-[1.1]">
                        <span class="animate-from-bottom block text-white pb-2">See how Balantro turns</span>
                        <span class="animate-from-bottom block text-slate-600 pb-2">messy business data</span>
                        <span class="animate-from-bottom block title-gradient animate-gradient-text pb-4">into clear
                            financial intelligence.</span>
                    </h2>
                    <div class="mt-12 flex justify-center">
                        <div class="w-px h-24 bg-gradient-to-b from-balantro-secondary to-transparent animate-bounce">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 2.6: THE INTELLIGENCE SYSTEM (PREMIUM BOXES) -->
        <section id="section-balantro-process" class="scroll-section flex-col relative bg-[#0a0f1c]">
            <div
                class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full animate-on-entry">
                <div class="text-center mb-16 animate-from-top">
                    <h3 class="text-balantro-secondary font-display text-xl md:text-2xl font-semibold mb-4">
                        The Balantro Process
                    </h3>
                    <div class="h-1 w-20 bg-balantro-secondary mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Premium Box 1 -->
                    <div class="group relative animate-from-bottom will-change-transform">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-balantro-secondary to-balantro-primary rounded-3xl blur opacity-25 group-hover:opacity-75 transition-opacity duration-500">
                        </div>
                        <div
                            class="relative p-8 bg-[#02040a] rounded-3xl border border-white/5 flex flex-col h-full hover:border-balantro-secondary/50 transition-all duration-300">
                            <div
                                class="w-14 h-14 rounded-2xl bg-balantro-secondary/10 border border-balantro-secondary/20 flex items-center justify-center text-balantro-secondary mb-6 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-bold">01</span>
                            </div>
                            <h4 class="text-2xl font-bold text-white mb-4">
                                Secure Upload
                            </h4>
                            <p class="text-slate-400 leading-relaxed font-medium">
                                Instantly sync bank statements and trade documents via
                                WhatsApp, email or our secure portal. Zero friction, total
                                security.
                            </p>
                            <div
                                class="mt-auto pt-6 flex items-center text-balantro-secondary font-semibold text-sm group-hover:gap-2 transition-all">
                                Learn more
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Premium Box 2 -->
                    <div class="group relative animate-from-bottom will-change-transform">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-balantro-primary to-purple-500 rounded-3xl blur opacity-25 group-hover:opacity-75 transition-opacity duration-500">
                        </div>
                        <div
                            class="relative p-8 bg-[#02040a] rounded-3xl border border-white/5 flex flex-col h-full hover:border-balantro-primary/50 transition-all duration-300">
                            <div
                                class="w-14 h-14 rounded-2xl bg-balantro-primary/10 border border-balantro-primary/20 flex items-center justify-center text-balantro-primary mb-6 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-bold">02</span>
                            </div>
                            <h4 class="text-2xl font-bold text-white mb-4">
                                AI Categorization
                            </h4>
                            <p class="text-slate-400 leading-relaxed font-medium">
                                Our proprietary AI engine automatically sorts and categorizes
                                every transaction. Verified by human experts for 100%
                                accuracy.
                            </p>
                            <div
                                class="mt-auto pt-6 flex items-center text-balantro-primary font-semibold text-sm group-hover:gap-2 transition-all">
                                How it works
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Premium Box 3 -->
                    <div class="group relative animate-from-bottom will-change-transform">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-purple-500 to-emerald-500 rounded-3xl blur opacity-25 group-hover:opacity-75 transition-opacity duration-500">
                        </div>
                        <div
                            class="relative p-8 bg-[#02040a] rounded-3xl border border-white/5 flex flex-col h-full hover:border-purple-500/50 transition-all duration-300">
                            <div
                                class="w-14 h-14 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 mb-6 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-bold">03</span>
                            </div>
                            <h4 class="text-2xl font-bold text-white mb-4">
                                Live Dashboard
                            </h4>
                            <p class="text-slate-400 leading-relaxed font-medium">
                                Get the "CEO's cockpit" view. Real-time updates on your sales,
                                margins, and cash flow—available on any device, anywhere.
                            </p>
                            <div
                                class="mt-auto pt-6 flex items-center text-purple-400 font-semibold text-sm group-hover:gap-2 transition-all">
                                See dashboard
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Premium Box 4 -->
                    <div class="group relative animate-from-bottom will-change-transform">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500 to-balantro-secondary rounded-3xl blur opacity-25 group-hover:opacity-75 transition-opacity duration-500">
                        </div>
                        <div
                            class="relative p-8 bg-[#02040a] rounded-3xl border border-white/5 flex flex-col h-full hover:border-emerald-500/50 transition-all duration-300">
                            <div
                                class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-6 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-bold">04</span>
                            </div>
                            <h4 class="text-2xl font-bold text-white mb-4">
                                Strategic Reports
                            </h4>
                            <p class="text-slate-400 leading-relaxed font-medium">
                                Deep-dive monthly MIS reports that reveal hidden insights and
                                potential risks. We don't just report numbers, we explain
                                them.
                            </p>
                            <div
                                class="mt-auto pt-6 flex items-center text-emerald-400 font-semibold text-sm group-hover:gap-2 transition-all">
                                Sample report
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 2: PRODUCT VIDEO SHOWCASE -->
        <section class="scroll-section flex-col relative bg-[#02040a] border-y border-white/5">
            <!-- Background Glow -->
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[80vw] h-[500px] bg-balantro-primary/20 blur-[120px] rounded-full pointer-events-none">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center animate-on-entry">
                <div class="mb-10 animate-from-top">
                    <span
                        class="inline-block py-1.5 px-4 rounded-full border border-balantro-secondary/30 bg-balantro-secondary/10 text-balantro-secondary text-xs font-semibold tracking-wider uppercase mb-6">
                        Product Experience
                    </span>
                    <h2
                        class="text-4xl md:text-5xl lg:text-6xl font-display font-bold text-white mb-6 tracking-tight leading-[1.1]">
                        The Financial Engine <br />
                        <span
                            class="inline-block py-1 text-transparent bg-clip-text bg-gradient-to-r from-balantro-secondary to-white">In
                            Action.</span>
                    </h2>
                </div>

                <!-- Video Container -->
                <div class="relative max-w-4xl mx-auto group animate-from-bottom">
                    <!-- Border Glow -->
                    <div class="absolute -inset-[1px] bg-gradient-to-b from-white/20 to-transparent rounded-2xl blur-sm">
                    </div>

                    <div
                        class="relative rounded-2xl overflow-hidden shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)] bg-black border border-white/10 aspect-video max-h-[60vh] mx-auto">
                        <!-- Video Placeholder / Poster -->
                        <img src="{{ asset('images/video_poster.png') }}" alt="Balantro Dashboard Video"
                            class="w-full h-full object-cover opacity-80 group-hover:opacity-60 transition-opacity duration-500" />

                        <!-- Play Button Overlay -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div
                                class="w-20 h-20 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center cursor-pointer group-hover:scale-110 transition-transform duration-300 animate-pulse-ring">
                                <div
                                    class="w-16 h-16 rounded-full bg-white text-balantro-navy flex items-center justify-center pl-1 shadow-[0_0_30px_rgba(255,255,255,0.3)]">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Fake UI Overlay (Timelines/Controls) -->
                        <div
                            class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/90 to-transparent flex items-center gap-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="text-xs font-medium text-white">00:00 / 01:30</div>
                            <div class="h-1 flex-1 bg-white/20 rounded-full overflow-hidden">
                                <div class="h-full w-1/3 bg-balantro-secondary"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="section-why-choose" class="scroll-section flex-row items-stretch relative bg-[#0a0f1c]">
            <!-- Left Split: Parallax Image -->
            <div class="hidden md:block w-1/2 h-full relative overflow-hidden group animate-from-left">
                <!-- Parallax Background -->
                <img src="{{ asset('images/why_us_stadium.png') }}" alt="State of the art financial management"
                    class="absolute inset-0 w-full h-full object-cover transform scale-105 transition-transform duration-[3s] group-hover:scale-100 opacity-100 object-center" />

                <!-- Gradient Overlay -->
                <div
                    class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-r from-balantro-navy via-balantro-navy/40 to-transparent">
                </div>

                <!-- Decorative Text -->
                <div class="absolute bottom-12 left-8 md:left-12 p-6 z-10 hidden md:block">
                    <h3
                        class="text-5xl lg:text-7xl font-display font-bold text-white mb-4 tracking-tighter drop-shadow-2xl opacity-90">
                        Built for<br />Scale.
                    </h3>
                    <div class="h-2 w-24 bg-balantro-secondary rounded-full shadow-[0_0_20px_rgba(34,211,238,0.5)]"></div>
                </div>
            </div>

            <!-- Right Split: Content -->
            <div class="w-full md:w-1/2 relative z-10 p-8 lg:p-12 flex flex-col justify-center animate-from-right">
                <!-- Background Elements -->
                <div class="absolute top-0 right-0 w-full h-full bg-[#0a0f1c] z-0"></div>
                <div
                    class="absolute top-1/2 right-0 -translate-y-1/2 w-[400px] h-[400px] bg-balantro-primary/5 rounded-full blur-[100px] pointer-events-none z-0">
                </div>

                <div class="mb-10 lg:mb-12 relative z-20">
                    <div class="inline-block mb-4">
                        <span
                            class="py-1 px-3 rounded-full border border-balantro-secondary/30 bg-balantro-secondary/10 text-balantro-secondary text-xs font-semibold tracking-wider uppercase">
                            The Balantro Advantage
                        </span>
                    </div>
                    <h2 class="font-display text-3xl lg:text-4xl font-bold text-white mb-4 leading-[1.1]">
                        Why Businesses Choose <br />
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-balantro-secondary to-balantro-primary filter drop-shadow-lg">BALANTRO</span>
                    </h2>
                    <p class="text-slate-400 text-base max-w-lg leading-relaxed">
                        We don't just provide software; we provide a complete financial
                        operating system with human expertise included.
                    </p>
                </div>

                <div class="space-y-6 lg:space-y-8 relative z-20">
                    <!-- Item 1 -->
                    <div class="flex gap-6 group cursor-default" data-aos="fade-up" data-aos-delay="200">
                        <div class="relative flex-shrink-0 w-14 h-14">
                            <div
                                class="absolute inset-0 bg-balantro-secondary/20 rounded-2xl blur-lg group-hover:bg-balantro-secondary/40 transition-all duration-500 animate-pulse">
                            </div>
                            <div
                                class="relative w-14 h-14 rounded-2xl bg-[#0F172A] border border-white/10 flex items-center justify-center text-balantro-secondary group-hover:border-balantro-secondary/50 group-hover:scale-110 transition-all duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3
                                class="text-xl font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors">
                                Accounting-First, Always
                            </h3>

                            <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                                Clean books come before any filing. That means accurate data,
                                stress-free audits, and decisions you can actually trust.
                            </p>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="flex gap-6 group cursor-default" data-aos="fade-up" data-aos-delay="300">
                        <div class="relative flex-shrink-0 w-14 h-14">
                            <div class="absolute inset-0 bg-balantro-primary/20 rounded-2xl blur-lg group-hover:bg-balantro-primary/40 transition-all duration-500 animate-pulse"
                                style="animation-delay: 1s"></div>
                            <div
                                class="relative w-14 h-14 rounded-2xl bg-[#0F172A] border border-white/10 flex items-center justify-center text-balantro-primary group-hover:border-balantro-primary/50 group-hover:scale-110 transition-all duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3
                                class="text-xl font-bold text-white mb-2 group-hover:text-balantro-primary transition-colors">
                                We Own the Outcome
                            </h3>

                            <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                                We don't consult and walk away. We execute, follow up, and
                                take responsibility when something needs fixing — without you
                                having to ask.
                            </p>
                        </div>
                    </div>
                    <!-- Item 3 -->
                    <div class="flex gap-6 group cursor-default" data-aos="fade-up" data-aos-delay="400">
                        <div class="relative flex-shrink-0 w-14 h-14">
                            <div class="absolute inset-0 bg-balantro-primary/20 rounded-2xl blur-lg group-hover:bg-balantro-primary/40 transition-all duration-500 animate-pulse"
                                style="animation-delay: 2s"></div>
                            <div
                                class="relative w-14 h-14 rounded-2xl bg-[#0F172A] border border-white/10 flex items-center justify-center text-balantro-primary group-hover:border-balantro-primary/50 group-hover:scale-110 transition-all duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3
                                class="text-xl font-bold text-white mb-2 group-hover:text-balantro-primary transition-colors">
                                Human + Technology
                            </h3>
                            <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                                The speed of software combined with the judgment of a CFO.
                                Automation with professional oversight.
                            </p>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="flex gap-6 group cursor-default" data-aos="fade-up" data-aos-delay="500">
                        <div class="relative flex-shrink-0 w-14 h-14">
                            <div class="absolute inset-0 bg-balantro-secondary/20 rounded-2xl blur-lg group-hover:bg-balantro-secondary/40 transition-all duration-500 animate-pulse"
                                style="animation-delay: 3s"></div>
                            <div
                                class="relative w-14 h-14 rounded-2xl bg-[#0F172A] border border-white/10 flex items-center justify-center text-balantro-secondary group-hover:border-balantro-secondary/50 group-hover:scale-110 transition-all duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3
                                class="text-xl font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors">
                                Built Around Process, Not People
                            </h3>
                            <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                                Your financial operations never depend on a single person. Our
                                system carries the institutional memory, so work continues
                                seamlessly even when teams change.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 4: SERVICES OVERVIEW (UPDATED STYLE) -->
        <section class="scroll-section flex-col relative !bg-[#889EB5]">
            <!-- Parallax Background Image -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/chain_link.png') }}" alt="Services background"
                    class="absolute inset-0 w-full h-full object-cover opacity-90 md:opacity-100 mix-blend-multiply brightness-[0.95] contrast-[1.1]" />

                <!-- Gradient Overlay to ensure text readability on the right side -->
                <div
                    class="absolute inset-0 bg-gradient-to-r from-transparent via-[#889EB5]/20 to-[#889EB5]/90 md:bg-gradient-to-r md:from-transparent md:via-[#889EB5]/60 md:to-[#889EB5]">
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 animate-from-top">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                    <div>
                        <h2 class="font-display text-4xl md:text-5xl font-bold text-[#0f172a] mb-4" data-aos="fade-up">
                            Our Services
                        </h2>
                        <p class="text-[#334155] max-w-xl text-lg font-medium" data-aos="fade-up" data-aos-delay="100">
                            Comprehensive financial backend management.
                        </p>
                    </div>
                    <a href="#" data-aos="fade-left" data-aos-delay="200"
                        class="group text-[#0f172a] hover:text-white hover:bg-[#0f172a] transition-all flex items-center gap-2 font-bold px-6 py-3 rounded-full border border-[#0f172a]/20">
                        View All Services
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Service 1 -->
                    <div data-aos="fade-up" data-aos-delay="300"
                        class="group relative p-10 rounded-3xl bg-white/20 backdrop-blur-md border border-white/30 overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:bg-white/30 shadow-lg hover:shadow-xl animate-border-drift">
                        <div
                            class="w-16 h-16 rounded-2xl bg-[#0f172a] flex items-center justify-center mb-8 text-white shadow-md group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#0f172a] mb-4">
                            Accounting &<br />Bookkeeping (Core)
                        </h3>
                        <p class="text-[#334155] leading-relaxed mb-8 font-medium">
                            Day-to-day accounting, monthly closing, and financial structure.
                        </p>
                        <ul class="space-y-2 text-sm text-[#475569] font-medium">
                            <li class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0f172a]"></div>
                                Daily Entry Management
                            </li>
                            <li class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0f172a]"></div>
                                Bank Reconciliation
                            </li>
                            <li class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0f172a]"></div>
                                Vendor Mgmt
                            </li>
                        </ul>
                    </div>

                    <!-- Service 2 -->
                    <div data-aos="fade-up" data-aos-delay="400"
                        class="group relative p-10 rounded-3xl bg-white/20 backdrop-blur-md border border-white/30 overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:bg-white/30 shadow-lg hover:shadow-xl animate-border-drift"
                        style="animation-delay: 1.5s">
                        <div
                            class="w-16 h-16 rounded-2xl bg-[#0f172a] flex items-center justify-center mb-8 text-white shadow-md group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#0f172a] mb-4">
                            Compliance<br />Management
                        </h3>
                        <p class="text-[#334155] leading-relaxed mb-8 font-medium">
                            GST, income tax, audits — managed systematically.
                        </p>
                        <ul class="space-y-2 text-sm text-[#475569] font-medium">
                            <li class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0f172a]"></div>
                                GST Filing & Reco
                            </li>
                            <li class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0f172a]"></div>
                                TDS Compliance
                            </li>
                            <li class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0f172a]"></div>
                                Audit Support
                            </li>
                        </ul>
                    </div>

                    <!-- Service 3 -->
                    <div data-aos="fade-up" data-aos-delay="500"
                        class="group relative p-10 rounded-3xl bg-white/20 backdrop-blur-md border border-white/30 overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:bg-white/30 shadow-lg hover:shadow-xl animate-border-drift"
                        style="animation-delay: 3s">
                        <div
                            class="w-16 h-16 rounded-2xl bg-[#0f172a] flex items-center justify-center mb-8 text-white shadow-md group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#0f172a] mb-4">
                            Financial Reports<br />& Visibility
                        </h3>
                        <p class="text-[#334155] leading-relaxed mb-8 font-medium">
                            Business-friendly reports for better decisions.
                        </p>
                        <ul class="space-y-2 text-sm text-[#475569] font-medium">
                            <li class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0f172a]"></div>
                                Monthly MIS
                            </li>
                            <li class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0f172a]"></div>
                                Cash Flow Analysis
                            </li>
                            <li class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0f172a]"></div>
                                Profitability Reports
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 5: PLATFORM FEATURES -->
        <section class="scroll-section flex-col relative bg-balantro-navy">
            <canvas id="canvas-platform" class="absolute inset-0 w-full h-full z-0 opacity-40"></canvas>
            <!-- Connecting Line Decorations -->
            <div class="absolute inset-0 flex justify-center pointer-events-none">
                <div class="w-px h-full bg-white/5"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 animate-on-entry">
                <div class="text-center mb-20">
                    <h2 class="font-display text-4xl md:text-5xl font-bold text-white mb-8" data-aos="fade-up">
                        A Platform That Supports Discipline <br />
                        <span class="text-slate-500">— Not Replaces Judgment</span>
                    </h2>

                    <div class="flex items-center justify-center gap-8" data-aos="fade-up" data-aos-delay="200">
                        <span
                            class="px-6 py-2 rounded-full bg-balantro-secondary/10 text-balantro-secondary text-sm font-semibold border border-balantro-secondary/20">Technology
                            enables efficiency</span>
                        <span class="text-slate-700 mx-2">+</span>
                        <span
                            class="px-6 py-2 rounded-full bg-balantro-primary/10 text-balantro-primary text-sm font-semibold border border-balantro-primary/20">Accountability
                            remains human</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-10 gap-x-12 text-left">
                    <!-- Feature 1 -->
                    <div class="flex gap-6 group">
                        <div
                            class="w-12 h-12 shrink-0 rounded-xl bg-slate-800 flex items-center justify-center border border-white/10 text-balantro-secondary group-hover:border-balantro-secondary/50 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-lg font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors">
                                Centralized Collection
                            </h4>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Unified document gathering via App, Web Portal, and integrated
                                WhatsApp bots.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="flex gap-6 group">
                        <div
                            class="w-12 h-12 shrink-0 rounded-xl bg-slate-800 flex items-center justify-center border border-white/10 text-balantro-secondary group-hover:border-balantro-secondary/50 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-lg font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors">
                                Auto Classification
                            </h4>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                ML-powered sorting with mandatory expert verification layer
                                for 100% accuracy.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex gap-6 group">
                        <div
                            class="w-12 h-12 shrink-0 rounded-xl bg-slate-800 flex items-center justify-center border border-white/10 text-balantro-secondary group-hover:border-balantro-secondary/50 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-lg font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors">
                                Task-based Workflows
                            </h4>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Structured execution paths that ensure no step is skipped,
                                regardless of who is working.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="flex gap-6 group">
                        <div
                            class="w-12 h-12 shrink-0 rounded-xl bg-slate-800 flex items-center justify-center border border-white/10 text-balantro-secondary group-hover:border-balantro-secondary/50 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-lg font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors">
                                Multi-level Review
                            </h4>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Maker-Checker concepts applied to every critical financial
                                transaction.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 5 -->
                    <div class="flex gap-6 group">
                        <div
                            class="w-12 h-12 shrink-0 rounded-xl bg-slate-800 flex items-center justify-center border border-white/10 text-balantro-secondary group-hover:border-balantro-secondary/50 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-lg font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors">
                                Smart Reporting
                            </h4>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                CEO-style dashboards that highlight anomalies, cash
                                bottlenecks, and profit trends.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 6 -->
                    <div class="flex gap-6 group">
                        <div
                            class="w-12 h-12 shrink-0 rounded-xl bg-slate-800 flex items-center justify-center border border-white/10 text-balantro-secondary group-hover:border-balantro-secondary/50 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-lg font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors">
                                Bank-Grade Security
                            </h4>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                256-bit encryption for all data at rest and in transit. Your
                                financial data is safe.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 6: AUTHORITY & TRUST -->
        <section class="scroll-section flex-col border-y border-white/20 !bg-[#0f1d3a] relative">
            <div
                class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-balantro-secondary/15 via-transparent to-transparent">
            </div>

            <div
                class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 animate-stats flex flex-col justify-center items-center">
                <h2 class="font-display text-2xl md:text-3xl font-bold text-white mb-16 tracking-wide uppercase text-center w-full"
                    data-aos="fade-up">
                    Trusted by Growing Businesses
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-y-12 gap-x-0 text-center">
                    <div class="group relative px-2 sm:px-6 border-r border-white/10">
                        <div class="text-5xl md:text-6xl font-bold font-display text-transparent bg-clip-text bg-gradient-to-b from-white to-slate-500 mb-4 group-hover:text-balantro-secondary transition-colors duration-500 stat-number"
                            data-target="250">
                            0
                        </div>

                        <div class="text-xs font-bold text-balantro-secondary uppercase tracking-[0.2em] mb-2">
                            Active Clients
                        </div>
                        <p class="text-[10px] text-slate-600">Across 12 Industries</p>
                    </div>
                    <div class="group relative px-2 sm:px-6 md:border-r border-white/10">
                        <div class="text-5xl md:text-6xl font-bold font-display text-transparent bg-clip-text bg-gradient-to-b from-white to-slate-500 mb-4 group-hover:text-balantro-secondary transition-colors duration-500 animate-pulse stat-number"
                            data-target="10000">
                            0
                        </div>

                        <div class="text-xs font-bold text-balantro-secondary uppercase tracking-[0.2em] mb-2">
                            Transactions
                        </div>
                        <p class="text-[10px] text-slate-600">Managed Monthly</p>
                    </div>
                    <div class="group relative px-2 sm:px-6 border-r border-white/10">
                        <div class="text-5xl md:text-6xl font-bold font-display text-transparent bg-clip-text bg-gradient-to-b from-white to-slate-500 mb-4 group-hover:text-balantro-secondary transition-colors duration-500 stat-number"
                            data-target="99">
                            0
                        </div>

                        <div class="text-xs font-bold text-balantro-secondary uppercase tracking-[0.2em] mb-2">
                            Compliance Rate
                        </div>
                        <p class="text-[10px] text-slate-600">On-Time Filings</p>
                    </div>
                    <div class="group relative px-2 sm:px-6">
                        <div
                            class="text-5xl md:text-6xl font-bold font-display text-transparent bg-clip-text bg-gradient-to-b from-white to-slate-500 mb-4 group-hover:text-balantro-secondary transition-colors duration-500">
                            9+
                        </div>
                        <div class="text-xs font-bold text-balantro-secondary uppercase tracking-[0.2em] mb-2">
                            Years Exp.
                        </div>
                        <p class="text-[10px] text-slate-600">Professional Leadership</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 7: WHAT WE DELIVER FOR CLIENTS - SPLIT LAYOUT -->
        <section class="scroll-section flex-row items-stretch relative bg-balantro-navy">
            <!-- Left Split: Content -->
            <div
                class="w-full md:w-1/2 relative z-20 flex flex-col justify-center p-6 md:p-8 lg:p-10 bg-balantro-navy animate-from-left">
                <div class="mb-4 lg:mb-6">
                    <div class="inline-block mb-3">
                        <span
                            class="py-1 px-3 rounded-full border border-balantro-primary/30 bg-balantro-primary/10 text-balantro-primary text-[10px] font-semibold tracking-wider uppercase">
                            Client Success Stories
                        </span>
                    </div>
                    <h2 class="font-display text-2xl lg:text-3xl font-bold text-white mb-3 leading-tight">
                        What We Deliver <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">for
                            Clients</span>
                    </h2>
                    <div class="h-1 w-16 bg-gradient-to-r from-balantro-secondary to-balantro-primary rounded-full"></div>
                </div>

                <div class="space-y-4 lg:space-y-5">
                    <!-- Testimonial 1 -->
                    <div
                        class="group relative p-4 lg:p-5 rounded-xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.05] hover:border-balantro-secondary/30 transition-all duration-300 animate-border-drift">
                        <div
                            class="absolute -top-2 -right-2 w-7 h-7 bg-balantro-secondary/20 rounded-full flex items-center justify-center text-balantro-secondary text-base font-serif leading-none group-hover:scale-110 transition-transform">
                            ❝
                        </div>

                        <p class="text-slate-300 text-sm leading-relaxed mb-3 italic">
                            "Balantro transformed our chaotic accounts into a streamlined
                            system. We now have total clarity."
                        </p>

                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-sm font-bold text-white border border-white/10 shadow-lg">
                                RM
                            </div>
                            <div>
                                <div class="font-bold text-white text-base">Rahul Mehta</div>
                                <div
                                    class="text-xs text-balantro-secondary uppercase tracking-widest font-semibold opacity-80">
                                    Manufacturing Director
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div
                        class="group relative p-4 lg:p-5 rounded-xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.05] hover:border-balantro-primary/30 transition-all duration-300 animate-border-drift">
                        <div
                            class="absolute -top-2 -right-2 w-7 h-7 bg-balantro-primary/20 rounded-full flex items-center justify-center text-balantro-primary text-base font-serif leading-none group-hover:scale-110 transition-transform">
                            ❝
                        </div>

                        <p class="text-slate-300 text-sm leading-relaxed mb-3 italic">
                            "Finally, I don't have to worry about GST filings and late fees.
                            The backend ownership model is real."
                        </p>

                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-sm font-bold text-white border border-white/10 shadow-lg">
                                AK
                            </div>
                            <div>
                                <div class="font-bold text-white text-base">
                                    Anjali Kapoor
                                </div>
                                <div
                                    class="text-xs text-balantro-primary uppercase tracking-widest font-semibold opacity-80">
                                    E-commerce Founder
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div
                        class="group relative p-4 lg:p-5 rounded-xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.05] hover:border-white/30 transition-all duration-300 animate-border-drift">
                        <div
                            class="absolute -top-2 -right-2 w-7 h-7 bg-white/10 rounded-full flex items-center justify-center text-white text-base font-serif leading-none group-hover:scale-110 transition-transform">
                            ❝
                        </div>

                        <p class="text-slate-300 text-sm leading-relaxed mb-3 italic">
                            "MIS reports used to be months late. Now, I get profitable
                            insights on day 5 of every month."
                        </p>

                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-sm font-bold text-white border border-white/10 shadow-lg">
                                VS
                            </div>
                            <div>
                                <div class="font-bold text-white text-base">Vikram Singh</div>
                                <div class="text-xs text-slate-400 uppercase tracking-widest font-semibold opacity-80">
                                    Logistics CEO
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Split: Parallax Image -->
            <div class="w-full md:w-1/2 h-full md:h-full relative overflow-hidden group animate-from-right">
                <!-- Parallax Background -->
                <img src="{{ asset('images/deliverables_goal.png') }}" alt="Client success results"
                    class="absolute inset-0 w-full h-full object-cover transform scale-105 transition-transform duration-[3s] group-hover:scale-100 opacity-100 object-center" />

                <!-- Gradient Overlay -->
                <div
                    class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-l from-balantro-navy via-balantro-navy/20 to-transparent">
                </div>

                <!-- Content Overlay on Image -->
                <div class="absolute bottom-16 right-12 text-right hidden md:block z-10">
                    <h3 class="text-6xl font-display font-bold text-white mb-2 drop-shadow-xl opacity-90">
                        Results.
                    </h3>
                    <p class="text-xl text-balantro-secondary font-medium tracking-widest uppercase">
                        Delivered Daily.
                    </p>
                </div>
            </div>
        </section>

        <!-- SECTION 8: FINAL CTA -->
        <section class="scroll-section flex-col relative">
            <!-- Intense Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-balantro-navy via-[#051421] to-balantro-navy z-0"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(34,211,238,0.15),transparent_70%)] z-0 animate-pulse"
                style="animation-duration: 4s"></div>
            <div class="absolute inset-0 opacity-30 z-0"
                style="
            background-image: url(&quot;https://grainy-gradients.vercel.app/noise.svg&quot;);
          ">
            </div>

            <div class="max-w-6xl mx-auto px-4 text-center relative z-10 animate-on-entry">
                <h2 class="font-display text-4xl md:text-6xl font-bold text-white mb-8 leading-tight drop-shadow-2xl"
                    data-aos="fade-up">
                    "Stop Managing Your Accountant. <br />
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-balantro-secondary to-balantro-primary animate-gradient-text">—
                        Start Running Your Business.</span>
                </h2>

                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center" data-aos="fade-up"
                    data-aos-delay="200">
                    <a href="#"
                        class="w-full sm:w-auto px-12 py-6 rounded-full bg-white text-balantro-navy font-bold text-xl transition-all hover:bg-slate-200 hover:scale-105 shadow-[0_0_40px_rgba(255,255,255,0.3)]">
                        Talk to Our Team
                    </a>
                    <a href="#"
                        class="w-full sm:w-auto px-12 py-6 rounded-full pill-button text-white font-medium text-xl flex items-center justify-center gap-3 hover:bg-white/10 border border-white/20">
                        <svg class="w-6 h-6 text-balantro-secondary" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                            </path>
                        </svg>
                        Call Now
                    </a>
                </div>
                <p class="mt-8 text-slate-500 text-sm">
                    15 minutes. Honest answers about whether we're the right fit for
                    your business. No pitch, No pressure.
                </p>
            </div>
        </section>
        <!-- JOIN THE NEW GENERATION SECTION -->
        <section class="scroll-section flex-col relative w-full">
            <!-- Background Video -->
            <video id="new-gen-video" loop muted playsinline preload="none"
                class="absolute inset-0 w-full h-full object-cover z-0 opacity-80 mix-blend-screen"
                style="pointer-events: none">
                <source src="images/wave_footer_desktop_20250426.webm" type="video/webm" />
            </video>

            <!-- Gradient Overlay for smooth start/end blending -->
            <div
                class="absolute inset-0 bg-gradient-to-b from-[#02040a] via-transparent to-[#02040a] z-0 pointer-events-none">
            </div>

            <!-- Content -->
            <div class="relative z-10 text-center px-4 max-w-4xl mx-auto flex flex-col items-center animate-on-entry">
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

        <!-- Script for lazy loading the wave video -->
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
    </div>
    <!-- End main-stack-wrapper -->



@endsection
@section('scripts')

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
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

        // Initialize animations on load
        document.addEventListener("DOMContentLoaded", () => {
            window.initBalantroAnimations = function() {
                document.documentElement.classList.remove("intro-running");
                AOS.init({
                    duration: 800,
                    once: false, // Changed to false for SoFi re-trigger on scroll feeling
                    offset: 100,
                    easing: "ease-out-cubic",
                });

                // GSAP ScrollTrigger for exact SoFi-like scrubbing
                if (typeof gsap !== "undefined") {
                    gsap.registerPlugin(ScrollTrigger);

                    // 1) 3D Phone inner rows - simply set them to be visible or let them fade with the section
                    const mobileRows = document.querySelectorAll(
                        ".animate-mobile-row, .mobile-view-rows",
                    );
                    if (mobileRows.length > 0) {
                        gsap.set(mobileRows, {
                            opacity: 1,
                            y: 0,
                            visibility: "visible"
                        });
                    }

                    // 2) Removed staggered feature steps to unify with section reveal

                    // 3) Master Pinned Stack Animation Logic
                    const sections = gsap.utils.toArray(".scroll-section");
                    const stackWrapper = document.querySelector(".main-stack-wrapper");

                    let mm = gsap.matchMedia();

                    mm.add("(min-width: 769px)", () => {
                        const masterTL = gsap.timeline({
                            scrollTrigger: {
                                trigger: stackWrapper,
                                start: "top top",
                                end: () =>
                                    `+=${sections.length * 150}%`, // Reduced scroll length for quicker transitions
                                pin: true,
                                scrub: 1, // Reduced delay for more responsive feel
                            },
                        });

                        sections.forEach((section, i) => {
                            if (i > 0) {
                                masterTL.add(`section-${i}-enter`);
                                gsap.set(section, {
                                    zIndex: 10 + i
                                });

                                masterTL.fromTo(
                                    section, {
                                        yPercent: 100,
                                        opacity: 0,
                                        autoAlpha: 0
                                    }, {
                                        yPercent: 0,
                                        opacity: 1,
                                        autoAlpha: 1,
                                        duration: 2,
                                        ease: "power2.inOut",
                                    },
                                    `section-${i}-enter`,
                                );

                                // --- HANDLE DIRECTIONAL INTERNAL TRANSITIONS ---

                                // Top entrance
                                const fromTop = section.querySelectorAll(".animate-from-top");
                                if (fromTop.length) {
                                    masterTL.fromTo(
                                        fromTop, {
                                            opacity: 0,
                                            y: -50
                                        }, {
                                            opacity: 1,
                                            y: 0,
                                            duration: 1.2,
                                            ease: "power3.out",
                                            stagger: 0.15,
                                        },
                                        `section-${i}-enter+=0.5`,
                                    );
                                }

                                // Special handling for Intelligence Reveal Heading to come one-by-one
                                if (section.id === "section-intelligence-reveal") {
                                    const lines = section.querySelectorAll(
                                        ".animate-from-bottom",
                                    );
                                    if (lines.length) {
                                        masterTL.fromTo(
                                            lines, {
                                                opacity: 0,
                                                y: 100
                                            }, {
                                                opacity: 1,
                                                y: 0,
                                                duration: 1.8,
                                                ease: "power4.out",
                                                stagger: 1.2, // Very long stagger for cinematic effect
                                                force3D: true,
                                            },
                                            `section-${i}-enter+=0.6`,
                                        );
                                    }
                                } else if (section.id === "section-balantro-process") {
                                    const processBoxes = section.querySelectorAll(
                                        ".animate-from-bottom",
                                    );
                                    if (processBoxes.length) {
                                        masterTL.fromTo(
                                            processBoxes, {
                                                opacity: 0,
                                                y: 150
                                            }, {
                                                opacity: 1,
                                                y: 0,
                                                duration: 2,
                                                ease: "power4.out",
                                                stagger: 0.8, // Long stagger for one-by-one effect
                                                force3D: true,
                                            },
                                            `section-${i}-enter+=0.6`,
                                        );
                                    }
                                } else {
                                    const fromBottom = section.querySelectorAll(
                                        ".animate-from-bottom",
                                    );
                                    if (fromBottom.length) {
                                        masterTL.fromTo(
                                            fromBottom, {
                                                opacity: 0,
                                                y: 50
                                            }, {
                                                opacity: 1,
                                                y: 0,
                                                duration: 1.5,
                                                ease: "power4.out", // High-end smoothness
                                                stagger: 0.25,
                                                force3D: true,
                                            },
                                            `section-${i}-enter+=0.6`,
                                        );
                                    }
                                }

                                // Left entrance
                                const fromLeft = section.querySelectorAll(".animate-from-left");
                                if (fromLeft.length) {
                                    masterTL.fromTo(
                                        fromLeft, {
                                            opacity: 0,
                                            x: -100
                                        }, {
                                            opacity: 1,
                                            x: 0,
                                            duration: 1.5,
                                            ease: "power3.out",
                                            stagger: 0.2,
                                        },
                                        `section-${i}-enter+=0.5`,
                                    );
                                }

                                // Right entrance
                                const fromRight = section.querySelectorAll(
                                    ".animate-from-right",
                                );
                                if (fromRight.length) {
                                    masterTL.fromTo(
                                        fromRight, {
                                            opacity: 0,
                                            x: 100
                                        }, {
                                            opacity: 1,
                                            x: 0,
                                            duration: 1.5,
                                            ease: "power3.out",
                                            stagger: 0.2,
                                        },
                                        `section-${i}-enter+=0.5`,
                                    );
                                }

                                // Legacy/Standard entrance
                                const standard = section.querySelectorAll(
                                    ".animate-on-entry:not(.animate-from-top):not(.animate-from-bottom)",
                                );
                                if (standard.length) {
                                    masterTL.fromTo(
                                        standard, {
                                            opacity: 0,
                                            y: 30
                                        }, {
                                            opacity: 1,
                                            y: 0,
                                            duration: 0.8,
                                            stagger: 0.1
                                        },
                                        `section-${i}-enter+=0.7`,
                                    );
                                }

                                // --- STATS COUNTER ANIMATION ---
                                const statsSection = section.querySelector(".animate-stats");
                                if (statsSection) {
                                    const numbers = section.querySelectorAll(".stat-number");
                                    numbers.forEach((num) => {
                                        const target = parseInt(num.getAttribute(
                                            "data-target"));
                                        const counter = {
                                            value: 0
                                        };
                                        masterTL.to(
                                            counter, {
                                                value: target,
                                                duration: 2,
                                                ease: "power1.inOut",
                                                onUpdate: () => {
                                                    if (target === 10000) {
                                                        num.innerText =
                                                            (counter.value /
                                                                1000)
                                                            .toFixed(1)
                                                            .replace(".0", "") +
                                                            "k+";
                                                    } else if (target === 99) {
                                                        num.innerText = Math
                                                            .round(counter
                                                                .value) + "%";
                                                    } else if (target === 250) {
                                                        num.innerText = Math
                                                            .round(counter
                                                                .value) + "+";
                                                    } else {
                                                        num.innerText = Math
                                                            .round(counter
                                                                .value);
                                                    }
                                                },
                                            },
                                            `section-${i}-enter+=1`,
                                        );
                                    });
                                }

                                // --- MARQUEE SCROLL ANIMATION ---
                                const marquee = section.querySelector("#why-choose-marquee");
                                if (marquee) {
                                    masterTL.fromTo(
                                        marquee, {
                                            xPercent: 10,
                                            opacity: 0
                                        }, {
                                            xPercent: -120,
                                            opacity: 1,
                                            duration: 8,
                                            ease: "none",
                                        },
                                        `section-${i}-enter`,
                                    );
                                }

                                masterTL.to(
                                    sections[i - 1], {
                                        opacity: 0,
                                        autoAlpha: 0,
                                        duration: 2,
                                        ease: "power2.inOut",
                                    },
                                    `section-${i}-enter`,
                                );
                                masterTL.to({}, {
                                    duration: 1
                                });
                            }
                        });

                        // Hold the final section on screen before unpinning to avoid the footer overlapping it right away
                        masterTL.to({}, {
                            duration: 4
                        });
                    }); // End mm.add

                    // Ensure first section content is visible immediately
                    const firstSection = sections[0];
                    const introContent = firstSection.querySelectorAll(
                        ".animate-on-entry, .animate-from-top, .animate-from-bottom",
                    );
                    gsap.set(firstSection, {
                        visibility: "visible",
                        opacity: 1,
                        autoAlpha: 1,
                    });
                    gsap.set(introContent, {
                        opacity: 1,
                        y: 0,
                        x: 0,
                        scale: 1,
                        visibility: "visible",
                    });

                    // On Mobile: Ensure Section 2 is also ready to show
                    if (window.innerWidth < 768) {
                        gsap.set(sections[1], {
                            visibility: "visible"
                        });
                        ScrollTrigger.refresh();
                    }

                    // Refresh on resize to fix mobile height issues
                    window.addEventListener("resize", () => ScrollTrigger.refresh());
                }
            };

            if (document.documentElement.classList.contains("skip-intro")) {
                window.initBalantroAnimations();
            }

            new ParticleNetwork("canvas-why-us", {
                particleColor: "rgba(34, 211, 238, 0.5)",
                lineColor: "rgba(34, 211, 238, 1)",
                particleAmount: 50,
                linkRadius: 120,
            });
            new ParticleNetwork("canvas-platform", {
                particleColor: "rgba(14, 165, 233, 0.5)",
                lineColor: "rgba(14, 165, 233, 1)",
                particleAmount: 40,
                linkRadius: 100,
            });
            new ParticleNetwork("canvas-footer", {
                particleColor: "rgba(14, 165, 233, 0.4)",
                lineColor: "rgba(14, 165, 233, 0.6)",
                particleAmount: 60,
                linkRadius: 150,
            });
        });
    </script>
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
    <script src="{{ asset('js/nav-scroll.js') }}"></script>
    <script src="{{ asset('js/magic-button.js') }}"></script>

@endsection
