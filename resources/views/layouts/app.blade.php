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

            <div class="flex min-h-screen flex-1 flex-col lg:ml-[248px]">
                <x-navbar
                    :title="trim($__env->yieldContent('title')) ?: 'Dashboard'"
                    :subtitle="trim($__env->yieldContent('subtitle')) ?: null"
                >
                    @hasSection('actions')
                        @yield('actions')
                    @endif
                </x-navbar>

                @if(session()->has(\App\Http\Controllers\SuperAdmin\ImpersonationController::SESSION_KEY))
                    <div class="bg-inverse-surface text-inverse-on-surface px-6 py-2.5 flex items-center justify-between text-sm">
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

                @php
                    $liveAnnouncements = \App\Models\Announcement::cachedLive();
                    $announcementStyles = [
                        'info'     => 'bg-primary/10 text-primary',
                        'warning'  => 'bg-amber-100 text-amber-800',
                        'critical' => 'bg-error-container text-on-error-container',
                    ];
                    $announcementIcons = ['info' => 'campaign', 'warning' => 'warning', 'critical' => 'error'];
                @endphp
                @foreach($liveAnnouncements as $announcement)
                    <div class="px-6 py-2.5 text-sm {{ $announcementStyles[$announcement->level] ?? $announcementStyles['info'] }}">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">{{ $announcementIcons[$announcement->level] ?? 'campaign' }}</span>
                            <strong>{{ $announcement->title }}</strong>
                            <span class="opacity-90">{{ $announcement->body }}</span>
                        </span>
                    </div>
                @endforeach

                <main class="flex-1 px-4 py-5 pb-24 sm:px-6 lg:pb-8">
                    <x-flash-message />

                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </main>
                
                <!-- BottomNavBar (Mobile) — role-aware, only links to routes the user can reach -->
                @php
                    $navUser = auth()->user();
                    $bottomNav = [
                        ['label' => 'Home', 'icon' => 'home', 'url' => route('dashboard'), 'match' => ['dashboard', '*.dashboard']],
                    ];
                    if (\Illuminate\Support\Facades\Route::has('admin.pos.index') && $navUser?->can('create', \App\Models\Sale::class)) {
                        $bottomNav[] = ['label' => 'POS', 'icon' => 'point_of_sale', 'url' => route('admin.pos.index'), 'match' => ['admin.pos.*']];
                    }
                    if (\Illuminate\Support\Facades\Route::has('admin.products.index') && $navUser?->can('viewAny', \App\Models\Product::class)) {
                        $bottomNav[] = ['label' => 'Stock', 'icon' => 'inventory_2', 'url' => route('admin.products.index'), 'match' => ['admin.products.*']];
                    }
                    $navUnread = $navUser?->unreadNotifications()->count() ?? 0;
                    if (\Illuminate\Support\Facades\Route::has('admin.notifications.index')) {
                        $bottomNav[] = ['label' => 'Alerts', 'icon' => 'notifications', 'url' => route('admin.notifications.index'), 'match' => ['admin.notifications.*'], 'badge' => $navUnread];
                    }
                @endphp
                <nav class="fixed bottom-0 left-0 z-50 flex w-full items-center justify-around border-t border-outline-variant/40 bg-white/90 px-2 py-1.5 backdrop-blur-xl dark:bg-on-background lg:hidden">
                    @foreach($bottomNav as $item)
                        @php $active = call_user_func_array([request(), 'routeIs'], $item['match']); @endphp
                        <a href="{{ $item['url'] }}" class="relative flex flex-1 flex-col items-center justify-center gap-0.5 rounded-lg py-1.5 text-[10px] font-medium transition-colors {{ $active ? 'text-primary' : 'text-on-surface-variant' }}">
                            <span class="material-symbols-outlined text-[22px] {{ $active ? 'active-icon' : '' }}">{{ $item['icon'] }}</span>
                            <span>{{ $item['label'] }}</span>
                            @if(($item['badge'] ?? 0) > 0)
                                <span class="absolute right-1/4 top-1 h-2 w-2 rounded-full bg-error ring-2 ring-white"></span>
                            @endif
                        </a>
                    @endforeach
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
    @stack('scripts')
</body>
</html>
