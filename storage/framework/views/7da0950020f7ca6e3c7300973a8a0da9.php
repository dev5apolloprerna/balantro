

<?php $__env->startSection('title', 'Privacy Policy'); ?>

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
    .policy-card {
        background: rgba(15, 23, 42, 0.42);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 24px 80px -44px rgba(14, 165, 233, 0.45);
    }

    .policy-card:hover {
        border-color: rgba(34, 211, 238, 0.28);
        background: rgba(15, 23, 42, 0.58);
    }

    .policy-card ul {
        margin-top: 1rem;
        display: grid;
        gap: 0.75rem;
    }

    .policy-card li {
        position: relative;
        padding-left: 1.6rem;
        color: rgb(203 213 225);
        line-height: 1.7;
    }

    .policy-card li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0.75rem;
        width: 0.45rem;
        height: 0.45rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #0EA5E9, #22D3EE);
        box-shadow: 0 0 18px rgba(34, 211, 238, 0.6);
    }
</style>

<!-- PAGE HERO SECTION -->
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
        <div class="absolute bottom-1/4 right-1/4 translate-x-1/4 translate-y-1/4 w-[600px] h-[600px] bg-balantro-secondary/15 rounded-full blur-[150px] pointer-events-none animate-pulse"
            style="animation-duration: 6s; animation-delay: 2s; z-index: 2"></div>
        <div
            class="absolute bottom-0 left-0 right-0 h-48 bg-gradient-to-t from-[#02040a] to-transparent z-10 pointer-events-none">
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 flex flex-col items-center text-center">
        <div
            class="inline-flex items-center gap-2 mb-6 animate-[fadeInUp_0.8s_ease-out_forwards] border border-white/10 bg-white/5 backdrop-blur-xl px-4 py-2 rounded-full shadow-[0_0_30px_rgba(34,211,238,0.15)] cursor-default">
            <span class="text-xs uppercase tracking-[0.2em] text-white font-medium">🔐 PRIVACY POLICY</span>
        </div>

        <h1
            class="font-display text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-6 leading-[1.05] text-white opacity-0 animate-[fadeInUp_0.8s_ease-out_0.2s_forwards] max-w-5xl mx-auto">
            Protecting Your Data <br class="hidden sm:block" />
            <span class="relative inline-block mt-2">
                <span
                    class="absolute -inset-2 bg-gradient-to-r from-balantro-primary via-[#a78bfa] to-balantro-secondary blur-2xl opacity-40"></span>
                <span
                    class="relative text-transparent bg-clip-text bg-gradient-to-r from-white via-blue-100 to-white drop-shadow-sm">With Discipline</span>
            </span>
        </h1>

        <p
            class="text-lg md:text-xl text-slate-400 max-w-3xl mx-auto leading-relaxed font-light opacity-0 animate-[fadeInUp_0.8s_ease-out_0.4s_forwards] mb-8 drop-shadow-md">
            Welcome to <span class="text-white font-medium">Balantro</span>. We value your privacy and are committed to
            protecting your personal and business information.
        </p>

        <div
            class="inline-flex items-center gap-3 rounded-full border border-balantro-primary/20 bg-balantro-primary/10 px-5 py-2 text-sm font-medium text-balantro-secondary opacity-0 animate-[fadeInUp_0.8s_ease-out_0.6s_forwards]">
            <span class="w-2 h-2 rounded-full bg-balantro-secondary animate-pulse"></span>
            Last Updated: July 2026
        </div>
    </div>
</section>

