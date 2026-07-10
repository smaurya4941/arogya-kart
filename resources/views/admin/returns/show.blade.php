@extends('layouts.admin')

@section('title', 'Return ' . $return->return_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ $return->return_number }}</h1>
            <p class="text-sm text-gray-600">{{ $return->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.sales.show', $return->sale_id) }}" class="bg-slate-800 text-white px-4 py-2 rounded hover:bg-slate-900">View original sale</a>
            <a href="{{ route('admin.returns.index') }}" class="px-4 py-2 rounded border">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white shadow rounded p-6 space-y-3">
            <h2 class="font-semibold">Return Details</h2>
            <dl class="text-sm space-y-2">
                <div class="flex justify-between"><dt class="text-gray-500">Invoice</dt><dd>{{ $return->sale?->invoice_number ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Customer</dt><dd>{{ $return->sale?->customer?->name ?? 'Walk-in' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Processed by</dt><dd>{{ $return->processor?->name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Refund method</dt><dd class="capitalize">{{ $return->refund_method }}</dd></div>
                @if($return->reason)
                    <div class="pt-2"><dt class="text-gray-500 mb-1">Reason</dt><dd>{{ $return->reason }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="lg:col-span-2 bg-white shadow rounded p-6">
            <h2 class="font-semibold mb-4">Returned Items</h2>
            <table class="min-w-full text-sm">
                <thead class="text-left text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-2 pr-4">Product</th>
                        <th class="py-2 pr-4 text-right">Qty</th>
                        <th class="py-2 pr-4 text-right">Unit ₹</th>
                        <th class="py-2 pr-4 text-right">Refund ₹</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($return->items as $item)
                        <tr>
                            <td class="py-2 pr-4">
                                {{ $item->product?->name ?? 'Product #'.$item->product_id }}
                                <div class="text-xs text-gray-400">Batch: {{ $item->batch?->batch_number ?? '—' }}</div>
                            </td>
                            <td class="py-2 pr-4 text-right">{{ $item->quantity }}</td>
                            <td class="py-2 pr-4 text-right">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-2 pr-4 text-right">{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-200 font-semibold">
                        <td class="py-3 pr-4" colspan="3">Total refunded ({{ $return->tax_amount > 0 ? 'incl. ₹'.number_format($return->tax_amount,2).' tax' : 'no tax' }})</td>
                        <td class="py-3 pr-4 text-right text-emerald-700">₹{{ number_format($return->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
