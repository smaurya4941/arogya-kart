@extends('layouts.admin')

@section('title', 'Purchase · ' . $purchase->invoice_number)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $purchase->invoice_number }}</h1>
        <p class="text-sm text-gray-600">
            {{ $purchase->supplier?->name ?? 'Unknown supplier' }} ·
            {{ $purchase->purchase_date->format('M d, Y') }}
        </p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('admin.purchases.create') }}"
           class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">New Purchase</a>
        <a href="{{ route('admin.purchases.index') }}" class="px-4 py-2 rounded border">Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-500">Supplier Invoice #</p>
        <p class="font-semibold">{{ $purchase->supplier_invoice_number ?? '-' }}</p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-500">Payment Terms</p>
        <p class="font-semibold">{{ $purchase->payment_terms ?? '-' }}</p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-500">Items</p>
        <p class="font-semibold">{{ $purchase->items->count() }}</p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-500">Total Amount</p>
        <p class="font-semibold text-emerald-700">₹{{ number_format($purchase->total_amount, 2) }}</p>
    </div>
</div>

<div class="bg-white rounded shadow">
    <div class="p-4 border-b">
        <h2 class="font-semibold">Line Items</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">Product</th>
                    <th class="p-3">Batch #</th>
                    <th class="p-3">Expiry</th>
                    <th class="p-3">Qty</th>
                    <th class="p-3">Buy Price</th>
                    <th class="p-3">MRP</th>
                    <th class="p-3">Sell Price</th>
                    <th class="p-3">GST %</th>
                    <th class="p-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $item->product?->name ?? '-' }}</td>
                        <td class="p-3 text-gray-600">{{ $item->batch?->batch_number ?? '-' }}</td>
                        <td class="p-3 text-gray-600">{{ $item->batch?->expiry_date?->format('M d, Y') ?? '-' }}</td>
                        <td class="p-3">{{ $item->quantity }}</td>
                        <td class="p-3">₹{{ number_format($item->purchase_price, 2) }}</td>
                        <td class="p-3">₹{{ number_format($item->mrp, 2) }}</td>
                        <td class="p-3">₹{{ number_format($item->selling_price, 2) }}</td>
                        <td class="p-3">{{ rtrim(rtrim(number_format($item->gst_percentage, 2), '0'), '.') }}%</td>
                        <td class="p-3 text-right font-medium">₹{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t bg-gray-50">
                    <td colspan="8" class="p-3 text-right font-semibold">Grand Total</td>
                    <td class="p-3 text-right font-bold">₹{{ number_format($purchase->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($purchase->notes)
    <div class="bg-white rounded shadow p-4 mt-6">
        <p class="text-xs text-gray-500 mb-1">Notes</p>
        <p class="text-sm">{{ $purchase->notes }}</p>
    </div>
@endif
@endsection