<!-- PRIVACY CONTENT -->
<div class="w-full relative z-10 bg-[#02040a]">
    <section class="inner-section-vh py-24 relative overflow-hidden bg-[#0a0f1c]">
        <div class="absolute w-full h-px top-0 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
        <div class="absolute -top-32 right-0 w-[420px] h-[420px] bg-balantro-primary/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-24 left-0 w-[360px] h-[360px] bg-balantro-secondary/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
            <div class="policy-card rounded-3xl p-8 md:p-10 transition-all duration-300" data-aos="fade-up">
                <p class="text-slate-300 text-lg leading-relaxed">
                    This Privacy Policy explains how we collect, use, store, and protect your information when you use
                    our platform and services. By using Balantro, you agree to the collection and use of information in
                    accordance with this Privacy Policy.
                </p>
            </div>

            <div class="policy-card rounded-3xl p-8 md:p-10 transition-all duration-300" data-aos="fade-up">
                <div class="text-sm font-bold tracking-[0.2em] uppercase text-balantro-primary mb-3">01</div>
                <h2 class="font-display text-3xl font-bold text-white mb-6">Information We Collect</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-xl font-display font-semibold text-white mb-3">Personal Information</h3>
                        <ul>
                            <li>Name</li>
                            <li>Email Address</li>
                            <li>Phone Number</li>
                            <li>Company Name</li>
                            <li>Business Address</li>
                            <li>Account Credentials</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-xl font-display font-semibold text-white mb-3">Financial & Accounting</h3>
                        <ul>
                            <li>Ledger Data</li>
                            <li>Transaction Records</li>
                            <li>GST Information</li>
                            <li>Sales and Purchase Data</li>
                            <li>Bank Statement Data</li>
                            <li>Tally Imported Data</li>
                            <li>Financial Reports and Analytics</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-xl font-display font-semibold text-white mb-3">Technical Information</h3>
                        <ul>
                            <li>IP Address</li>
                            <li>Browser Type</li>
                            <li>Device Information</li>
                            <li>Operating System</li>
                            <li>Usage Statistics</li>
                            <li>Login Activity</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="policy-card rounded-3xl p-8 transition-all duration-300" data-aos="fade-up">
                    <div class="text-sm font-bold tracking-[0.2em] uppercase text-balantro-primary mb-3">02</div>
                    <h2 class="font-display text-2xl font-bold text-white mb-4">How We Use Your Information</h2>
                    <ul>
                        <li>Provide accounting and business management services.</li>
                        <li>Generate financial reports and dashboards.</li>
                        <li>Import and process accounting data.</li>
                        <li>Improve system performance and user experience.</li>
                        <li>Provide customer support.</li>
                        <li>Maintain security and prevent unauthorized access.</li>
                        <li>Comply with legal and regulatory requirements.</li>
                    </ul>
                </div>

                <div class="policy-card rounded-3xl p-8 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-sm font-bold tracking-[0.2em] uppercase text-balantro-primary mb-3">03</div>
                    <h2 class="font-display text-2xl font-bold text-white mb-4">Data Security</h2>
                    <p class="text-slate-300 leading-relaxed">
                        We implement appropriate technical and organizational measures to protect your information from
                        unauthorized access, loss, misuse, alteration, or disclosure. However, no method of electronic
                        transmission or storage is completely secure.
                    </p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="policy-card rounded-3xl p-8 transition-all duration-300" data-aos="fade-up">
                    <div class="text-sm font-bold tracking-[0.2em] uppercase text-balantro-primary mb-3">04</div>
                    <h2 class="font-display text-2xl font-bold text-white mb-4">Data Sharing</h2>
                    <p class="text-slate-300 leading-relaxed mb-4">
                        We do not sell, rent, or trade your personal or business information. We may share information
                        with your consent, trusted service providers, legal authorities when required, or to protect our
                        rights and prevent fraud.
                    </p>
                </div>

                <div class="policy-card rounded-3xl p-8 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-sm font-bold tracking-[0.2em] uppercase text-balantro-primary mb-3">05</div>
                    <h2 class="font-display text-2xl font-bold text-white mb-4">Data Retention</h2>
                    <p class="text-slate-300 leading-relaxed">
                        We retain your information only for as long as necessary to provide services, meet legal
                        obligations, resolve disputes, and enforce agreements.
                    </p>
                </div>
            </div>

            <div class="policy-card rounded-3xl p-8 md:p-10 transition-all duration-300" data-aos="fade-up">
                <div class="text-sm font-bold tracking-[0.2em] uppercase text-balantro-primary mb-3">06</div>
                <h2 class="font-display text-3xl font-bold text-white mb-6">Cookies and Tracking Technologies</h2>
                <p class="text-slate-300 leading-relaxed mb-4">Balantro may use cookies and similar technologies to:</p>
                <ul class="md:grid-cols-2">
                    <li>Remember user preferences.</li>
                    <li>Improve website functionality.</li>
                    <li>Analyze platform usage.</li>
                    <li>Enhance security.</li>
                </ul>
                <p class="text-slate-400 leading-relaxed mt-6">
                    You may disable cookies through your browser settings; however, some features may not function
                    properly.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="policy-card rounded-3xl p-8 transition-all duration-300" data-aos="fade-up">
                    <div class="text-sm font-bold tracking-[0.2em] uppercase text-balantro-primary mb-3">07</div>
                    <h2 class="font-display text-2xl font-bold text-white mb-4">User Rights</h2>
                    <p class="text-slate-300 leading-relaxed mb-4">Depending on applicable laws, users may have the right to:</p>
                    <ul>
                        <li>Access their personal information.</li>
                        <li>Correct inaccurate information.</li>
                        <li>Request deletion of data.</li>
                        <li>Restrict processing.</li>
                        <li>Request data portability.</li>
                    </ul>
                </div>

                <div class="policy-card rounded-3xl p-8 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-sm font-bold tracking-[0.2em] uppercase text-balantro-primary mb-3">08</div>
                    <h2 class="font-display text-2xl font-bold text-white mb-4">Third-Party Services</h2>
                    <p class="text-slate-300 leading-relaxed mb-4">Balantro may integrate with third-party services including:</p>
                    <ul>
                        <li>Payment Gateways</li>
                        <li>Cloud Storage Providers</li>
                        <li>Accounting Software Integrations</li>
                        <li>Analytics Tools</li>
                    </ul>
                    <p class="text-slate-400 leading-relaxed mt-6">These third-party services operate under their own privacy policies.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="policy-card rounded-3xl p-8 transition-all duration-300" data-aos="fade-up">
                    <div class="text-sm font-bold tracking-[0.2em] uppercase text-balantro-primary mb-3">09</div>
                    <h2 class="font-display text-2xl font-bold text-white mb-4">Changes to This Policy</h2>
                    <p class="text-slate-300 leading-relaxed">
                        We may update this Privacy Policy periodically. Any changes will be posted on this page with an
                        updated revision date.
                    </p>
                </div>

                <div class="policy-card rounded-3xl p-8 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-sm font-bold tracking-[0.2em] uppercase text-balantro-primary mb-3">10</div>
                    <h2 class="font-display text-2xl font-bold text-white mb-4">Contact Us</h2>
                    <div class="space-y-3 text-slate-300 leading-relaxed">
                        <p><strong class="text-white">Balantro Support Team</strong></p>
                        <p>Email: <a href="mailto:support@balantro.com" class="text-balantro-secondary hover:text-white transition-colors">support@balantro.com</a></p>
                        <p>Website: <a href="https://balantro.com" class="text-balantro-secondary hover:text-white transition-colors">https://balantro.com</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.front', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/frontend/privacypolicy.blade.php ENDPATH**/ ?>