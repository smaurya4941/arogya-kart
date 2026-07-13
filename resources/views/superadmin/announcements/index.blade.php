@extends('layouts.superadmin')

@section('title', 'Announcements')

@php
    $levelBadge = ['info' => 'badge-success', 'warning' => 'badge-neutral', 'critical' => 'badge-danger'];
@endphp

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h2 class="section-title">Announcements</h2>
        <a href="{{ route('superadmin.announcements.create') }}" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> New announcement
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Severity</th>
                        <th>Window</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $a)
                        <tr>
                            <td>
                                <div class="font-medium text-on-surface">{{ $a->title }}</div>
                                <div class="max-w-md truncate text-xs text-on-surface-variant">{{ $a->body }}</div>
                            </td>
                            <td><span class="badge {{ $levelBadge[$a->level] ?? 'badge-neutral' }}">{{ ucfirst($a->level) }}</span></td>
                            <td class="text-xs text-on-surface-variant">
                                {{ optional($a->starts_at)->format('d M Y') ?? 'Now' }} &rarr; {{ optional($a->ends_at)->format('d M Y') ?? '∞' }}
                            </td>
                            <td><span class="badge {{ $a->is_active ? 'badge-success' : 'badge-neutral' }}">{{ $a->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('superadmin.announcements.edit', $a) }}" class="btn btn-xs btn-outline">Edit</a>
                                    <form method="POST" action="{{ route('superadmin.announcements.toggle', $a) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-xs bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25">{{ $a->is_active ? 'Deactivate' : 'Activate' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('superadmin.announcements.destroy', $a) }}" class="inline" onsubmit="return confirm('Delete this announcement?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state">No announcements yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($announcements->hasPages())
            <div class="card-footer">{{ $announcements->links() }}</div>
        @endif
    </div>
@endsection
