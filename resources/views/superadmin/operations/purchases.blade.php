@extends('layouts.superadmin')

@section('title', 'Operations · Purchases')

@section('content')
    @include('superadmin.operations._tabs')

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap items-center gap-2">
                @include('superadmin.operations._tenant_select')
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Invoice #" class="form-input w-48">
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
                        <th>Supplier</th>
                        <th>Date</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td class="text-on-surface-variant">{{ $purchase->pharmacy?->name ?? '—' }}</td>
                            <td class="font-mono-data">{{ $purchase->invoice_number }}</td>
                            <td>{{ $purchase->supplier?->name ?? '—' }}</td>
                            <td class="text-on-surface-variant">{{ optional($purchase->purchase_date)->format('d M Y') }}</td>
                            <td class="text-right font-semibold">₹{{ number_format($purchase->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state">No purchases found.</div></td></tr>
                    @endforelse
                </tbody>
                @if($purchases->isNotEmpty())
                    <tfoot>
                        <tr class="font-semibold">
                            <td colspan="4" class="text-right">Total (all matching)</td>
                            <td class="text-right">₹{{ number_format($total, 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="card-footer">{{ $purchases->links() }}</div>
        @endif
    </div>
@endsection
