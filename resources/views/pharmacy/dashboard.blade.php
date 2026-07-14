@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', now()->format('l, F j, Y'))

@php
    $metrics = [
        [
            'label' => "Today's Sales", 'icon' => 'payments', 'tone' => 'text-primary bg-primary/10',
            'value' => '₹'.number_format($todayRevenue ?? 0, 2),
            'meta' => ($todayInvoices ?? 0).' orders',
        ],
        [
            'label' => 'Monthly Revenue', 'icon' => 'account_balance_wallet', 'tone' => 'text-emerald-600 bg-emerald-100',
            'value' => '₹'.number_format($monthlyRevenue ?? 0, 2),
            'meta' => 'This month',
        ],
        [
            'label' => 'Net Profit', 'icon' => 'trending_up', 'tone' => 'text-blue-600 bg-blue-100',
            'value' => '₹'.number_format($netProfit ?? 0, 2),
            'meta' => 'This month',
        ],
        [
            'label' => 'Monthly Expenses', 'icon' => 'money_off', 'tone' => 'text-rose-600 bg-rose-100',
            'value' => '₹'.number_format($monthlyExpenses ?? 0, 2),
            'meta' => 'This month',
        ],
        [
            'label' => 'Total Medicines', 'icon' => 'medication', 'tone' => 'text-tertiary bg-tertiary/10',
            'value' => str_pad($totalMedicinesCount ?? 0, 2, '0', STR_PAD_LEFT),
            'meta' => 'Active: '.($activeMedicines ?? 0),
        ],
        [
            'label' => "Items Sold Today", 'icon' => 'shopping_bag', 'tone' => 'text-indigo-600 bg-indigo-100',
            'value' => str_pad($todayItemsSold ?? 0, 2, '0', STR_PAD_LEFT),
            'meta' => 'Total quantity',
        ],
        [
            'label' => 'Low Stock', 'icon' => 'inventory', 'tone' => 'text-secondary bg-secondary/10',
            'value' => str_pad($lowStockCount ?? 0, 2, '0', STR_PAD_LEFT),
            'meta' => ($lowStockCount ?? 0) > 0 ? 'Needs attention' : 'Healthy',
        ],
        [
            'label' => 'Expiring Soon', 'icon' => 'event_busy', 'tone' => 'text-error bg-error-container/40',
            'value' => str_pad($expiringCount ?? 0, 2, '0', STR_PAD_LEFT),
            'meta' => 'Next 90 days',
        ],
    ];
@endphp

