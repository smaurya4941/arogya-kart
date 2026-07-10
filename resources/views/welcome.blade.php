<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PharmaFlow') }} | Enterprise Pharmacy SaaS</title>

    <!-- Fonts & Icons (Matching Dashboard) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <style>
        body { background-color: #f8f9ff; font-family: 'Geist', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .active-icon { font-variation-settings: 'FILL' 1; }
        .glass-nav {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        /* Decorative Background Elements */
        .bg-blob {
            position: absolute;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.5;
            animation: float 10s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
            100% { transform: translateY(0px) scale(1); }
        }

        /* Ripple effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        @keyframes ripple {
            to { transform: scale(4); opacity: 0; }
        }
        button, .btn-ripple { position: relative; overflow: hidden; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body-md text-on-surface antialiased relative min-h-screen flex flex-col overflow-x-hidden">

    <!-- Decorative Background Blobs -->
    <div class="bg-blob bg-primary/20 w-[600px] h-[600px] rounded-full top-[-200px] left-[-200px]"></div>
    <div class="bg-blob bg-secondary/20 w-[500px] h-[500px] rounded-full bottom-0 right-[-100px]" style="animation-delay: 2s;"></div>
    <div class="bg-blob bg-tertiary-fixed-dim/20 w-[400px] h-[400px] rounded-full top-[30%] left-[60%]" style="animation-delay: 5s;"></div>

    <!-- Navigation -->
    <header class="fixed top-0 w-full z-50 glass-nav bg-white/60 border-b border-outline-variant/20 transition-all duration-300">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-on-primary shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined text-title-lg" style="font-variation-settings: 'FILL' 1;">medical_services</span>
                    </div>
                    <a href="{{ url('/') }}" class="group">
                        <h1 class="font-headline-md text-headline-md font-bold text-primary leading-tight group-hover:opacity-80 transition-opacity">PharmaFlow</h1>
                        <p class="text-[10px] font-bold text-on-surface-variant/70 uppercase tracking-[0.2em]">Enterprise Suite</p>
                    </a>
                </div>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-label-md font-bold text-on-surface-variant hover:text-primary transition-colors tracking-wide">FEATURES</a>
                    <a href="#benefits" class="text-label-md font-bold text-on-surface-variant hover:text-primary transition-colors tracking-wide">BENEFITS</a>
                    <a href="#pricing" class="text-label-md font-bold text-on-surface-variant hover:text-primary transition-colors tracking-wide">PRICING</a>
                </nav>

                <!-- Auth Actions -->
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-ripple hidden sm:flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-label-md font-bold text-on-primary shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all">
                                Go to Dashboard
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-label-md font-bold text-on-surface hover:text-primary transition-colors px-2">
                                Sign In
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-ripple hidden sm:flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-label-md font-bold text-on-primary shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col justify-center pt-32 pb-16 lg:pt-48 lg:pb-24">
        <!-- Hero Section -->
        <section class="relative mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-surface-container border border-outline-variant/30 text-primary font-bold text-label-md mb-8 mx-auto shadow-sm">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                PharmaFlow v2.0 is now live
            </div>
            
            <h1 class="font-display-lg text-4xl sm:text-6xl lg:text-7xl font-bold text-on-surface tracking-tight mb-8 leading-[1.1]">
                Modernize Your <br class="hidden sm:block"/>
                <span class="text-primary relative inline-block">
                    Pharmacy Operations
                    <svg class="absolute w-full h-3 -bottom-1 left-0 text-primary-fixed-dim/50" viewBox="0 0 100 10" preserveAspectRatio="none">
                        <path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="4" fill="transparent"/>
                    </svg>
                </span>
            </h1>
            
            <p class="font-body-lg text-lg sm:text-xl text-on-surface-variant max-w-2xl mx-auto mb-12 leading-relaxed">
                An all-in-one enterprise suite designed specifically for retail pharmacies, clinics, and hospital dispensaries. Manage inventory, process billing, and gain actionable insights effortlessly.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="btn-ripple w-full sm:w-auto flex items-center justify-center gap-2 rounded-xl bg-primary px-8 py-4 text-body-md font-bold text-on-primary shadow-xl shadow-primary/20 hover:-translate-y-1 hover:shadow-2xl hover:shadow-primary/30 transition-all duration-300">
                    Start Your Free Trial
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>
                <a href="#features" class="btn-ripple w-full sm:w-auto flex items-center justify-center gap-2 rounded-xl bg-white border border-outline-variant/30 px-8 py-4 text-body-md font-bold text-on-surface shadow-sm hover:bg-surface-container-low transition-all duration-300">
                    <span class="material-symbols-outlined text-[20px]">play_circle</span>
                    See How It Works
                </a>
            </div>

            <!-- Dashboard Preview Image / Mockup -->
            <div class="mt-20 lg:mt-24 relative mx-auto max-w-5xl">
                <div class="rounded-2xl border border-outline-variant/30 bg-white/50 backdrop-blur-sm p-2 shadow-2xl overflow-hidden transform perspective-1000 rotate-x-2 hover:rotate-x-0 transition-transform duration-700 ease-out">
                    <div class="rounded-xl border border-outline-variant/20 bg-surface-container-low overflow-hidden aspect-video flex items-center justify-center relative group">
                        <div class="absolute inset-0 bg-gradient-to-tr from-primary/5 to-tertiary/5"></div>
                        <!-- Abstract Dashboard Mockup -->
                        <div class="w-full h-full p-4 sm:p-8 flex flex-col gap-4">
                            <div class="h-12 w-full flex justify-between items-center border-b border-outline-variant/20 pb-4">
                                <div class="w-32 h-6 bg-outline-variant/30 rounded-md"></div>
                                <div class="flex gap-2">
                                    <div class="w-8 h-8 rounded-full bg-outline-variant/30"></div>
                                    <div class="w-8 h-8 rounded-full bg-primary/20"></div>
                                </div>
                            </div>
                            <div class="flex gap-4 h-24">
                                <div class="flex-1 bg-white rounded-lg shadow-sm border border-outline-variant/20 p-4 flex flex-col justify-between">
                                    <div class="w-10 h-10 rounded-lg bg-primary/10"></div>
                                    <div class="w-1/2 h-4 bg-outline-variant/30 rounded"></div>
                                </div>
                                <div class="flex-1 bg-white rounded-lg shadow-sm border border-outline-variant/20 p-4 flex flex-col justify-between">
                                    <div class="w-10 h-10 rounded-lg bg-secondary/10"></div>
                                    <div class="w-1/2 h-4 bg-outline-variant/30 rounded"></div>
                                </div>
                                <div class="hidden sm:flex flex-1 bg-white rounded-lg shadow-sm border border-outline-variant/20 p-4 flex flex-col justify-between">
                                    <div class="w-10 h-10 rounded-lg bg-error/10"></div>
                                    <div class="w-1/2 h-4 bg-outline-variant/30 rounded"></div>
                                </div>
                            </div>
                            <div class="flex-1 flex gap-4">
                                <div class="flex-[2] bg-white rounded-lg shadow-sm border border-outline-variant/20"></div>
                                <div class="flex-[1] bg-white rounded-lg shadow-sm border border-outline-variant/20 hidden md:block"></div>
                            </div>
                        </div>
                        
                        <!-- Overlay Play Button -->
                        <div class="absolute inset-0 flex items-center justify-center bg-on-background/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button class="w-20 h-20 bg-white rounded-full flex items-center justify-center text-primary shadow-2xl transform scale-90 group-hover:scale-100 transition-transform duration-300">
                                <span class="material-symbols-outlined text-[40px] ml-2 active-icon">play_arrow</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="relative py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="font-headline-md text-primary font-bold tracking-wide uppercase text-label-md mb-2">Built for Scale</h2>
                    <p class="font-display-lg text-3xl sm:text-4xl font-bold text-on-surface">Everything you need to run your pharmacy.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white p-8 rounded-2xl border border-outline-variant/30 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="w-14 h-14 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[28px]">point_of_sale</span>
                        </div>
                        <h3 class="font-title-lg text-xl font-bold text-on-surface mb-3">Lightning Fast POS</h3>
                        <p class="text-on-surface-variant leading-relaxed">Execute transactions in seconds with barcode scanning, instant GST calculation, and digital receipts.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white p-8 rounded-2xl border border-outline-variant/30 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="w-14 h-14 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center mb-6 group-hover:bg-secondary group-hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[28px]">inventory_2</span>
                        </div>
                        <h3 class="font-title-lg text-xl font-bold text-on-surface mb-3">Smart Inventory</h3>
                        <p class="text-on-surface-variant leading-relaxed">Automated low-stock alerts and proactive expiration tracking so you never run out or waste medicine.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white p-8 rounded-2xl border border-outline-variant/30 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="w-14 h-14 rounded-xl bg-tertiary-container/10 text-tertiary flex items-center justify-center mb-6 group-hover:bg-tertiary group-hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[28px]">monitoring</span>
                        </div>
                        <h3 class="font-title-lg text-xl font-bold text-on-surface mb-3">Executive Analytics</h3>
                        <p class="text-on-surface-variant leading-relaxed">Gain deep insights into your margins, top-selling products, and staff performance in real-time.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-outline-variant/30 py-12 mt-auto">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3 opacity-80 hover:opacity-100 transition-opacity">
                <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-on-primary shadow-sm">
                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">medical_services</span>
                </div>
                <div>
                    <h1 class="font-bold text-primary leading-none text-body-md">PharmaFlow</h1>
                    <p class="text-[8px] font-bold text-on-surface-variant uppercase tracking-[0.2em]">Enterprise Suite</p>
                </div>
            </div>
            
            <p class="text-label-md text-on-surface-variant text-center md:text-left">
                &copy; {{ date('Y') }} PharmaFlow Inc. All rights reserved. Built with precision.
            </p>
            
            <div class="flex items-center gap-6">
                <a href="#" class="text-on-surface-variant hover:text-primary transition-colors"><span class="material-symbols-outlined">language</span></a>
                <a href="#" class="text-on-surface-variant hover:text-primary transition-colors"><span class="material-symbols-outlined">help</span></a>
            </div>
        </div>
    </footer>

    <script>
        // Micro-interactions
        document.querySelectorAll('.btn-ripple').forEach(elem => {
            elem.addEventListener('mousedown', function(e) {
                const rect = this.getBoundingClientRect();
                const ripple = document.createElement('div');
                ripple.classList.add('ripple');
                
                ripple.style.width = ripple.style.height = Math.max(rect.width, rect.height) + 'px';
                ripple.style.left = e.clientX - rect.left - rect.width/2 + 'px';
                ripple.style.top = e.clientY - rect.top - rect.height/2 + 'px';
                
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });
    </script>
</body>
</html>
