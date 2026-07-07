@props([
    'title' => 'Dashboard',
    'subtitle' => null,
])

@php
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/95 backdrop-blur">
    <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 text-slate-600 lg:hidden"
                @click="sidebarOpen = true"
                aria-label="Open sidebar"
            >
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4A1 1 0 013 5zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm1 4a1 1 0 100 2h12a1 1 0 100-2H4z" clip-rule="evenodd" />
                </svg>
            </button>

            <div class="min-w-0">
                <h1 class="truncate text-xl font-semibold text-slate-900">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="mt-1 truncate text-sm text-slate-500">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3 sm:gap-4">
            @if (trim($slot))
                <div class="hidden items-center gap-3 md:flex">
                    {{ $slot }}
                </div>
            @endif

            <div class="hidden text-right sm:block">
                <p class="text-sm font-semibold text-slate-800">{{ $user?->name }}</p>
                <p class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ $user?->role?->value ?? 'user' }}</p>
            </div>

            {{-- Notification Bell --}}
            @php $unread = auth()->user()?->unreadNotifications()->count() ?? 0; @endphp
            <a href="{{ route('admin.notifications.index') }}"
               class="relative inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 text-slate-600 transition hover:border-slate-300 hover:text-slate-900"
               aria-label="Notifications">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
                @if ($unread > 0)
                    <span class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">
                        {{ $unread > 9 ? '9+' : $unread }}
                    </span>
                @endif
            </a>

            <a
                href="{{ route('profile.edit') }}"
                class="hidden rounded-2xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:text-slate-900 sm:inline-flex"
            >
                Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="inline-flex rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    Logout
                </button>
            </form>
        </div>
    </div>

    @if (trim($slot))
        <div class="border-t border-slate-200 px-4 py-3 md:hidden">
            <div class="mx-auto flex w-full max-w-7xl items-center gap-3">
                {{ $slot }}
            </div>
        </div>
    @endif
</header>
