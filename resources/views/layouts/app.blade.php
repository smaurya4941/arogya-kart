<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $appName = config('app.name', 'ArogyaKart');
        $sectionTitle = trim($__env->yieldContent('title'));
        $pageTitle = $sectionTitle !== '' ? $sectionTitle.' | '.$appName : $appName;
    @endphp

    <title>{{ $pageTitle }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <style>
        body { background-color: #f8f9ff; }
        .glass-nav { backdrop-filter: blur(20px); }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .active-icon { font-variation-settings: 'FILL' 1; }
        
        /* Custom scrollbar for data density */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #bcc9c6; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #6d7a77; }
        
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
        button, a { position: relative; overflow: hidden; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body-md text-on-surface antialiased overflow-x-hidden">
    @auth
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col lg:flex-row">
            <!-- Mobile Sidebar Overlay -->
            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-on-background/40 lg:hidden"
                @click="sidebarOpen = false"
                aria-hidden="true"
            ></div>

            <x-sidebar />

            <div class="flex min-h-screen flex-1 flex-col lg:ml-[280px]">
                <x-navbar
                    :title="trim($__env->yieldContent('title')) ?: 'Dashboard'"
                    :subtitle="trim($__env->yieldContent('subtitle')) ?: null"
                >
                    @hasSection('actions')
                        @yield('actions')
                    @endif
                </x-navbar>

                @if(session()->has(\App\Http\Controllers\SuperAdmin\ImpersonationController::SESSION_KEY))
                    <div class="bg-indigo-600 text-white px-6 py-2.5 flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">visibility</span>
                            You are impersonating <strong>{{ auth()->user()->pharmacy?->name ?? auth()->user()->name }}</strong>. Actions you take affect this pharmacy.
                        </span>
                        <form method="POST" action="{{ route('impersonate.leave') }}">
                            @csrf
                            <button class="bg-white/20 hover:bg-white/30 rounded-lg px-3 py-1 font-medium transition">Return to platform admin</button>
                        </form>
                    </div>
                @endif

                <main class="flex-1 pb-24 lg:pb-12">
                    <x-flash-message />

                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </main>
                
                <!-- BottomNavBar (Mobile) -->
                <nav class="fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-2 bg-surface-container-lowest dark:bg-on-background border-t border-outline-variant/30 shadow-[0_-4px_10px_rgba(0,0,0,0.05)] lg:hidden">
                    <a class="flex flex-col items-center justify-center bg-primary-container text-on-primary-container rounded-xl p-2 active:bg-surface-variant transition-all" href="{{ route('pharmacy.dashboard') }}">
                        <span class="material-symbols-outlined active-icon">home</span>
                        <span class="font-label-md text-label-md">Home</span>
                    </a>
                    <a class="flex flex-col items-center justify-center text-on-surface-variant active:bg-surface-variant p-2 rounded-xl transition-all" href="#">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <span class="font-label-md text-label-md">POS</span>
                    </a>
                    <a class="flex flex-col items-center justify-center text-on-surface-variant active:bg-surface-variant p-2 rounded-xl transition-all" href="#">
                        <span class="material-symbols-outlined">inventory</span>
                        <span class="font-label-md text-label-md">Stock</span>
                    </a>
                    <a class="flex flex-col items-center justify-center text-on-surface-variant active:bg-surface-variant p-2 rounded-xl transition-all relative" href="#">
                        <span class="material-symbols-outlined">notifications_active</span>
                        <span class="font-label-md text-label-md">Alerts</span>
                        @if(auth()->user()?->unreadNotifications()->count() > 0)
                            <span class="absolute top-0 right-1 w-2 h-2 bg-error rounded-full border border-white"></span>
                        @endif
                    </a>
                </nav>
            </div>
        </div>
    @else
        <div class="min-h-screen">
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </div>
    @endauth

    <script>
        // Simple micro-interactions
        document.querySelectorAll('button, a').forEach(elem => {
            elem.addEventListener('click', function(e) {
                const ripple = document.createElement('div');
                ripple.classList.add('ripple');
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });
    </script>
</body>
</html>
