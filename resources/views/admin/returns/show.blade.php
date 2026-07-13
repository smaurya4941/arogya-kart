@extends('layouts.admin')

@section('title', 'Return ' . $return->return_number)

@section('content')
<div class="page mx-auto max-w-4xl">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $return->return_number }}</h1>
            <p class="page-subtitle">{{ $return->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.sales.show', $return->sale_id) }}" class="btn btn-primary">View original sale</a>
            <a href="{{ route('admin.returns.index') }}" class="btn btn-outline">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad space-y-3">
            <h2 class="section-title">Return Details</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-on-surface-variant">Invoice</dt><dd>{{ $return->sale?->invoice_number ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Customer</dt><dd>{{ $return->sale?->customer?->name ?? 'Walk-in' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Processed by</dt><dd>{{ $return->processor?->name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Refund method</dt><dd class="capitalize">{{ $return->refund_method }}</dd></div>
                @if($return->reason)
                    <div class="pt-2"><dt class="mb-1 text-on-surface-variant">Reason</dt><dd>{{ $return->reason }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="card card-pad lg:col-span-2">
            <h2 class="section-title mb-4">Returned Items</h2>
            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Unit ₹</th>
                            <th class="text-right">Refund ₹</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($return->items as $item)
                            <tr>
                                <td>
                                    {{ $item->product?->name ?? 'Product #'.$item->product_id }}
                                    <div class="text-xs text-on-surface-variant">Batch: {{ $item->batch?->batch_number ?? '—' }}</div>
                                </td>
                                <td class="text-right">{{ $item->quantity }}</td>
                                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-right">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold">
                            <td class="px-4 py-3" colspan="3">Total refunded ({{ $return->tax_amount > 0 ? 'incl. ₹'.number_format($return->tax_amount,2).' tax' : 'no tax' }})</td>
                            <td class="px-4 py-3 text-right text-primary">₹{{ number_format($return->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
