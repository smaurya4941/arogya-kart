<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-3xl text-gray-900 dark:text-white leading-tight tracking-tight">
                {{ __('Executive Dashboard') }}
            </h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Section 1: Today's Sales -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl shadow-lg p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-10"></div>
                <div class="relative z-10">
                    <h3 class="text-lg font-medium text-blue-100 mb-2 uppercase tracking-wide">Today's Sales</h3>
                    <div class="text-5xl font-extrabold mb-6">₹{{ number_format($todayRevenue, 2) }}</div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-blue-200 text-sm">Invoices</p>
                            <p class="text-2xl font-bold">{{ $todayInvoices }}</p>
                        </div>
                        <div>
                            <p class="text-blue-200 text-sm">Items Sold</p>
                            <p class="text-2xl font-bold">{{ $todayItemsSold }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Section 2: Total Medicines -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider text-sm">Total Medicines</h4>
                        <span class="p-2 bg-indigo-100 text-indigo-600 rounded-lg dark:bg-indigo-900/50 dark:text-indigo-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </span>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">{{ $totalMedicinesCount }}</div>
                    <div class="flex justify-between text-sm">
                        <span class="text-green-500 font-medium">{{ $activeMedicines }} Active</span>
                        <span class="text-red-500 font-medium">{{ $inactiveMedicines }} Inactive</span>
                    </div>
                </div>

                <!-- Section 5: Financial Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 md:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider text-sm">Financial Summary (This Month)</h4>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Revenue</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($monthlyRevenue, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Expenses</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($monthlyExpenses, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Net Profit</p>
                            <p class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-green-500' : 'text-red-500' }}">₹{{ number_format($netProfit, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts & Issues -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Section 3: Low Stock Medicines -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                            <span class="w-3 h-3 rounded-full bg-orange-500 mr-2"></span>
                            Low Stock Alerts ({{ $lowStockCount }})
                        </h3>
                        <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                    </div>
                    <div class="p-0">
                        @if($lowStockCount > 0)
                            <ul class="divide-y divide-gray-100 dark:divide-gray-700 max-h-64 overflow-y-auto">
                                @foreach($lowStockMedicines->take(5) as $medicine)
                                <li class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $medicine->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Min Req: {{ $medicine->min_stock_alert }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-orange-500">{{ $medicine->total_stock }}</p>
                                        <p class="text-xs text-gray-500 uppercase">In Stock</p>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto text-green-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p>All medicines are sufficiently stocked.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Section 4: Expiring Medicines -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                            <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                            Expiring Soon ({{ $expiringCount }})
                        </h3>
                        <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                    </div>
                    <div class="p-0">
                        @if($expiringCount > 0)
                            <ul class="divide-y divide-gray-100 dark:divide-gray-700 max-h-64 overflow-y-auto">
                                @foreach($expiringMedicines->take(5) as $batch)
                                @php
                                    $daysLeft = now()->diffInDays($batch->expiry_date, false);
                                    $textColor = $daysLeft < 30 ? 'text-red-500' : 'text-orange-500';
                                @endphp
                                <li class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $batch->product->name ?? 'Unknown' }} <span class="text-xs bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded text-gray-600 dark:text-gray-300 ml-2">Batch: {{ $batch->batch_number }}</span></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Qty: {{ $batch->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold {{ $textColor }}">{{ ceil($daysLeft) }} Days</p>
                                        <p class="text-xs text-gray-500 uppercase">Remaining</p>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto text-green-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p>No medicines are expiring soon.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section 6: Recent Activities -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Activities</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
                    <div class="p-6">
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Recent Sales</h4>
                        @if($recentSales->count() > 0)
                            <ul class="space-y-4">
                                @foreach($recentSales as $sale)
                                <li class="flex justify-between items-center text-sm">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Invoice #{{ $sale->invoice_number }}</p>
                                        <p class="text-gray-500">{{ $sale->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="font-bold text-green-600">₹{{ number_format($sale->total_amount, 2) }}</span>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">No recent sales.</p>
                        @endif
                    </div>
                    <div class="p-6">
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Recent Purchases</h4>
                        @if($recentPurchases->count() > 0)
                            <ul class="space-y-4">
                                @foreach($recentPurchases as $purchase)
                                <li class="flex justify-between items-center text-sm">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">PO #{{ $purchase->invoice_number }}</p>
                                        <p class="text-gray-500">{{ $purchase->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="font-bold text-red-500">₹{{ number_format($purchase->total_amount, 2) }}</span>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">No recent purchases.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section 7: Analytics Chart (Placeholder) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Revenue Trend (Last 30 Days)</h3>
                    <select class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm">
                        <option>Last 30 Days</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div class="h-64 bg-gray-50 dark:bg-gray-900/50 rounded-xl flex items-center justify-center border border-dashed border-gray-200 dark:border-gray-700">
                    <p class="text-gray-400 dark:text-gray-500 flex items-center flex-col">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        Chart visualizer ready for integration
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
