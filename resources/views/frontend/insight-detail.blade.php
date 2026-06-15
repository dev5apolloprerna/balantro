@extends('layouts.front')

@section('title', 'Insight Detail')

@section('content')

    {{-- paste features.html body here --}}
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
        /* Reading Progress Bar */
        #reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #22d3ee, #0ea5e9);
            width: 0%;
            z-index: 9999;
            transition: width 0.1s linear;
            box-shadow: 0 0 10px rgba(34, 211, 238, 0.6);
        }

        /* Article prose styles */
        .prose-article h2 {
            font-family: "Outfit", sans-serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            margin: 2.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        .prose-article h3 {
            font-family: "Outfit", sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: #e2e8f0;
            margin: 2rem 0 0.75rem;
        }

        .prose-article p {
            color: #94a3b8;
            line-height: 1.85;
            margin-bottom: 1.4rem;
            font-size: 1.05rem;
        }

        .prose-article ul {
            list-style: none;
            margin: 1.2rem 0 1.6rem;
            padding: 0;
        }

        .prose-article ul li {
            color: #94a3b8;
            padding: 0.4rem 0 0.4rem 1.6rem;
            position: relative;
            font-size: 1rem;
            line-height: 1.75;
        }

        .prose-article ul li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0.85rem;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22d3ee;
        }

        .prose-article blockquote {
            border-left: 3px solid #22d3ee;
            padding: 1rem 1.5rem;
            margin: 2rem 0;
            background: rgba(34, 211, 238, 0.04);
            border-radius: 0 12px 12px 0;
        }

        .prose-article blockquote p {
            color: #cbd5e1;
            font-style: italic;
            font-size: 1.1rem;
            margin: 0;
        }

        .prose-article strong {
            color: #e2e8f0;
            font-weight: 600;
        }

        /* TOC */
        .toc-link {
            transition: all 0.2s ease;
        }

        .toc-link:hover,
        .toc-link.active {
            color: #22d3ee;
            padding-left: 0.5rem;
        }

        .toc-link.active {
            border-left: 2px solid #22d3ee;
        }

        /* Highlight box */
        .insight-highlight {
            background: rgba(14, 165, 233, 0.06);
            border: 1px solid rgba(14, 165, 233, 0.2);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin: 2rem 0;
        }

        /* Back to top */
        #back-to-top {
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        #back-to-top.visible {
            opacity: 1;
            pointer-events: all;
        }

        .related-card:hover {
            transform: translateY(-4px);
            border-color: rgba(34, 211, 238, 0.3);
        }
    </style>

    <!-- Reading Progress -->
    <div id="reading-progress"></div>

    <!-- ARTICLE HERO -->
    <section class="inner-hero-vh relative overflow-hidden">
        <div class="absolute inset-0 z-0 bg-[#02040a]">
            <div class="hero-grid-bg">
                <div class="hero-grid-lines"></div>
                <div class="hero-grid-beam"></div>
                <div class="hero-grid-scanline"></div>
                <div class="hero-grid-corner-tr"></div>
                <div class="hero-grid-corner-bl"></div>
                <div class="hero-grid-mask"></div>
            </div>
            <div class="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-balantro-primary/20 rounded-full blur-[120px] pointer-events-none animate-pulse"
                style="animation-duration: 4s; z-index: 2"></div>
            <div
                class="absolute bottom-0 left-0 right-0 h-48 bg-gradient-to-t from-[#02040a] to-transparent z-10 pointer-events-none">
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
            <!-- Breadcrumb -->
            <nav
                class="flex items-center gap-2 text-sm text-slate-500 mb-8 opacity-0 animate-[fadeInUp_0.8s_ease-out_forwards]">
                <a href="index.html" class="hover:text-balantro-secondary transition-colors">Home</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="insights.html" class="hover:text-balantro-secondary transition-colors">Insights</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-slate-400 truncate">
                    {{-- Clean Books --}}
                    {{ $blog->title }}
                </span>
            </nav>

            <!-- Category & Meta -->
            <div class="flex flex-wrap items-center gap-3 mb-6 opacity-0 animate-[fadeInUp_0.8s_ease-out_0.1s_forwards]">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-balantro-primary/10 border border-balantro-primary/30 text-balantro-primary text-xs font-bold uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-balantro-primary animate-pulse"></span>
                    {{-- Accounting --}}
                    {{ $blog->category->name ?? 'Insight' }}
                </span>
                <span class="text-slate-500 text-sm">·</span>
                <span class="text-slate-400 text-sm">5 min read</span>
                <span class="text-slate-500 text-sm">·</span>
                <span class="text-slate-400 text-sm">March 3, 2026</span>
            </div>

            <!-- Title -->
            <h1
                class="font-display text-3xl md:text-5xl lg:text-6xl font-bold tracking-tight mb-6 leading-[1.1] text-white opacity-0 animate-[fadeInUp_0.8s_ease-out_0.2s_forwards]">
                {{-- Why Clean Books Matter More Than --}}
                {{ $blog->title }}
                {{-- <span class="relative inline-block mt-2">
                    <span
                        class="absolute -inset-2 bg-gradient-to-r from-balantro-primary via-[#a78bfa] to-balantro-secondary blur-2xl opacity-40"></span>
                    <span
                        class="relative text-transparent bg-clip-text bg-gradient-to-r from-white via-blue-100 to-white">Profits
                        in Early Growth</span>
                </span> --}}
            </h1>

            <!-- Subtitle -->
            <p
                class="text-lg md:text-xl text-slate-400 max-w-2xl leading-relaxed mb-10 opacity-0 animate-[fadeInUp_0.8s_ease-out_0.3s_forwards]">
                A practical view on financial discipline. Most early-stage businesses
                chase profits — but the ones that survive and scale obsess over their
                books first.
            </p>

            <!-- Author Strip -->
            <div class="flex items-center gap-4 opacity-0 animate-[fadeInUp_0.8s_ease-out_0.4s_forwards]">
                <div
                    class="w-12 h-12 rounded-full bg-gradient-to-br from-balantro-secondary to-balantro-primary flex items-center justify-center font-display font-bold text-balantro-navy text-lg">
                    B
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">
                        Balantro Editorial Team
                    </p>
                    <p class="text-slate-500 text-xs">
                        Accounting & Compliance Experts
                    </p>
                </div>
                <!-- Share buttons -->
                <div class="ml-auto flex items-center gap-3">
                    <span class="text-slate-500 text-xs uppercase tracking-widest hidden sm:block">Share</span>
                    <a href="#" aria-label="Share on LinkedIn"
                        class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-balantro-secondary hover:border-balantro-secondary/40 transition-all">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                        </svg>
                    </a>
                    <a href="#" aria-label="Share on X"
                        class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-balantro-secondary hover:border-balantro-secondary/40 transition-all">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ARTICLE BODY -->
    <section class="pb-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex gap-12 items-start">
                <!-- MAIN CONTENT -->
                <article class="flex-1 min-w-0 prose-article" data-aos="fade-up">
                    <!-- Feature Banner -->
                    <div
                        class="rounded-2xl bg-gradient-to-br from-balantro-primary/10 via-[#0a0f1c] to-[#0a0f1c] border border-balantro-primary/20 aspect-video flex items-center justify-center mb-12 relative overflow-hidden">
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-balantro-primary/20 via-transparent to-balantro-secondary/10">
                        </div>
                        <div class="relative z-10 text-center">
                            <div class="text-6xl mb-4">📚</div>
                            <p class="text-white/60 font-display font-medium text-lg">
                                Financial Discipline in Early Growth
                            </p>
                        </div>
                        <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-balantro-primary/10 rounded-full blur-3xl">
                        </div>
                    </div>

                    <p>{!! $blog->description !!}</p>

                    {{-- <h2 id="the-real-problem">The Real Problem Isn't Revenue</h2>
                    <p>
                        Most founders of early-stage businesses obsess over one thing:
                        <strong>revenue</strong>. They celebrate every new client, every
                        invoice raised, every rupee that hits the bank account. And that's
                        understandable — revenue is visible, exciting, and feels like
                        proof that the business is working.
                    </p>
                    <p>
                        But here's the uncomfortable truth that most accountants won't say
                        directly:
                        <strong>revenue without clean books is a ticking time bomb</strong>. You can be bringing in ₹50
                        lakhs a year and still be unable to
                        tell whether you're actually profitable. You can have clients
                        paying on time and still find yourself short on cash every
                        quarter.
                    </p>

                    <blockquote>
                        <p>
                            "You can't manage what you can't measure. And you can't measure
                            anything if your books are a mess."
                        </p>
                    </blockquote>

                    <h2 id="what-clean-books-mean">
                        What 'Clean Books' Actually Means
                    </h2>
                    <p>
                        Clean books doesn't mean having a fancy accounting software
                        subscription. It means your financial records are:
                    </p>
                    <ul>
                        <li>
                            <strong>Timely</strong> — entries made within the week, not
                            months later
                        </li>
                        <li>
                            <strong>Categorised correctly</strong> — expenses mapped to the
                            right heads
                        </li>
                        <li>
                            <strong>Reconciled regularly</strong> — your books match your
                            bank statements
                        </li>
                        <li>
                            <strong>GST-compliant</strong> — all sales and purchases entered
                            with proper tax codes
                        </li>
                        <li>
                            <strong>Separated</strong> — personal expenses are never mixed
                            with business
                        </li>
                    </ul>
                    <p>
                        When these five things are consistently in order, something
                        powerful happens: you can actually see your business in real time.
                    </p>

                    <div class="insight-highlight">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl mt-0.5">💡</span>
                            <div>
                                <p class="text-balantro-secondary font-semibold font-display text-base !mb-1">
                                    Key Insight
                                </p>
                                <p class="!mb-0 !text-slate-300">
                                    Businesses with clean, reconciled books are 3x more likely
                                    to receive debt financing and 2x more likely to identify
                                    cost-saving opportunities within their first year of
                                    operation.
                                </p>
                            </div>
                        </div>
                    </div>

                    <h2 id="why-early-stage">Why Early-Stage Is the Critical Window</h2>
                    <p>
                        The habits you set in months 1–24 of a business become the default
                        operating system for the next decade. If you start by keeping
                        sloppy records, you're not just creating a mess — you're building
                        a culture of financial blindness that's extremely hard to change
                        later.
                    </p>

                    <h3>The Cost of Catching Up</h3>
                    <p>
                        We've seen businesses come to us after 2+ years of poor
                        record-keeping. The cleanup cost — in time, professional fees, and
                        stress — is always multiples higher than if they'd done it right
                        from the start. In some cases, the backlog was so large that the
                        business couldn't reconstruct its financial history at all.
                    </p>

                    <h3>The Tax Exposure Risk</h3>
                    <p>
                        Messy books aren't just internally confusing — they create real
                        GST scrutiny risk. If your GSTR-1 doesn't match your books, if
                        your ITC claims don't have proper documentation, if your TDS
                        records are incomplete — you're exposed. The interest and
                        penalties alone can wipe out months of profit.
                    </p>

                    <h2 id="profit-vs-discipline">Profit vs. Financial Discipline</h2>
                    <p>Here's the framing shift we ask every new client to make:</p>

                    <blockquote>
                        <p>
                            "A business that makes ₹10 lakh profit with clean books is far
                            more valuable — and survivable — than one making ₹40 lakh
                            without them."
                        </p>
                    </blockquote>

                    <p>
                        Why? Because the first business can do the following. The second
                        cannot:
                    </p>
                    <ul>
                        <li>Apply for a bank loan with confidence</li>
                        <li>Know exactly which product or service is most profitable</li>
                        <li>Pay GST on time without surprise cash shortfalls</li>
                        <li>
                            Hand over finances to a CA or investor with zero embarrassment
                        </li>
                        <li>Catch expense leakage and take action before it compounds</li>
                    </ul>

                    <h2 id="practical-steps">Practical Steps to Get There</h2>
                    <p>
                        You don't need to be a finance expert. You need a system. Here's
                        where to start:
                    </p>

                    <div class="grid sm:grid-cols-2 gap-4 my-8 not-prose">
                        <div
                            class="p-5 rounded-xl bg-white/[0.03] border border-white/10 hover:border-balantro-primary/30 transition-all">
                            <div class="text-2xl mb-3">🗂️</div>
                            <h4 class="font-display font-semibold text-white mb-2">
                                1. Separate Your Accounts
                            </h4>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Open a dedicated current account for the business. Never use
                                personal accounts for business transactions.
                            </p>
                        </div>
                        <div
                            class="p-5 rounded-xl bg-white/[0.03] border border-white/10 hover:border-balantro-primary/30 transition-all">
                            <div class="text-2xl mb-3">📅</div>
                            <h4 class="font-display font-semibold text-white mb-2">
                                2. Weekly Reconciliation
                            </h4>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Set a fixed time each week — even 30 minutes — to update
                                records and reconcile your bank statement.
                            </p>
                        </div>
                        <div
                            class="p-5 rounded-xl bg-white/[0.03] border border-white/10 hover:border-balantro-primary/30 transition-all">
                            <div class="text-2xl mb-3">🧾</div>
                            <h4 class="font-display font-semibold text-white mb-2">
                                3. Invoice Everything
                            </h4>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Every sale gets an invoice. Every purchase gets a bill. No
                                exceptions. This is the foundation of GST compliance.
                            </p>
                        </div>
                        <div
                            class="p-5 rounded-xl bg-white/[0.03] border border-white/10 hover:border-balantro-primary/30 transition-all">
                            <div class="text-2xl mb-3">📊</div>
                            <h4 class="font-display font-semibold text-white mb-2">
                                4. Monthly P&L Review
                            </h4>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Review a basic P&L every month. Know your gross margin, your
                                fixed costs, and your net position.
                            </p>
                        </div>
                    </div>

                    <h2 id="conclusion">The Bottom Line</h2>
                    <p>
                        Profits are a lagging indicator. Clean books are the leading
                        indicator that actually tells you where you're going. The
                        businesses we've seen grow consistently and sustainably are not
                        always the ones with the highest revenue — they're the ones where
                        the founder actually knows what's happening inside the numbers.
                    </p>
                    <p>Start with discipline. The growth will follow.</p> --}}

                    <!-- Tags -->
                    <div class="flex flex-wrap gap-2 mt-12 pt-8 border-t border-white/10">
                        <span class="text-slate-500 text-sm mr-2">Tags:</span>
                        <a href="insights.html"
                            class="px-3 py-1 rounded-full text-xs bg-white/5 border border-white/10 text-slate-400 hover:text-white hover:border-white/20 transition-all">Accounting</a>
                        <a href="insights.html"
                            class="px-3 py-1 rounded-full text-xs bg-white/5 border border-white/10 text-slate-400 hover:text-white hover:border-white/20 transition-all">Financial
                            Discipline</a>
                        <a href="insights.html"
                            class="px-3 py-1 rounded-full text-xs bg-white/5 border border-white/10 text-slate-400 hover:text-white hover:border-white/20 transition-all">MSMEs</a>
                        <a href="insights.html"
                            class="px-3 py-1 rounded-full text-xs bg-white/5 border border-white/10 text-slate-400 hover:text-white hover:border-white/20 transition-all">Early
                            Growth</a>
                    </div>

                    <!-- Author Box -->
                    <div class="mt-8 p-6 rounded-2xl bg-white/[0.03] border border-white/10 flex gap-5 items-start"
                        data-aos="fade-up">
                        <div
                            class="w-14 h-14 rounded-full bg-gradient-to-br from-balantro-secondary to-balantro-primary flex items-center justify-center font-display font-bold text-balantro-navy text-xl flex-shrink-0">
                            B
                        </div>
                        <div>
                            <p class="font-display font-bold text-white text-base mb-1">
                                Balantro Editorial Team
                            </p>
                            <p class="text-xs text-slate-500 uppercase tracking-widest mb-3">
                                Accounting & Compliance Experts · India
                            </p>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                Our editorial team comprises practising accountants,
                                compliance specialists, and business advisors with years of
                                hands-on experience working with Indian MSMEs. All content is
                                written from practice — not theory.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- SIDEBAR (desktop only) -->
                <aside class="hidden lg:block w-72 flex-shrink-0 sticky top-32 space-y-6">
                    <!-- TOC -->
                    <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-md"
                        data-aos="fade-left">
                        <h3
                            class="font-display font-bold text-white text-sm uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-balantro-secondary" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h8" />
                            </svg>
                            Table of Contents
                        </h3>
                        <nav class="space-y-1">
                            <a href="#the-real-problem"
                                class="toc-link block text-slate-400 text-sm py-1.5 border-l border-white/10 pl-3 hover:text-balantro-secondary">The
                                Real Problem</a>
                            <a href="#what-clean-books-mean"
                                class="toc-link block text-slate-400 text-sm py-1.5 border-l border-white/10 pl-3 hover:text-balantro-secondary">What
                                Clean Books Mean</a>
                            <a href="#why-early-stage"
                                class="toc-link block text-slate-400 text-sm py-1.5 border-l border-white/10 pl-3 hover:text-balantro-secondary">Why
                                Early-Stage Matters</a>
                            <a href="#profit-vs-discipline"
                                class="toc-link block text-slate-400 text-sm py-1.5 border-l border-white/10 pl-3 hover:text-balantro-secondary">Profit
                                vs. Discipline</a>
                            <a href="#practical-steps"
                                class="toc-link block text-slate-400 text-sm py-1.5 border-l border-white/10 pl-3 hover:text-balantro-secondary">Practical
                                Steps</a>
                            <a href="#conclusion"
                                class="toc-link block text-slate-400 text-sm py-1.5 border-l border-white/10 pl-3 hover:text-balantro-secondary">The
                                Bottom Line</a>
                        </nav>
                    </div>

                    <!-- Quick Stats -->
                    <div class="p-6 rounded-2xl bg-gradient-to-br from-balantro-primary/10 to-transparent border border-balantro-primary/20"
                        data-aos="fade-left" data-aos-delay="100">
                        <h3 class="font-display font-bold text-white text-sm uppercase tracking-widest mb-4">
                            Article Stats
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 text-xs">Reading Time</span>
                                <span class="text-balantro-secondary font-semibold text-sm">5 min</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 text-xs">Category</span>
                                <span class="text-white font-semibold text-sm">Accounting</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 text-xs">Published</span>
                                <span class="text-white font-semibold text-sm">Mar 2026</span>
                            </div>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/10 text-center" data-aos="fade-left"
                        data-aos-delay="150">
                        <p class="text-slate-400 text-sm mb-4 leading-relaxed">
                            Want clarity on your own business finances?
                        </p>
                        <a href="#"
                            class="block w-full px-5 py-3 rounded-full bg-gradient-to-r from-balantro-secondary to-balantro-primary text-balantro-navy font-bold text-sm hover:brightness-110 hover:shadow-[0_0_20px_rgba(34,211,238,0.4)] transition-all">Talk
                            to Our Team</a>
                        <p class="text-slate-600 text-xs mt-3">
                            No pressure. Just perspective.
                        </p>
                    </div>
                </aside>
            </div>

            <!-- RELATED ARTICLES -->
            @if ($relatedBlogs->count() > 0)
                <!-- RELATED ARTICLES -->
                <div class="mt-24" data-aos="fade-up">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="font-display font-bold text-white text-2xl">
                            More Insights
                        </h2>
                        <a href="{{ route('insights', ['category' => $blog->category->slugname ?? '']) }}"
                            class="text-balantro-primary text-sm font-medium hover:text-balantro-secondary transition-colors flex items-center gap-1">
                            View All
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($relatedBlogs as $relatedBlog)
                            <a href="{{ route('insight.detail', $relatedBlog->slugname) }}"
                                class="related-card group flex flex-col rounded-2xl bg-white/[0.03] border border-white/10 overflow-hidden transition-all duration-300">
                                <div class="aspect-video bg-[#0a0f1c] relative overflow-hidden">
                                    @if (!empty($relatedBlog->image))
                                        <img src="{{ asset('uploads/Blog/' . $relatedBlog->image) }}"
                                            alt="{{ $relatedBlog->title }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="absolute inset-0 bg-gradient-to-br from-[#34d399]/20 to-transparent">
                                        </div>
                                        <div class="absolute inset-0 flex items-center justify-center text-3xl">
                                            📋
                                        </div>
                                    @endif
                                </div>

                                <div class="p-6 flex flex-col flex-grow">
                                    <div
                                        class="text-[11px] font-bold tracking-widest uppercase text-slate-500 mb-2 flex items-center gap-2">
                                        <span>{{ $relatedBlog->category->name ?? 'Insight' }}</span>
                                    </div>

                                    <h3
                                        class="text-base font-display font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors leading-snug">
                                        {{ $relatedBlog->title }}
                                    </h3>

                                    <p class="text-slate-400 text-sm flex-grow">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($relatedBlog->description), 80) }}
                                    </p>

                                    <div
                                        class="font-medium text-balantro-primary flex items-center group-hover:translate-x-1 transition-transform text-sm mt-4">
                                        Read Article
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            {{-- <div class="mt-24" data-aos="fade-up">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="font-display font-bold text-white text-2xl">
                        More Insights
                    </h2>
                    <a href="insights.html"
                        class="text-balantro-primary text-sm font-medium hover:text-balantro-secondary transition-colors flex items-center gap-1">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="insight-detail.html"
                        class="related-card group flex flex-col rounded-2xl bg-white/[0.03] border border-white/10 overflow-hidden transition-all duration-300">
                        <div class="aspect-video bg-[#0a0f1c] relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#34d399]/20 to-transparent"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-3xl">
                                📋
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <div
                                class="text-[11px] font-bold tracking-widest uppercase text-slate-500 mb-2 flex items-center gap-2">
                                <span>Compliance</span><span class="w-1 h-1 rounded-full bg-slate-500"></span><span>4 min
                                    read</span>
                            </div>
                            <h3
                                class="text-base font-display font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors leading-snug">
                                The Hidden Cost of Delayed GST Returns
                            </h3>
                            <p class="text-slate-400 text-sm flex-grow">
                                Why waiting until the last minute is breaking your working
                                capital.
                            </p>
                            <div
                                class="font-medium text-balantro-primary flex items-center group-hover:translate-x-1 transition-transform text-sm mt-4">
                                Read Article
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </div>
                        </div>
                    </a>

                    <a href="insight-detail.html"
                        class="related-card group flex flex-col rounded-2xl bg-white/[0.03] border border-white/10 overflow-hidden transition-all duration-300">
                        <div class="aspect-video bg-[#0a0f1c] relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#fbbf24]/20 to-transparent"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-3xl">
                                ⚙️
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <div
                                class="text-[11px] font-bold tracking-widest uppercase text-slate-500 mb-2 flex items-center gap-2">
                                <span>Systems</span><span class="w-1 h-1 rounded-full bg-slate-500"></span><span>6 min
                                    read</span>
                            </div>
                            <h3
                                class="text-base font-display font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors leading-snug">
                                Process Over People: Designing The Backend
                            </h3>
                            <p class="text-slate-400 text-sm flex-grow">
                                How resilient businesses structure their operations beyond
                                individual dependencies.
                            </p>
                            <div
                                class="font-medium text-balantro-primary flex items-center group-hover:translate-x-1 transition-transform text-sm mt-4">
                                Read Article
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </div>
                        </div>
                    </a>

                    <a href="insight-detail.html"
                        class="related-card group flex flex-col rounded-2xl bg-white/[0.03] border border-white/10 overflow-hidden transition-all duration-300">
                        <div class="aspect-video bg-[#0a0f1c] relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-balantro-primary/20 to-transparent"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-3xl">
                                💼
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <div
                                class="text-[11px] font-bold tracking-widest uppercase text-slate-500 mb-2 flex items-center gap-2">
                                <span>MSMEs</span><span class="w-1 h-1 rounded-full bg-slate-500"></span><span>7 min
                                    read</span>
                            </div>
                            <h3
                                class="text-base font-display font-bold text-white mb-2 group-hover:text-balantro-secondary transition-colors leading-snug">
                                5 Financial Mistakes That Kill Growing Businesses
                            </h3>
                            <p class="text-slate-400 text-sm flex-grow">
                                Common but avoidable errors that set back even profitable
                                companies.
                            </p>
                            <div
                                class="font-medium text-balantro-primary flex items-center group-hover:translate-x-1 transition-transform text-sm mt-4">
                                Read Article
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>
            </div> --}}

            <!-- CTA STRIP -->
            <div class="inner-section-vh mt-20 p-10 rounded-3xl bg-gradient-to-br from-balantro-primary/10 via-white/[0.02] to-balantro-secondary/5 border border-white/10 text-center"
                data-aos="fade-up">
                <h2 class="font-display font-bold text-white text-2xl md:text-4xl mb-4">
                    Want Clarity on Your Own Business?
                </h2>
                <p class="text-slate-400 mb-8 max-w-xl mx-auto">
                    Talk to our team — no pressure, just perspective. We help Indian
                    businesses get their financial backend in order.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#"
                        class="px-10 py-4 rounded-full bg-white text-balantro-navy font-bold text-base hover:bg-slate-200 hover:scale-105 transition-all shadow-[0_0_30px_rgba(255,255,255,0.15)]">Talk
                        to Our Team</a>
                    <a href="insights.html"
                        class="px-10 py-4 rounded-full text-white font-medium text-base flex items-center justify-center gap-2 border border-white/20 hover:bg-white/10 transition-all">←
                        Back to Insights</a>
                </div>
            </div>
        </div>
    </section>

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

    <!-- Back to Top -->
    <button id="back-to-top" onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-8 right-8 z-50 w-12 h-12 rounded-full bg-balantro-primary/20 border border-balantro-primary/40 text-balantro-secondary flex items-center justify-center hover:bg-balantro-primary/40 transition-all shadow-[0_0_20px_rgba(34,211,238,0.3)]"
        aria-label="Back to top">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (window.AOS) {
                AOS.init({
                    duration: 800,
                    once: true,
                    offset: 80,
                    easing: "ease-out-cubic",
                });
            }

            const btn = document.getElementById("mobile-menu-btn");
            const menu = document.getElementById("mobile-menu");
            const iconBars = document.getElementById("menu-icon-bars");
            const iconClose = document.getElementById("menu-icon-close");
            if (btn && menu) {
                btn.addEventListener("click", () => {
                    menu.classList.toggle("hidden");
                    iconBars?.classList.toggle("hidden");
                    iconClose?.classList.toggle("hidden");
                });
            }

            const bar = document.getElementById("reading-progress");
            const btt = document.getElementById("back-to-top");
            if (bar) {
                window.addEventListener("scroll", () => {
                    const scrollTop = window.scrollY;
                    const docHeight = document.body.scrollHeight - window.innerHeight;
                    bar.style.width = docHeight > 0 ? (scrollTop / docHeight) * 100 + "%" : "0%";

                    if (btt) {
                        if (scrollTop > 400) btt.classList.add("visible");
                        else btt.classList.remove("visible");
                    }
                });
            }

            const sections = document.querySelectorAll("article h2[id], article h3[id]");
            const tocLinks = document.querySelectorAll(".toc-link");
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        tocLinks.forEach((link) => link.classList.remove("active"));
                        const active = document.querySelector(`.toc-link[href="#${entry.target.id}"]`);
                        if (active) active.classList.add("active");
                    }
                });
            }, {
                rootMargin: "-20% 0px -70% 0px",
            });
            sections.forEach((section) => observer.observe(section));

            class InsightDetailParticleNetwork {
                constructor(canvasId) {
                    this.canvas = document.getElementById(canvasId);
                    if (!this.canvas) return;
                    this.ctx = this.canvas.getContext("2d");
                    this.particles = [];
                    this.resizeReset = this.resizeReset.bind(this);
                    this.animationLoop = this.animationLoop.bind(this);
                    this.resizeReset();
                    for (let i = 0; i < 40; i++) this.particles.push(this.newParticle());
                    window.addEventListener("resize", this.resizeReset);
                    requestAnimationFrame(this.animationLoop);
                }

                resizeReset() {
                    this.w = this.canvas.width = this.canvas.offsetWidth;
                    this.h = this.canvas.height = this.canvas.offsetHeight;
                }

                newParticle() {
                    return {
                        x: Math.random() * this.w,
                        y: Math.random() * this.h,
                        vx: (Math.random() - 0.5) * 0.6,
                        vy: (Math.random() - 0.5) * 0.6,
                        r: Math.random() * 1.5 + 0.5,
                    };
                }

                animationLoop() {
                    this.ctx.clearRect(0, 0, this.w, this.h);
                    this.particles.forEach((particle) => {
                        particle.x += particle.vx;
                        particle.y += particle.vy;
                        if (particle.x < 0 || particle.x > this.w) particle.vx *= -1;
                        if (particle.y < 0 || particle.y > this.h) particle.vy *= -1;
                        this.ctx.beginPath();
                        this.ctx.arc(particle.x, particle.y, particle.r, 0, Math.PI * 2);
                        this.ctx.fillStyle = "rgba(14,165,233,0.4)";
                        this.ctx.fill();
                    });
                    requestAnimationFrame(this.animationLoop);
                }
            }

            new InsightDetailParticleNetwork("canvas-footer");
        });
    </script>
    <script src="{{ asset('js/magic-button.js') }}"></script>
@endsection