@section('content')
<div class="page">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h2 class="page-title">Operations Dashboard</h2>
            <p class="page-subtitle">{{ auth()->user()->pharmacy->name ?? 'Your pharmacy' }} · {{ now()->format('M j, Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.pos.index') }}" class="btn btn-outline">
                <span class="material-symbols-outlined text-[18px]">receipt_long</span> New Bill
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-outline">
                <span class="material-symbols-outlined text-[18px]">add_business</span> Add Inventory
            </a>
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary">
                <span class="material-symbols-outlined text-[18px]">payments</span> Purchase
            </a>
        </div>
    </div>

    <!-- Metric cards -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach($metrics as $m)
            <div class="card card-pad">
                <div class="mb-3 flex items-center justify-between">
                    <span class="icon-tile {{ $m['tone'] }}">
                        <span class="material-symbols-outlined text-[20px]">{{ $m['icon'] }}</span>
                    </span>
                    <span class="text-[11px] font-medium text-on-surface-variant">{{ $m['meta'] }}</span>
                </div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">{{ $m['label'] }}</p>
                <h3 class="mt-0.5 text-2xl font-bold tracking-tight text-on-surface">{{ $m['value'] }}</h3>
            </div>
        @endforeach
    </div>

    <!-- Main grid -->
    <div class="grid grid-cols-12 gap-4">
        <!-- Sales overview -->
        <div class="card col-span-12 lg:col-span-8" x-data="salesChart()">
            <div class="card-header">
                <div>
                    <h4 class="section-title">Sales Overview</h4>
                    <p class="text-xs text-on-surface-variant" x-text="timeframe === '7days' ? 'Weekly distribution' : 'Monthly distribution'">Weekly distribution</p>
                </div>
                <div class="flex gap-1 rounded-lg bg-surface-container-low p-0.5">
                    <button @click="setTimeframe('7days')" :class="timeframe === '7days' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant hover:bg-white/60'" class="rounded-md px-2.5 py-1 text-xs font-semibold transition-colors">7 Days</button>
                    <button @click="setTimeframe('30days')" :class="timeframe === '30days' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant hover:bg-white/60'" class="rounded-md px-2.5 py-1 text-xs font-medium transition-colors">Month</button>
                </div>
            </div>
            <div class="card-pad" style="height: 300px;">
                <div id="salesChart" class="w-full h-full"></div>
            </div>
        </div>

        <!-- Critical inventory -->
        <div class="card col-span-12 flex flex-col lg:col-span-4">
            <div class="card-header">
                <h4 class="section-title">Critical Inventory</h4>
                @if(count($lowStockMedicines ?? []) > 0 || count($expiringMedicines ?? []) > 0)
                    <span class="badge badge-danger">Urgent</span>
                @else
                    <span class="badge badge-success">Clear</span>
                @endif
            </div>
            <div class="flex-1 space-y-2 p-3">
                @forelse(collect($expiringMedicines ?? [])->take(2) as $batch)
                    <div class="flex items-center gap-3 rounded-lg bg-surface-container-low px-3 py-2">
                        <span class="icon-tile h-9 w-9 bg-error-container/40 text-error">
                            <span class="material-symbols-outlined text-[18px]">event_busy</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-on-surface">{{ $batch->product->name }}</p>
                            <p class="text-xs text-on-surface-variant">Expiring {{ \Carbon\Carbon::parse($batch->expiry_date)->diffForHumans() }}</p>
                        </div>
                        <span class="font-mono-data text-error">{{ $batch->quantity }}</span>
                    </div>
                @empty
                @endforelse

                @forelse(collect($lowStockMedicines ?? [])->take(3) as $med)
                    <div class="flex items-center gap-3 rounded-lg bg-surface-container-low px-3 py-2">
                        <span class="icon-tile h-9 w-9 bg-amber-100 text-amber-600">
                            <span class="material-symbols-outlined text-[18px]">warning</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-on-surface">{{ $med->name }}</p>
                            <p class="text-xs text-on-surface-variant">Below reorder level</p>
                        </div>
                        <span class="font-mono-data text-secondary">{{ $med->total_stock }}</span>
                    </div>
                @empty
                    @if(count($expiringMedicines ?? []) === 0)
                        <div class="empty-state">
                            <span class="material-symbols-outlined text-[40px] opacity-40">check_circle</span>
                            <p class="text-sm">No critical alerts.</p>
                        </div>
                    @endif
                @endforelse
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.products.index') }}" class="btn btn-ghost w-full">View Inventory</a>
            </div>
        </div>

        <!-- Recent sales -->
        <div class="card col-span-12 overflow-hidden">
            <div class="card-header">
                <h4 class="section-title">Recent Sales</h4>
                <a href="{{ route('admin.sales.index') }}" class="text-sm font-semibold text-primary hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales ?? [] as $sale)
                            <tr>
                                <td class="font-mono-data">#{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="font-medium">{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                                <td class="text-on-surface-variant">{{ $sale->created_at->format('M d, g:i A') }}</td>
                                <td class="font-semibold">₹{{ number_format($sale->total_amount, 2) }}</td>
                                <td><span class="badge badge-success">Completed</span></td>
                                <td class="text-right">
                                    <a href="{{ route('admin.sales.show', $sale) }}" class="btn-icon ml-auto">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <span class="material-symbols-outlined text-[32px] opacity-40">receipt_long</span>
                                        No recent sales found.
                                    </div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('salesChart', () => ({
            timeframe: '7days',
            chart: null,
            data7Days: @json($last7Days ?? []),
            data30Days: @json($last30Days ?? []),

            init() {
                setTimeout(() => {
                    this.renderChart();
                }, 100);
            },

            setTimeframe(tf) {
                this.timeframe = tf;
                this.updateChart();
            },

            getChartOptions() {
                const data = this.timeframe === '7days' ? this.data7Days : this.data30Days;
                return {
                    series: [{
                        name: 'Sales',
                        data: data.map(item => item.sales)
                    }],
                    chart: {
                        type: 'area',
                        height: 280,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        fontFamily: 'inherit'
                    },
                    colors: ['#0f766e'], // primary color (teal-700 approx)
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                    xaxis: {
                        categories: data.map(item => item.date),
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: {
                            style: { colors: '#64748b', fontSize: '12px' }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: (value) => { return '₹' + parseFloat(value).toFixed(0) },
                            style: { colors: '#64748b', fontSize: '12px' }
                        }
                    },
                    grid: {
                        borderColor: '#e2e8f0',
                        strokeDashArray: 4,
                        yaxis: { lines: { show: true } }
                    },
                    tooltip: { theme: 'light' }
                };
            },

            renderChart() {
                if (this.chart) {
                    this.chart.destroy();
                }
                this.chart = new ApexCharts(document.querySelector("#salesChart"), this.getChartOptions());
                this.chart.render();
            },

            updateChart() {
                const data = this.timeframe === '7days' ? this.data7Days : this.data30Days;
                this.chart.updateOptions({
                    xaxis: { categories: data.map(item => item.date) }
                });
                this.chart.updateSeries([{
                    name: 'Sales',
                    data: data.map(item => item.sales)
                }]);
            }
        }));
    });
</script>
@endpush
