@extends('layouts.superadmin')

@section('title', $pharmacy->name)

@section('content')
    <a href="{{ route('superadmin.pharmacies.index') }}" class="text-sm font-medium text-primary hover:underline">&larr; Back to pharmacies</a>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Profile --}}
        <div class="card card-pad">
            <div class="flex items-center justify-between">
                <h2 class="section-title">Profile</h2>
                @if($pharmacy->trashed())
                    <span class="badge badge-danger">Archived</span>
                @else
                    <span class="badge {{ $pharmacy->isActive() ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($pharmacy->status) }}</span>
                @endif
            </div>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-on-surface-variant">Owner</dt><dd>{{ $pharmacy->owner_name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Email</dt><dd>{{ $pharmacy->email ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Phone</dt><dd>{{ $pharmacy->phone ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">GST</dt><dd>{{ $pharmacy->gst ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Drug License</dt><dd>{{ $pharmacy->drug_license_number ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Joined</dt><dd>{{ $pharmacy->created_at->format('d M Y') }}</dd></div>
            </dl>
            @if($pharmacy->trashed())
                <form method="POST" action="{{ route('superadmin.pharmacies.restore', $pharmacy->id) }}" class="mt-5"
                      onsubmit="return confirm('Restore this pharmacy?')">
                    @csrf @method('PATCH')
                    <button class="btn w-full bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25">Restore pharmacy</button>
                </form>
            @else
                <a href="{{ route('superadmin.pharmacies.edit', $pharmacy) }}" class="btn btn-primary mt-5 w-full">Edit profile</a>

                <form method="POST" action="{{ route('superadmin.pharmacies.toggle-status', $pharmacy) }}" class="mt-3"
                      onsubmit="return confirm('{{ $pharmacy->isActive() ? 'Suspend' : 'Reactivate' }} this pharmacy?')">
                    @csrf @method('PATCH')
                    <button class="btn w-full {{ $pharmacy->isActive() ? 'bg-error-container text-on-error-container hover:opacity-90' : 'bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25' }}">
                        {{ $pharmacy->isActive() ? 'Suspend pharmacy' : 'Reactivate pharmacy' }}
                    </button>
                </form>

                @if(auth()->user()->hasAdminCapability(\App\Support\AdminCapability::IMPERSONATE))
                    <form method="POST" action="{{ route('superadmin.pharmacies.impersonate', $pharmacy) }}" class="mt-3"
                          onsubmit="return confirm('Log in as this pharmacy to provide support?')">
                        @csrf
                        <button class="btn btn-outline w-full">Impersonate owner</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('superadmin.pharmacies.destroy', $pharmacy) }}" class="mt-3"
                      onsubmit="return confirm('Archive this pharmacy? Its users lose access until it is restored.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline w-full text-error">Archive pharmacy</button>
                </form>
            @endif
        </div>

        {{-- Subscription + users --}}
        <div class="space-y-4 lg:col-span-2">
            <div class="card card-pad">
                <h2 class="section-title mb-4">Current Subscription</h2>
                @if($sub = $pharmacy->currentSubscription)
                    <div class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-4">
                        <div><p class="text-on-surface-variant">Plan</p><p class="font-semibold">{{ $sub->plan?->name ?? '—' }}</p></div>
                        <div><p class="text-on-surface-variant">Status</p><p class="font-semibold">{{ ucfirst($sub->status) }}</p></div>
                        <div><p class="text-on-surface-variant">Cycle</p><p class="font-semibold">{{ ucfirst($sub->billing_cycle) }}</p></div>
                        <div><p class="text-on-surface-variant">Ends</p><p class="font-semibold">{{ optional($sub->currentPeriodEnd())->format('d M Y') ?? '—' }}</p></div>
                    </div>
                @else
                    <p class="text-sm text-on-surface-variant">No subscription on record.</p>
                @endif
            </div>

            <div class="card overflow-hidden">
                <div class="card-header">
                    <h2 class="section-title">Users ({{ $pharmacy->users->count() }})</h2>
                    <a href="{{ route('superadmin.users.index', ['pharmacy_id' => $pharmacy->id]) }}" class="text-sm font-semibold text-primary hover:underline">Manage all</a>
                </div>
                <table class="table-saas">
                    <thead>
                        <tr><th>Name</th><th>Email</th><th>Role</th><th class="text-right">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($pharmacy->users as $user)
                            <tr>
                                <td class="font-medium">{{ $user->name }}</td>
                                <td class="text-on-surface-variant">{{ $user->email }}</td>
                                <td>{{ ucfirst(str_replace('_',' ', $user->role?->value ?? $user->role)) }}</td>
                                <td class="text-right"><a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-xs btn-outline">Edit</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card overflow-hidden">
                <div class="card-header"><h2 class="section-title">Recent Invoices</h2></div>
                <table class="table-saas">
                    <thead>
                        <tr><th>Invoice</th><th>Date</th><th>Total</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($pharmacy->invoices as $invoice)
                            <tr>
                                <td class="font-mono-data">{{ $invoice->invoice_number }}</td>
                                <td class="text-on-surface-variant">{{ $invoice->created_at->format('d M Y') }}</td>
                                <td>₹{{ number_format($invoice->total, 2) }}</td>
                                <td>{{ ucfirst($invoice->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="empty-state">No invoices.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
