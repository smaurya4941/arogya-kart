@extends('layouts.superadmin')

@section('title', 'Dashboard')

@section('content')
    {{-- KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        @php
            $cards = [
                ['Total Pharmacies', $totalPharmacies, $activePharmacies.' active', 'text-blue-600'],
                ['Active Subscriptions', $activeSubs, $trialSubs.' on trial', 'text-green-600'],
                ['Revenue (This Month)', '₹'.number_format($monthlyRevenue, 2), 'paid invoices', 'text-purple-600'],
                ['Total Revenue', '₹'.number_format($totalRevenue, 2), 'all time', 'text-amber-600'],
            ];
        @endphp
        @foreach($cards as [$label, $value, $sub, $color])
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-bold {{ $color }}">{{ $value }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ $sub }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        {{-- Recent pharmacies --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-900">Recent Pharmacies</h2>
                <a href="{{ route('superadmin.pharmacies.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500 border-b border-gray-100">
                        <tr><th class="py-2 pr-4">Pharmacy</th><th class="py-2 pr-4">Plan</th><th class="py-2 pr-4">Status</th><th class="py-2 pr-4">Joined</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentPharmacies as $pharmacy)
                            <tr>
                                <td class="py-3 pr-4">
                                    <a href="{{ route('superadmin.pharmacies.show', $pharmacy) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $pharmacy->name }}</a>
                                    <div class="text-xs text-gray-400">{{ $pharmacy->email }}</div>
                                </td>
                                <td class="py-3 pr-4">{{ $pharmacy->currentSubscription?->plan?->name ?? '—' }}</td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $pharmacy->isActive() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($pharmacy->status) }}</span>
                                </td>
                                <td class="py-3 pr-4 text-gray-500">{{ $pharmacy->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-400">No pharmacies yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Plan distribution --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Plan Distribution</h2>
            <div class="space-y-3">
                @forelse($planDistribution as $row)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-700">{{ $row->plan?->name ?? 'Unknown' }}</span>
                        <span class="font-semibold text-gray-900">{{ $row->total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No active subscriptions.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
