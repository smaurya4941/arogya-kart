@extends('layouts.superadmin')

@section('title', 'Pharmacies')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold text-on-surface">Pharmacies</h1>
        <a href="{{ route('superadmin.pharmacies.create') }}" class="btn btn-primary btn-sm">Onboard pharmacy</a>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, owner…" class="form-input min-w-[200px] flex-1">
                <select name="status" class="form-select w-auto">
                    <option value="">All statuses</option>
                    <option value="active" @selected(request('status')==='active')>Active</option>
                    <option value="suspended" @selected(request('status')==='suspended')>Suspended</option>
                </select>
                <select name="trashed" class="form-select w-auto">
                    <option value="">Active tenants</option>
                    <option value="with" @selected(request('trashed')==='with')>Include archived</option>
                    <option value="only" @selected(request('trashed')==='only')>Archived only</option>
                </select>
                <button class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Pharmacy</th>
                        <th>Owner</th>
                        <th>Plan</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pharmacies as $pharmacy)
                        <tr class="{{ $pharmacy->trashed() ? 'opacity-60' : '' }}">
                            <td>
                                <a href="{{ route('superadmin.pharmacies.show', $pharmacy) }}" class="font-medium text-on-surface hover:text-primary">{{ $pharmacy->name }}</a>
                                <div class="text-xs text-on-surface-variant">{{ $pharmacy->email }}</div>
                            </td>
                            <td class="text-on-surface-variant">{{ $pharmacy->owner_name }}</td>
                            <td>{{ $pharmacy->currentSubscription?->plan?->name ?? '—' }}</td>
                            <td class="text-on-surface-variant">{{ $pharmacy->users_count }}</td>
                            <td>
                                @if($pharmacy->trashed())
                                    <span class="badge badge-danger">Archived</span>
                                @else
                                    <span class="badge {{ $pharmacy->isActive() ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($pharmacy->status) }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($pharmacy->trashed())
                                        <form method="POST" action="{{ route('superadmin.pharmacies.restore', $pharmacy->id) }}" class="inline"
                                              onsubmit="return confirm('Restore this pharmacy?')">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25">Restore</button>
                                        </form>
                                    @else
                                        <a href="{{ route('superadmin.pharmacies.edit', $pharmacy) }}" class="btn btn-xs btn-outline">Edit</a>
                                        <form method="POST" action="{{ route('superadmin.pharmacies.toggle-status', $pharmacy) }}" class="inline"
                                              onsubmit="return confirm('{{ $pharmacy->isActive() ? 'Suspend' : 'Reactivate' }} this pharmacy?')">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs {{ $pharmacy->isActive() ? 'bg-error-container text-on-error-container hover:opacity-90' : 'bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25' }}">
                                                {{ $pharmacy->isActive() ? 'Suspend' : 'Reactivate' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('superadmin.pharmacies.destroy', $pharmacy) }}" class="inline"
                                              onsubmit="return confirm('Archive this pharmacy? Its users lose access until it is restored.')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-outline">Archive</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">No pharmacies found.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pharmacies->hasPages())
            <div class="card-footer">{{ $pharmacies->links() }}</div>
        @endif
    </div>
@endsection
