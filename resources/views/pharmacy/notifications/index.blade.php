@extends('layouts.app')

@section('title', 'Notifications')
@section('subtitle', 'Alerts for low stock, expiry and report downloads')

@section('actions')
    @if (auth()->user()->unreadNotifications->count() > 0)
        <form method="POST" action="{{ route('admin.notifications.readAll') }}"
              onsubmit="return confirm('Mark all as read?')">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:border-slate-300 hover:text-slate-900 transition">
                Mark all read
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="space-y-4">
    @forelse ($notifications as $notification)
        @php
            $data    = $notification->data;
            $isRead  = $notification->read_at !== null;
            $icon    = $data['icon'] ?? 'bell';
            $type    = $data['type'] ?? 'info';

            $iconMap = [
                'warning'           => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z',
                'clock'             => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                'document-download' => 'M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3',
                'bell'              => 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0',
            ];
            $colorMap = [
                'low_stock'     => 'text-amber-600 bg-amber-50',
                'expiry_alert'  => 'text-rose-600 bg-rose-50',
                'report_ready'  => 'text-indigo-600 bg-indigo-50',
            ];

            $iconPath  = $iconMap[$icon] ?? $iconMap['bell'];
            $colorClass = $colorMap[$type] ?? 'text-slate-600 bg-slate-100';
        @endphp

        <div @class([
            'flex items-start gap-4 rounded-2xl border p-5 transition',
            'bg-white border-slate-200' => $isRead,
            'bg-indigo-50/60 border-indigo-200 shadow-sm' => !$isRead,
        ])>
            {{-- Icon --}}
            <div @class(['flex-none flex h-10 w-10 items-center justify-center rounded-xl', $colorClass])>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                </svg>
            </div>

            {{-- Body --}}
            <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-2">
                    <p @class(['text-sm font-medium', 'text-slate-900' => !$isRead, 'text-slate-600' => $isRead])>
                        {{ $data['message'] ?? 'No message.' }}
                    </p>
                    @if (!$isRead)
                        <span class="flex-none h-2.5 w-2.5 rounded-full bg-indigo-500 mt-1"></span>
                    @endif
                </div>
                <p class="mt-1 text-xs text-slate-400">
                    {{ $notification->created_at->diffForHumans() }}
                </p>

                {{-- Action buttons --}}
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    @if (!empty($data['url']))
                        <a href="{{ $data['url'] }}"
                           class="inline-flex items-center gap-1 rounded-lg bg-white border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 hover:border-slate-300 transition">
                            View
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                            </svg>
                        </a>
                    @endif

                    @if (!$isRead)
                        <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}">
                            @csrf
                            <button type="submit"
                                    class="text-xs text-slate-400 hover:text-slate-700 transition underline-offset-2 hover:underline">
                                Mark as read
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    @empty
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-white py-20 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-4">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
            </div>
            <p class="text-slate-500 font-medium">You're all caught up</p>
            <p class="text-slate-400 text-sm mt-1">No notifications yet. Low-stock and expiry alerts will appear here.</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if ($notifications->hasPages())
        <div class="pt-2">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
