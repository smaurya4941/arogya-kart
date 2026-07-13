<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Platform Admin') | {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400;vertical-align:middle;}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-surface antialiased">
<div x-data="{ open: false }" class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 hidden w-60 flex-col bg-on-background text-outline-variant lg:flex">
        <div class="flex h-14 items-center gap-2 border-b border-white/10 px-5">
            <span class="text-base font-bold text-white">{{ config('app.name') }}</span>
            <span class="rounded bg-primary px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-widest text-on-primary">Platform</span>
        </div>
        <nav class="flex-1 space-y-1 px-3 py-4 text-sm">
            @php
                // Each row: [route, label, icon, capability]. A null capability is
                // always shown; otherwise the item appears only if the current
                // super admin holds that capability.
                $nav = [
                    ['superadmin.dashboard', 'Dashboard', 'dashboard', null],
                    ['superadmin.pharmacies.index', 'Pharmacies', 'local_pharmacy', \App\Support\AdminCapability::PHARMACIES],
                    ['superadmin.operations.index', 'Operations', 'inventory_2', \App\Support\AdminCapability::OPERATIONS],
                    ['superadmin.users.index', 'Users', 'group', \App\Support\AdminCapability::USERS],
                    ['superadmin.subscriptions.index', 'Subscriptions', 'card_membership', \App\Support\AdminCapability::BILLING],
                    ['superadmin.invoices.index', 'Invoices', 'receipt_long', \App\Support\AdminCapability::BILLING],
                    ['superadmin.plans.index', 'Plans', 'workspace_premium', \App\Support\AdminCapability::BILLING],
                    ['superadmin.coupons.index', 'Coupons', 'sell', \App\Support\AdminCapability::BILLING],
                    ['superadmin.announcements.index', 'Announcements', 'campaign', \App\Support\AdminCapability::ANNOUNCEMENTS],
                    ['superadmin.audit.index', 'Activity Log', 'history', \App\Support\AdminCapability::AUDIT],
                    ['superadmin.system.index', 'System Health', 'monitor_heart', \App\Support\AdminCapability::SYSTEM],
                    ['superadmin.settings.index', 'Settings', 'settings', \App\Support\AdminCapability::SETTINGS],
                ];
            @endphp
            @foreach($nav as [$route, $label, $icon, $capability])
                @continue($capability !== null && ! auth()->user()->hasAdminCapability($capability))
                @php $active = request()->routeIs(Str::before($route, '.index').'*') || request()->routeIs($route); @endphp
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 font-medium transition {{ $active ? 'bg-white/10 text-white' : 'text-outline-variant hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
                    {{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="border-t border-white/10 p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-outline-variant transition hover:bg-white/5 hover:text-white">
                    <span class="material-symbols-outlined text-[20px]">logout</span> Sign out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex min-h-screen flex-1 flex-col lg:ml-60">
        <header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-outline-variant/40 bg-white/80 px-6 backdrop-blur-xl">
            <h1 class="text-base font-bold text-on-surface">@yield('title', 'Platform Admin')</h1>
            <div class="text-sm text-on-surface-variant">{{ auth()->user()->name }}</div>
        </header>

        <main class="flex-1 p-4 sm:p-6">
            @if(session('success'))
                <div class="mb-4 rounded-lg border border-tertiary/30 bg-tertiary-container/15 px-4 py-3 text-sm text-tertiary">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-lg border border-error/30 bg-error-container/40 px-4 py-3 text-sm text-on-error-container">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-lg border border-error/30 bg-error-container/40 px-4 py-3 text-sm text-on-error-container">
                    <ul class="list-inside list-disc">
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
