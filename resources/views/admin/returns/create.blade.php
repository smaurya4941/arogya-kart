@extends('layouts.admin')

@section('title', 'Return — ' . $sale->invoice_number)

@section('content')
<div class="page mx-auto max-w-4xl" x-data="{ refund: 0 }">
    <div class="page-header">
        <div>
            <h1 class="page-title">Process Return</h1>
            <p class="page-subtitle">
                Against invoice <span class="font-medium">{{ $sale->invoice_number }}</span>
                · {{ $sale->customer?->name ?? 'Walk-in' }}
                · {{ $sale->sale_date->format('d M Y') }}
            </p>
        </div>
        <a href="{{ route('admin.sales.show', $sale) }}" class="btn btn-outline">Back to sale</a>
    </div>

    <form method="POST" action="{{ route('admin.returns.store', $sale) }}"
          x-on:input="refund = [...$el.querySelectorAll('[data-unit]')].reduce((s,i)=>s + (parseFloat(i.value||0)*parseFloat(i.dataset.unit)),0)">
        @csrf
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-right">Sold</th>
                            <th class="text-right">Returned</th>
                            <th class="text-right">Unit ₹</th>
                            <th class="text-right">Return Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $i => $item)
                            @php $returnable = $item->returnableQuantity(); @endphp
                            <tr class="{{ $returnable === 0 ? 'opacity-50' : '' }}">
                                <td>
                                    <div class="font-medium text-on-surface">{{ $item->product?->name ?? 'Product #'.$item->product_id }}</div>
                                    <div class="text-xs text-on-surface-variant">Batch: {{ $item->batch?->batch_number ?? '—' }}</div>
                                    <input type="hidden" name="lines[{{ $i }}][sale_item_id]" value="{{ $item->id }}">
                                </td>
                                <td class="text-right">{{ $item->quantity }}</td>
                                <td class="text-right">{{ $item->returnedQuantity() }}</td>
                                <td class="text-right">{{ number_format($item->unitRefundValue(), 2) }}</td>
                                <td class="text-right">
                                    <input type="number" name="lines[{{ $i }}][quantity]" value="0" min="0" max="{{ $returnable }}"
                                           data-unit="{{ $item->unitRefundValue() }}"
                                           {{ $returnable === 0 ? 'disabled' : '' }}
                                           class="form-input h-8 w-20 text-right">
                                    <div class="mt-1 text-[11px] text-on-surface-variant">max {{ $returnable }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="form-label">Refund method</label>
                <select name="refund_method" class="form-select">
                    <option value="cash">Cash</option>
                    <option value="upi">UPI</option>
                    <option value="card">Card</option>
                    <option value="adjustment">Adjustment / Credit note</option>
                </select>
            </div>
            <div>
                <label class="form-label">Reason <span class="text-outline">(optional)</span></label>
                <input type="text" name="reason" value="{{ old('reason') }}" placeholder="e.g. Wrong item, expired, customer changed mind" class="form-input">
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between rounded-xl border border-primary/20 bg-primary/5 px-5 py-4">
            <span class="text-sm text-primary">Estimated refund</span>
            <span class="text-xl font-bold text-primary">₹<span x-text="refund.toFixed(2)">0.00</span></span>
        </div>

        <div class="mt-4 flex gap-2">
            <button class="btn btn-primary" onclick="return confirm('Process this return and restore stock?')">Process Return</button>
            <a href="{{ route('admin.sales.show', $sale) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
