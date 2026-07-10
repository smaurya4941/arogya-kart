@props([
    'title' => 'Dashboard',
    'subtitle' => null,
])

@php
    $user = auth()->user();
@endphp

<!-- TopNavBar -->
<header class="sticky top-0 z-30 bg-white/70 dark:bg-on-background/70 backdrop-blur-xl border-b border-outline-variant/30 dark:border-outline/20 shadow-sm flex justify-between items-center h-16 px-4 sm:px-6">
    <div class="flex items-center gap-4 lg:gap-6">
        <!-- Mobile Menu Toggle -->
        <button
            type="button"
            class="p-2 text-on-surface-variant hover:bg-surface-variant/20 rounded-full lg:hidden"
            @click="sidebarOpen = true"
            aria-label="Open sidebar"
        >
            <span class="material-symbols-outlined">menu</span>
        </button>

        <div class="relative hidden md:block">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
            <input class="bg-surface-container-low border-none rounded-full pl-10 pr-4 py-2 text-body-md w-64 focus:ring-2 focus:ring-primary/50 transition-all" placeholder="Search orders, meds, patients..." type="text"/>
        </div>
        <nav class="hidden md:flex items-center gap-4 text-label-md font-label-md">
            @if(trim($slot))
                {{ $slot }}
            @endif
        </nav>
        
        <!-- Mobile Page Title -->
        <div class="md:hidden">
            <h1 class="truncate text-title-lg font-bold text-on-surface leading-tight">{{ $title }}</h1>
        </div>
    </div>
    
    <div class="flex items-center gap-2 sm:gap-4">
        @php $unread = auth()->user()?->unreadNotifications()->count() ?? 0; @endphp
        <a href="{{ route('admin.notifications.index') }}" class="p-2 text-on-surface-variant hover:bg-surface-variant/20 rounded-full relative inline-flex items-center justify-center">
            <span class="material-symbols-outlined">notifications</span>
            @if ($unread > 0)
                <span class="absolute top-2 right-2 flex h-2 w-2 items-center justify-center rounded-full bg-error text-[8px] font-bold text-on-error border-2 border-white shadow-sm">
                    {{ $unread > 9 ? '+' : '' }}
                </span>
            @endif
        </a>
        
        <button class="hidden sm:inline-flex p-2 text-on-surface-variant hover:bg-surface-variant/20 rounded-full">
            <span class="material-symbols-outlined">apps</span>
        </button>

        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-full border border-outline-variant/30 bg-surface-container-high p-1 pr-3 transition-colors hover:bg-surface-variant/30">
            <div class="w-7 h-7 rounded-full bg-primary flex items-center justify-center text-on-primary font-bold text-xs">
                {{ substr($user?->name ?? 'U', 0, 1) }}
            </div>
            <div class="hidden sm:flex flex-col text-left">
                <span class="text-label-md font-bold leading-none mb-0.5 text-on-surface">{{ $user?->name }}</span>
                <span class="text-[9px] uppercase tracking-wider text-on-surface-variant leading-none">{{ $user?->role?->value ?? 'user' }}</span>
            </div>
        </a>
    </div>
</header>
