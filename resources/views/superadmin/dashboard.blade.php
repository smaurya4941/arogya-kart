@extends('layouts.superadmin')

@section('title', 'Dashboard')

@section('content')
    {{-- KPI cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['Total Pharmacies', $totalPharmacies, $activePharmacies.' active', 'text-primary'],
                ['Active Subscriptions', $activeSubs, $trialSubs.' on trial', 'text-tertiary'],
            ];
            // Revenue figures are billing data — hide from admins without that capability.
            if (auth()->user()->hasAdminCapability(\App\Support\AdminCapability::BILLING)) {
                $cards[] = ['Revenue (This Month)', '₹'.number_format($monthlyRevenue, 2), 'paid invoices', 'text-secondary'];
                $cards[] = ['Total Revenue', '₹'.number_format($totalRevenue, 2), 'all time', 'text-amber-600'];
            }
        @endphp
        @foreach($cards as [$label, $value, $sub, $color])
            <div class="card card-pad">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold {{ $color }}">{{ $value }}</p>
                <p class="mt-1 text-xs text-on-surface-variant">{{ $sub }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Recent pharmacies --}}
        @if(auth()->user()->hasAdminCapability(\App\Support\AdminCapability::PHARMACIES))
        <div class="card overflow-hidden lg:col-span-2">
            <div class="card-header">
                <h2 class="section-title">Recent Pharmacies</h2>
                <a href="{{ route('superadmin.pharmacies.index') }}" class="text-sm font-semibold text-primary hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr><th>Pharmacy</th><th>Plan</th><th>Status</th><th>Joined</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentPharmacies as $pharmacy)
                            <tr>
                                <td>
                                    <a href="{{ route('superadmin.pharmacies.show', $pharmacy) }}" class="font-medium text-on-surface hover:text-primary">{{ $pharmacy->name }}</a>
                                    <div class="text-xs text-on-surface-variant">{{ $pharmacy->email }}</div>
                                </td>
                                <td>{{ $pharmacy->currentSubscription?->plan?->name ?? '—' }}</td>
                                <td><span class="badge {{ $pharmacy->isActive() ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($pharmacy->status) }}</span></td>
                                <td class="text-on-surface-variant">{{ $pharmacy->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="empty-state">No pharmacies yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Plan distribution --}}
        @if(auth()->user()->hasAdminCapability(\App\Support\AdminCapability::BILLING))
        <div class="card card-pad">
            <h2 class="section-title mb-4">Plan Distribution</h2>
            <div class="space-y-3">
                @forelse($planDistribution as $row)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-on-surface">{{ $row->plan?->name ?? 'Unknown' }}</span>
                        <span class="font-semibold text-on-surface">{{ $row->total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-on-surface-variant">No active subscriptions.</p>
                @endforelse
            </div>
        </div>
        @endif
    </div>
@endsection
