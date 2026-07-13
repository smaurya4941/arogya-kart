@extends('layouts.app')

@section('title', 'Notifications')
@section('subtitle', 'Alerts for low stock, expiry and report downloads')

@section('actions')
    @if (auth()->user()->unreadNotifications->count() > 0)
        <form method="POST" action="{{ route('admin.notifications.readAll') }}" onsubmit="return confirm('Mark all as read?')">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm">Mark all read</button>
        </form>
    @endif
@endsection

@section('content')
<div class="page mx-auto max-w-3xl">
    <div class="space-y-3">
        @forelse ($notifications as $notification)
            @php
                $data    = $notification->data;
                $isRead  = $notification->read_at !== null;
                $type    = $data['type'] ?? 'info';

                $iconMap = [
                    'low_stock'    => 'warning',
                    'expiry_alert' => 'event_busy',
                    'report_ready' => 'download',
                ];
                $colorMap = [
                    'low_stock'    => 'text-amber-600 bg-amber-100',
                    'expiry_alert' => 'text-error bg-error-container/40',
                    'report_ready' => 'text-primary bg-primary/10',
                ];
                $icon = $iconMap[$type] ?? 'notifications';
                $colorClass = $colorMap[$type] ?? 'text-on-surface-variant bg-surface-container-high';
            @endphp

            <div @class([
                'flex items-start gap-3 rounded-xl border p-4 transition',
                'card' => $isRead,
                'border-primary/30 bg-primary/5 shadow-sm' => !$isRead,
            ])>
                <div @class(['icon-tile', $colorClass])>
                    <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <p @class(['text-sm', 'font-semibold text-on-surface' => !$isRead, 'text-on-surface-variant' => $isRead])>
                            {{ $data['message'] ?? 'No message.' }}
                        </p>
                        @if (!$isRead)
                            <span class="mt-1 h-2 w-2 flex-none rounded-full bg-primary"></span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-on-surface-variant">{{ $notification->created_at->diffForHumans() }}</p>

                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        @if (!empty($data['url']))
                            <a href="{{ $data['url'] }}" class="btn btn-outline btn-xs">
                                View <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </a>
                        @endif
                        @if (!$isRead)
                            <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="text-xs text-on-surface-variant underline-offset-2 transition hover:text-on-surface hover:underline">Mark as read</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="card flex flex-col items-center justify-center border-dashed py-16 text-center">
                <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-surface-container-high text-on-surface-variant">
                    <span class="material-symbols-outlined text-[28px]">notifications</span>
                </div>
                <p class="font-medium text-on-surface">You're all caught up</p>
                <p class="mt-1 text-sm text-on-surface-variant">No notifications yet. Low-stock and expiry alerts will appear here.</p>
            </div>
        @endforelse

        @if ($notifications->hasPages())
            <div class="pt-2">{{ $notifications->links() }}</div>
        @endif
    </div>
</div>
@endsection
