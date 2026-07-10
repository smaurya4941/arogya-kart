@extends('layouts.app')

@section('title', 'Operations Dashboard')
@section('subtitle', now()->format('l, F j, Y'))

@section('content')
<div class="space-y-8">
    <!-- Dashboard Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-primary font-bold">Today</span>
                <span class="w-1.5 h-1.5 rounded-full bg-outline-variant"></span>
                <span class="text-on-surface-variant">{{ auth()->user()->pharmacy->name ?? 'City Wellness Pharmacy' }}</span>
            </div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Operations Dashboard</h2>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.pos.index') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-outline-variant/30 rounded-xl font-bold text-primary shadow-sm hover:bg-surface-container-low transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                New Bill
            </a>
            <a href="{{ route('admin.products.create') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-outline-variant/30 rounded-xl font-bold text-primary shadow-sm hover:bg-surface-container-low transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]">add_business</span>
                Add Inventory
            </a>
            <a href="{{ route('admin.purchases.create') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-on-primary rounded-xl font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]">payments</span>
                Purchase
            </a>
        </div>
    </div>

    <!-- Metric Cards Bento -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-widget-gap">
        <!-- Sales Card -->
        <div class="bg-white border border-outline-variant/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-primary/10 text-primary rounded-xl group-hover:bg-primary group-hover:text-on-primary transition-colors">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <span class="text-tertiary-container font-bold flex items-center gap-1 text-label-md">
                    <span class="material-symbols-outlined text-[16px]">trending_up</span>
                    {{ $todayInvoices ?? 0 }} Orders
                </span>
            </div>
            <p class="text-on-surface-variant font-label-md uppercase tracking-wider mb-1">Today's Sales</p>
            <h3 class="text-headline-md font-display-lg font-bold text-on-surface">${{ number_format($todayRevenue ?? 0, 2) }}</h3>
        </div>

        <!-- Low Stock Card -->
        <div class="bg-white border border-outline-variant/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-secondary/10 text-secondary rounded-xl group-hover:bg-secondary group-hover:text-on-primary transition-colors">
                    <span class="material-symbols-outlined">inventory</span>
                </div>
                @if(($lowStockCount ?? 0) > 0)
                    <span class="text-error font-bold flex items-center gap-1 text-label-md">Critical</span>
                @else
                    <span class="text-tertiary font-bold flex items-center gap-1 text-label-md">Optimal</span>
                @endif
            </div>
            <p class="text-on-surface-variant font-label-md uppercase tracking-wider mb-1">Low Stock Items</p>
            <h3 class="text-headline-md font-display-lg font-bold text-on-surface">{{ str_pad($lowStockCount ?? 0, 2, '0', STR_PAD_LEFT) }}</h3>
        </div>

        <!-- Expiring Soon Card -->
        <div class="bg-white border border-outline-variant/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-error-container/20 text-error rounded-xl group-hover:bg-error group-hover:text-on-primary transition-colors">
                    <span class="material-symbols-outlined">event_busy</span>
                </div>
                <span class="text-on-surface-variant font-bold text-label-md">90 Days</span>
            </div>
            <p class="text-on-surface-variant font-label-md uppercase tracking-wider mb-1">Expiring Soon</p>
            <h3 class="text-headline-md font-display-lg font-bold text-on-surface">{{ str_pad($expiringCount ?? 0, 2, '0', STR_PAD_LEFT) }}</h3>
        </div>

        <!-- Total Medicines Card -->
        <div class="bg-white border border-outline-variant/30 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-tertiary/10 text-tertiary rounded-xl group-hover:bg-tertiary group-hover:text-on-primary transition-colors">
                    <span class="material-symbols-outlined">medication</span>
                </div>
                <span class="text-on-surface-variant font-bold text-label-md">Active: {{ $activeMedicines ?? 0 }}</span>
            </div>
            <p class="text-on-surface-variant font-label-md uppercase tracking-wider mb-1">Total Medicines</p>
            <h3 class="text-headline-md font-display-lg font-bold text-on-surface">{{ str_pad($totalMedicinesCount ?? 0, 2, '0', STR_PAD_LEFT) }}</h3>
        </div>
    </div>

    <!-- Main Dashboard Grid -->
    <div class="grid grid-cols-12 gap-widget-gap">
        <!-- Sales Overview Chart (Column 8) -->
        <div class="col-span-12 lg:col-span-8 bg-white border border-outline-variant/30 rounded-2xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h4 class="font-title-lg text-title-lg text-on-surface">Sales Overview</h4>
                    <p class="text-label-md text-on-surface-variant mt-1">Simulated weekly distribution</p>
                </div>
                <div class="flex gap-2 bg-surface-container-low p-1 rounded-lg">
                    <button class="px-3 py-1 text-label-md rounded-md bg-white shadow-sm font-bold text-primary">Last 7 Days</button>
                    <button class="px-3 py-1 text-label-md rounded-md text-on-surface-variant hover:bg-white/50 transition-colors">Month</button>
                </div>
            </div>
            <div class="h-64 flex items-end justify-between gap-2 sm:gap-4 px-0 sm:px-2">
                <!-- Simulated Bar Chart -->
                @php $heights = [60, 45, 75, 90, 55, 85, 100]; $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']; @endphp
                @foreach($heights as $i => $h)
                <div class="flex-1 flex flex-col items-center gap-3">
                    <div class="w-full bg-primary/20 rounded-t-lg relative group overflow-hidden" style="height: {{ $h }}%">
                        <div class="absolute bottom-0 w-full bg-primary group-hover:h-full transition-all duration-500" style="height: 80%"></div>
                    </div>
                    <span class="text-[10px] sm:text-label-md font-label-md text-on-surface-variant">{{ $days[$i] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Critical Inventory Alerts (Column 4) -->
        <div class="col-span-12 lg:col-span-4 bg-white border border-outline-variant/30 rounded-2xl p-6 shadow-sm overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h4 class="font-title-lg text-title-lg text-on-surface">Critical Inventory</h4>
                @if(count($lowStockMedicines ?? []) > 0 || count($expiringMedicines ?? []) > 0)
                    <span class="px-2 py-0.5 bg-error-container text-on-error-container text-[10px] font-black rounded uppercase">Urgent</span>
                @else
                    <span class="px-2 py-0.5 bg-tertiary-container/20 text-tertiary text-[10px] font-black rounded uppercase">Clear</span>
                @endif
            </div>
            <div class="space-y-4 overflow-y-auto pr-1 flex-1">
                @forelse(collect($expiringMedicines ?? [])->take(2) as $batch)
                    <div class="flex items-center gap-4 p-3 bg-surface-container-low rounded-xl border border-outline-variant/10">
                        <div class="w-10 h-10 rounded-lg bg-error-container/20 flex items-center justify-center text-error flex-shrink-0">
                            <span class="material-symbols-outlined">event_busy</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-body-md font-bold text-on-surface truncate">{{ $batch->product->name }}</p>
                            <p class="text-label-md text-on-surface-variant">Expiring {{ \Carbon\Carbon::parse($batch->expiry_date)->diffForHumans() }}</p>
                        </div>
                        <span class="text-error font-mono-data">{{ $batch->quantity }}</span>
                    </div>
                @empty
                @endforelse

                @forelse(collect($lowStockMedicines ?? [])->take(3) as $med)
                    <div class="flex items-center gap-4 p-3 bg-surface-container-low rounded-xl border border-outline-variant/10">
                        <div class="w-10 h-10 rounded-lg bg-tertiary-container/10 flex items-center justify-center text-tertiary flex-shrink-0">
                            <span class="material-symbols-outlined">warning</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-body-md font-bold text-on-surface truncate">{{ $med->name }}</p>
                            <p class="text-label-md text-on-surface-variant">Below reorder level</p>
                        </div>
                        <span class="text-secondary font-mono-data">{{ $med->total_stock }}</span>
                    </div>
                @empty
                    @if(count($expiringMedicines ?? []) === 0)
                        <div class="flex flex-col items-center justify-center h-full text-outline-variant py-8">
                            <span class="material-symbols-outlined text-[48px] mb-2 opacity-50">check_circle</span>
                            <p class="text-body-md font-medium text-center">No critical inventory alerts.</p>
                        </div>
                    @endif
                @endforelse
            </div>
            <a href="{{ route('admin.products.index') }}" class="block w-full mt-6 py-3 text-primary font-bold text-body-md text-center bg-primary/5 hover:bg-primary/10 rounded-xl transition-colors">
                View Inventory
            </a>
        </div>

        <!-- Recent Invoices Table (Full Width) -->
        <div class="col-span-12 bg-white border border-outline-variant/30 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-outline-variant/20 flex justify-between items-center bg-surface-container-lowest">
                <h4 class="font-title-lg text-title-lg text-on-surface">Recent Sales</h4>
                <a href="{{ route('admin.sales.index') }}" class="text-primary font-bold text-body-md hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-surface-container-low/50">
                            <th class="px-6 py-4 font-label-md text-on-surface-variant uppercase tracking-wider">Invoice ID</th>
                            <th class="px-6 py-4 font-label-md text-on-surface-variant uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 font-label-md text-on-surface-variant uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 font-label-md text-on-surface-variant uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 font-label-md text-on-surface-variant uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 font-label-md text-on-surface-variant uppercase tracking-wider text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        @forelse($recentSales ?? [] as $sale)
                            <tr class="hover:bg-primary/5 transition-colors group">
                                <td class="px-6 py-4 font-mono-data text-on-surface">#{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 font-body-md font-bold">{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                                <td class="px-6 py-4 text-body-md text-on-surface-variant">{{ $sale->created_at->format('M d, g:i A') }}</td>
                                <td class="px-6 py-4 font-bold text-on-surface">${{ number_format($sale->total_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[12px] font-bold bg-tertiary-fixed text-on-tertiary-fixed">Completed</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="p-2 text-outline hover:text-primary transition-colors inline-block">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-outline-variant">
                                    <span class="material-symbols-outlined text-[32px] mb-2 opacity-50 block">receipt_long</span>
                                    No recent sales found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
