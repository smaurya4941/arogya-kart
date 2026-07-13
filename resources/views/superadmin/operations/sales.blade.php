@extends('layouts.superadmin')

@section('title', 'Operations · Sales')

@section('content')
    @include('superadmin.operations._tabs')

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap items-center gap-2">
                @include('superadmin.operations._tenant_select')
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Invoice #" class="form-input w-40">
                <select name="payment_status" class="form-select w-auto">
                    <option value="">Any payment</option>
                    <option value="paid" @selected(request('payment_status')==='paid')>Paid</option>
                    <option value="partial" @selected(request('payment_status')==='partial')>Partial</option>
                    <option value="unpaid" @selected(request('payment_status')==='unpaid')>Unpaid</option>
                </select>
                <input type="date" name="from" value="{{ request('from') }}" class="form-input w-auto">
                <input type="date" name="to" value="{{ request('to') }}" class="form-input w-auto">
                <button class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Pharmacy</th>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Cashier</th>
                        <th class="text-right">Total</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td class="text-on-surface-variant">{{ $sale->pharmacy?->name ?? '—' }}</td>
                            <td class="font-mono-data">{{ $sale->invoice_number }}</td>
                            <td class="text-on-surface-variant">{{ optional($sale->sale_date)->format('d M Y') }}</td>
                            <td>{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                            <td class="text-on-surface-variant">{{ $sale->cashier?->name ?? '—' }}</td>
                            <td class="text-right font-semibold">₹{{ number_format($sale->total_amount, 2) }}</td>
                            <td><span class="badge {{ $sale->paymentStatusBadge() }}">{{ ucfirst($sale->payment_status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty-state">No sales found.</div></td></tr>
                    @endforelse
                </tbody>
                @if($sales->isNotEmpty())
                    <tfoot>
                        <tr class="font-semibold">
                            <td colspan="5" class="text-right">Total (all matching)</td>
                            <td class="text-right">₹{{ number_format($total, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if($sales->hasPages())
            <div class="card-footer">{{ $sales->links() }}</div>
        @endif
    </div>
@endsection
