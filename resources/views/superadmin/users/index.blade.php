@extends('layouts.superadmin')

@section('title', 'Users')

@php
    use App\Enums\UserRole;

    // Badge colour per role, keyed by the enum value.
    $roleBadge = [
        UserRole::SUPER_ADMIN->value => 'bg-primary/10 text-primary',
        UserRole::ADMIN->value       => 'bg-secondary/10 text-secondary',
        UserRole::STAFF->value       => 'bg-tertiary-container/20 text-tertiary',
        UserRole::CLIENT->value      => 'bg-outline-variant/30 text-on-surface-variant',
    ];
@endphp

@section('content')
    {{-- Header + primary action --}}
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="section-title">All Users</h2>
            <p class="text-sm text-on-surface-variant">{{ number_format($totalUsers) }} accounts across every tenant.</p>
        </div>
        <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> New user
        </a>
    </div>

    {{-- Role stat strip --}}
    <div class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach($roles as $role)
            <a href="{{ route('superadmin.users.index', ['role' => $role->value]) }}"
               class="card card-pad transition hover:border-primary/40 {{ request('role') === $role->value ? 'border-primary/50 ring-1 ring-primary/20' : '' }}">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">{{ $role->label() }}</p>
                <p class="mt-1 text-2xl font-bold text-on-surface">{{ $roleCounts[$role->value] ?? 0 }}</p>
            </a>
        @endforeach
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone…" class="form-input min-w-[200px] flex-1">
                <select name="role" class="form-select w-auto">
                    <option value="">All roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-select w-auto">
                    <option value="">All statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <select name="pharmacy_id" class="form-select w-auto">
                    <option value="">All pharmacies</option>
                    @foreach($pharmacies as $pharmacy)
                        <option value="{{ $pharmacy->id }}" @selected((string) request('pharmacy_id') === (string) $pharmacy->id)>{{ $pharmacy->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm">Filter</button>
                @if(request()->hasAny(['q', 'role', 'status', 'pharmacy_id']))
                    <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline btn-sm">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Pharmacy</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php $roleValue = $user->role?->value ?? $user->role; @endphp
                        <tr>
                            <td>
                                <div class="font-medium text-on-surface">
                                    {{ $user->name }}
                                    @if($user->id === auth()->id())
                                        <span class="ml-1 rounded bg-primary/10 px-1.5 py-0.5 text-[10px] font-semibold text-primary">You</span>
                                    @endif
                                </div>
                                <div class="text-xs text-on-surface-variant">{{ $user->email }}</div>
                            </td>
                            <td>
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $roleBadge[$roleValue] ?? 'bg-outline-variant/30 text-on-surface-variant' }}">
                                    {{ $user->role instanceof UserRole ? $user->role->label() : ucfirst(str_replace('_', ' ', (string) $roleValue)) }}
                                </span>
                                @if($user->isSuperAdmin() && ! $user->isFullSuperAdmin())
                                    <span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700" title="{{ collect($user->admin_capabilities)->map(fn($c) => \App\Support\AdminCapability::label($c))->join(', ') }}">Restricted</span>
                                @endif
                            </td>
                            <td class="text-on-surface-variant">{{ $user->pharmacy?->name ?? '—' }}</td>
                            <td><span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($user->status) }}</span></td>
                            <td class="text-on-surface-variant">{{ $user->created_at?->format('d M Y') ?? '—' }}</td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-xs btn-outline">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('superadmin.users.toggle-status', $user) }}" class="inline"
                                              onsubmit="return confirm('{{ $user->status === 'active' ? 'Suspend' : 'Reactivate' }} {{ addslashes($user->name) }}?')">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs {{ $user->status === 'active' ? 'bg-error-container text-on-error-container hover:opacity-90' : 'bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25' }}">
                                                {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" class="inline"
                                              onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">No users found.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="card-footer">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
