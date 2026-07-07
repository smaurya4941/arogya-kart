<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
    @auth
        <div x-data="{ sidebarOpen: false }" class="min-h-screen lg:flex">
            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-30 bg-slate-950/40 lg:hidden"
                @click="sidebarOpen = false"
                aria-hidden="true"
            ></div>

            <x-sidebar />

            <div class="flex min-h-screen flex-1 flex-col lg:pl-72">
                <x-navbar
                    :title="trim($__env->yieldContent('title')) ?: 'Dashboard'"
                    :subtitle="trim($__env->yieldContent('subtitle')) ?: null"
                >
                    @hasSection('actions')
                        @yield('actions')
                    @endif
                </x-navbar>

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <div class="mx-auto w-full max-w-7xl space-y-6">
                        <x-flash-message />

                        @hasSection('content')
                            @yield('content')
                        @else
                            {{ $slot ?? '' }}
                        @endif
                    </div>
                </main>
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
</body>
</html>
