@extends('layouts.admin')

@section('title', 'Purchase · ' . $purchase->invoice_number)

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $purchase->invoice_number }}</h1>
            <p class="page-subtitle">
                {{ $purchase->supplier?->name ?? 'Unknown supplier' }} ·
                {{ $purchase->purchase_date->format('M d, Y') }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary">
                <span class="material-symbols-outlined text-[18px]">add</span> New Purchase
            </a>
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Supplier Invoice #</p>
            <p class="mt-1 font-semibold text-on-surface">{{ $purchase->supplier_invoice_number ?? '-' }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Payment Terms</p>
            <p class="mt-1 font-semibold text-on-surface">{{ $purchase->payment_terms ?? '-' }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Items</p>
            <p class="mt-1 font-semibold text-on-surface">{{ $purchase->items->count() }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Total Amount</p>
            <p class="mt-1 font-semibold text-primary">₹{{ number_format($purchase->total_amount, 2) }}</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <h2 class="section-title">Line Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Batch #</th>
                        <th>Expiry</th>
                        <th>Qty</th>
                        <th>Buy Price</th>
                        <th>MRP</th>
                        <th>Sell Price</th>
                        <th>GST %</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                        <tr>
                            <td class="font-medium">{{ $item->product?->name ?? '-' }}</td>
                            <td class="text-on-surface-variant">{{ $item->batch?->batch_number ?? '-' }}</td>
                            <td class="text-on-surface-variant">{{ $item->batch?->expiry_date?->format('M d, Y') ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->purchase_price, 2) }}</td>
                            <td>₹{{ number_format($item->mrp, 2) }}</td>
                            <td>₹{{ number_format($item->selling_price, 2) }}</td>
                            <td>{{ rtrim(rtrim(number_format($item->gst_percentage, 2), '0'), '.') }}%</td>
                            <td class="text-right font-medium">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-surface-container-low/60">
                        <td colspan="8" class="px-4 py-3 text-right font-semibold">Grand Total</td>
                        <td class="px-4 py-3 text-right font-bold text-on-surface">₹{{ number_format($purchase->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if($purchase->notes)
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Notes</p>
            <p class="mt-1 text-sm text-on-surface">{{ $purchase->notes }}</p>
        </div>
    @endif
</div>
@endsection
