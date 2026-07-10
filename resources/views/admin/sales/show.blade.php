@extends('layouts.admin')

@section('title', 'Sale ' . $sale->invoice_number)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $sale->invoice_number }}</h1>
        <p class="text-sm text-gray-600">{{ $sale->sale_date->format('d M Y, h:i A') }}</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.sales.invoice', $sale) }}" target="_blank"
           class="bg-slate-800 text-white px-4 py-2 rounded hover:bg-slate-900">Print Invoice</a>
        @can('create', \App\Models\SaleReturn::class)
            @if($sale->hasReturnableItems())
                <a href="{{ route('admin.returns.create', $sale) }}" class="bg-amber-500 text-white px-4 py-2 rounded hover:bg-amber-600">Process Return</a>
            @endif
        @endcan
        <a href="{{ route('admin.sales.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">New Sale</a>
        <a href="{{ route('admin.sales.index') }}" class="px-4 py-2 rounded border">Back</a>
    </div>
</div>

@if($sale->returns->isNotEmpty())
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-5 py-3 text-sm text-amber-800">
        <span class="font-medium">₹{{ number_format($sale->totalRefunded(), 2) }}</span> refunded across {{ $sale->returns->count() }} return(s).
        @foreach($sale->returns as $r)
            <a href="{{ route('admin.returns.show', $r) }}" class="underline ml-1">{{ $r->return_number }}</a>
        @endforeach
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white shadow rounded p-6 space-y-3">
        <h2 class="font-semibold">Bill Details</h2>
        <dl class="text-sm space-y-2">
            <div class="flex justify-between"><dt class="text-gray-500">Customer</dt><dd>{{ $sale->customer?->name ?? 'Walk-in' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Cashier</dt><dd>{{ $sale->cashier?->name ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Payment</dt><dd class="capitalize">{{ $sale->payment_method }}</dd></div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Status</dt>
                <dd><span class="rounded-full px-2 py-0.5 text-xs capitalize {{ $sale->paymentStatusBadge() }}">{{ $sale->payment_status }}</span></dd>
            </div>
            @if($sale->notes)
                <div class="pt-2"><dt class="text-gray-500 mb-1">Notes</dt><dd>{{ $sale->notes }}</dd></div>
            @endif
        </dl>
    </div>

    <div class="lg:col-span-2 bg-white shadow rounded">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-3">Medicine</th>
                        <th class="p-3">Batch</th>
                        <th class="p-3 text-right">Price</th>
                        <th class="p-3 text-right">Qty</th>
                        <th class="p-3 text-right">Disc %</th>
                        <th class="p-3 text-right">GST %</th>
                        <th class="p-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                        <tr class="border-t">
                            <td class="p-3 font-medium">{{ $item->product?->name ?? '—' }}</td>
                            <td class="p-3 text-gray-600">{{ $item->batch?->batch_number ?? '—' }}</td>
                            <td class="p-3 text-right">₹{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="p-3 text-right">{{ $item->quantity }}</td>
                            <td class="p-3 text-right">{{ rtrim(rtrim(number_format((float) $item->discount_percentage, 2), '0'), '.') }}</td>
                            <td class="p-3 text-right">{{ rtrim(rtrim(number_format((float) $item->tax_percentage, 2), '0'), '.') }}</td>
                            <td class="p-3 text-right font-medium">₹{{ number_format((float) $item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t">
                    <tr><td colspan="6" class="p-2 text-right text-gray-500">Subtotal</td><td class="p-2 text-right">₹{{ number_format((float) $sale->subtotal, 2) }}</td></tr>
                    <tr><td colspan="6" class="p-2 text-right text-gray-500">GST</td><td class="p-2 text-right">₹{{ number_format((float) $sale->tax_amount, 2) }}</td></tr>
                    <tr><td colspan="6" class="p-2 text-right text-gray-500">Discount</td><td class="p-2 text-right">− ₹{{ number_format((float) $sale->discount_amount, 2) }}</td></tr>
                    <tr class="font-bold"><td colspan="6" class="p-2 text-right">Grand Total</td><td class="p-2 text-right">₹{{ number_format((float) $sale->total_amount, 2) }}</td></tr>
                    <tr><td colspan="6" class="p-2 text-right text-gray-500">Paid</td><td class="p-2 text-right">₹{{ number_format((float) $sale->paid_amount, 2) }}</td></tr>
                    @if($sale->due_amount > 0)
                        <tr class="text-rose-600"><td colspan="6" class="p-2 text-right">Balance Due</td><td class="p-2 text-right">₹{{ number_format((float) $sale->due_amount, 2) }}</td></tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
