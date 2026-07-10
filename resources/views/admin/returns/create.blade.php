@extends('layouts.admin')

@section('title', 'Return — ' . $sale->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ refund: 0 }">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Process Return</h1>
            <p class="text-sm text-gray-600">
                Against invoice <span class="font-medium">{{ $sale->invoice_number }}</span>
                · {{ $sale->customer?->name ?? 'Walk-in' }}
                · {{ $sale->sale_date->format('d M Y') }}
            </p>
        </div>
        <a href="{{ route('admin.sales.show', $sale) }}" class="px-4 py-2 rounded border">Back to sale</a>
    </div>

    <form method="POST" action="{{ route('admin.returns.store', $sale) }}"
          x-on:input="refund = [...$el.querySelectorAll('[data-unit]')].reduce((s,i)=>s + (parseFloat(i.value||0)*parseFloat(i.dataset.unit)),0)">
        @csrf
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Item</th>
                        <th class="px-4 py-3 font-semibold text-right">Sold</th>
                        <th class="px-4 py-3 font-semibold text-right">Returned</th>
                        <th class="px-4 py-3 font-semibold text-right">Unit ₹</th>
                        <th class="px-4 py-3 font-semibold text-right">Return Qty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sale->items as $i => $item)
                        @php $returnable = $item->returnableQuantity(); @endphp
                        <tr class="{{ $returnable === 0 ? 'opacity-50' : '' }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $item->product?->name ?? 'Product #'.$item->product_id }}</div>
                                <div class="text-xs text-gray-400">Batch: {{ $item->batch?->batch_number ?? '—' }}</div>
                                <input type="hidden" name="lines[{{ $i }}][sale_item_id]" value="{{ $item->id }}">
                            </td>
                            <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right">{{ $item->returnedQuantity() }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($item->unitRefundValue(), 2) }}</td>
                            <td class="px-4 py-3 text-right">
                                <input type="number" name="lines[{{ $i }}][quantity]" value="0" min="0" max="{{ $returnable }}"
                                       data-unit="{{ $item->unitRefundValue() }}"
                                       {{ $returnable === 0 ? 'disabled' : '' }}
                                       class="w-20 rounded-lg border-gray-300 text-sm text-right focus:ring-emerald-500 focus:border-emerald-500">
                                <div class="text-[11px] text-gray-400 mt-1">max {{ $returnable }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Refund method</label>
                <select name="refund_method" class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="cash">Cash</option>
                    <option value="upi">UPI</option>
                    <option value="card">Card</option>
                    <option value="adjustment">Adjustment / Credit note</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-gray-400">(optional)</span></label>
                <input type="text" name="reason" value="{{ old('reason') }}" placeholder="e.g. Wrong item, expired, customer changed mind"
                       class="w-full rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between bg-emerald-50 border border-emerald-100 rounded-xl px-5 py-4">
            <span class="text-sm text-emerald-800">Estimated refund</span>
            <span class="text-xl font-bold text-emerald-800">₹<span x-text="refund.toFixed(2)">0.00</span></span>
        </div>

        <div class="mt-6 flex gap-3">
            <button class="px-5 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700"
                    onclick="return confirm('Process this return and restore stock?')">
                Process Return
            </button>
            <a href="{{ route('admin.sales.show', $sale) }}" class="px-5 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
