<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Platform Admin') | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 antialiased">
<div x-data="{ open: false }" class="min-h-screen flex">
    {{-- Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col w-64 bg-gray-900 text-gray-300 fixed inset-y-0">
        <div class="h-16 flex items-center px-6 border-b border-gray-800">
            <span class="text-white font-bold text-lg">{{ config('app.name') }}</span>
            <span class="ml-2 text-[10px] uppercase tracking-widest bg-blue-600 text-white px-2 py-0.5 rounded">Platform</span>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            @php
                $nav = [
                    ['superadmin.dashboard', 'Dashboard'],
                    ['superadmin.pharmacies.index', 'Pharmacies'],
                    ['superadmin.subscriptions.index', 'Subscriptions'],
                    ['superadmin.plans.index', 'Plans'],
                    ['superadmin.audit.index', 'Activity Log'],
                ];
            @endphp
            @foreach($nav as [$route, $label])
                <a href="{{ route($route) }}"
                   class="block px-3 py-2 rounded-lg transition {{ request()->routeIs(Str::before($route, '.index').'*') || request()->routeIs($route) ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="p-3 border-t border-gray-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-gray-800 hover:text-white transition">
                    Sign out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
            <h1 class="text-lg font-semibold text-gray-900">@yield('title', 'Platform Admin')</h1>
            <div class="text-sm text-gray-500">{{ auth()->user()->name }}</div>
        </header>

        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
