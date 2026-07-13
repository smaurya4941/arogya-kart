@extends('layouts.superadmin')

@section('title', 'Operations')

@section('content')
    @include('superadmin.operations._tabs')

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        <div class="card card-pad">
            <p class="text-sm text-on-surface-variant">Sales (this month)</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹{{ number_format($salesThisMonth, 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-sm text-on-surface-variant">Sales (all time)</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹{{ number_format($salesAllTime, 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-sm text-on-surface-variant">Purchases (all time)</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹{{ number_format($purchasesAllTime, 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-sm text-on-surface-variant">Expenses (this month)</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹{{ number_format($expensesThisMonth, 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-sm text-on-surface-variant">Products</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">{{ number_format($productCount) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-sm text-on-surface-variant">Customers</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">{{ number_format($customerCount) }}</p>
        </div>
    </div>

    <div class="card mt-4 overflow-hidden">
        <div class="card-header"><h2 class="section-title">Top pharmacies by sales (this month)</h2></div>
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Pharmacy</th>
                        <th class="text-right">Sales</th>
                        <th class="text-right">Value</th>
                        <th class="text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topPharmacies as $row)
                        <tr>
                            <td class="font-medium">{{ $row->pharmacy?->name ?? '—' }}</td>
                            <td class="text-right text-on-surface-variant">{{ number_format($row->sale_count) }}</td>
                            <td class="text-right font-semibold">₹{{ number_format($row->sales_value, 2) }}</td>
                            <td class="text-right">
                                <a href="{{ route('superadmin.operations.sales', ['pharmacy_id' => $row->pharmacy_id]) }}" class="btn btn-xs btn-outline">View sales</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="empty-state">No sales recorded this month.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
