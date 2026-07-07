@extends('layouts.admin')

@section('title', 'New Purchase')

@php
    $oldItems = old('items');
    $initialRows = is_array($oldItems) && count($oldItems)
        ? array_values($oldItems)
        : [[
            'product_id' => '', 'batch_number' => '', 'expiry_date' => '',
            'quantity' => 1, 'purchase_price' => '', 'mrp' => '',
            'selling_price' => '', 'gst_percentage' => 0,
        ]];
@endphp

@section('content')
<div class="max-w-6xl"
     x-data="purchaseForm({{ Illuminate\Support\Js::from($initialRows) }})">
    <h1 class="text-2xl font-bold mb-4">New Purchase</h1>

    <form method="POST" action="{{ route('admin.purchases.store') }}" class="space-y-6">
        @csrf

        {{-- Header --}}
        <div class="bg-white shadow rounded p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Supplier <span class="text-rose-500">*</span></label>
                <select name="supplier_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            @selected(old('supplier_id', $selectedSupplierId) == $supplier->id)>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
                @if($suppliers->isEmpty())
                    <p class="text-xs text-rose-600 mt-1">
                        No active suppliers. <a href="{{ route('admin.suppliers.create') }}" class="underline">Add one first.</a>
                    </p>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Purchase Date <span class="text-rose-500">*</span></label>
                <input type="date" name="purchase_date" value="{{ old('purchase_date', now()->toDateString()) }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Supplier Invoice #</label>
                <input type="text" name="supplier_invoice_number" value="{{ old('supplier_invoice_number') }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Payment Terms</label>
                <input type="text" name="payment_terms" value="{{ old('payment_terms') }}"
                       placeholder="e.g. Net 30" class="w-full border rounded px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Notes</label>
                <input type="text" name="notes" value="{{ old('notes') }}"
                       class="w-full border rounded px-3 py-2">
            </div>
        </div>

        {{-- Line items --}}
        <div class="bg-white shadow rounded p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold">Items</h2>
                <button type="button" @click="addRow()"
                        class="bg-emerald-600 text-white px-3 py-1.5 rounded text-sm hover:bg-emerald-700">
                    + Add Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="p-2">Product</th>
                            <th class="p-2">Batch #</th>
                            <th class="p-2">Expiry</th>
                            <th class="p-2 w-20">Qty</th>
                            <th class="p-2 w-28">Buy Price</th>
                            <th class="p-2 w-28">MRP</th>
                            <th class="p-2 w-28">Sell Price</th>
                            <th class="p-2 w-20">GST %</th>
                            <th class="p-2 w-28 text-right">Line Total</th>
                            <th class="p-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, i) in rows" :key="i">
                            <tr class="border-t align-top">
                                <td class="p-2">
                                    <select :name="`items[${i}][product_id]`" x-model="row.product_id"
                                            class="w-44 border rounded px-2 py-1.5" required>
                                        <option value="">Select</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input type="text" :name="`items[${i}][batch_number]`" x-model="row.batch_number"
                                           class="w-28 border rounded px-2 py-1.5" required>
                                </td>
                                <td class="p-2">
                                    <input type="date" :name="`items[${i}][expiry_date]`" x-model="row.expiry_date"
                                           class="w-36 border rounded px-2 py-1.5" required>
                                </td>
                                <td class="p-2">
                                    <input type="number" min="1" :name="`items[${i}][quantity]`" x-model.number="row.quantity"
                                           class="w-20 border rounded px-2 py-1.5" required>
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.01" min="0" :name="`items[${i}][purchase_price]`" x-model.number="row.purchase_price"
                                           class="w-28 border rounded px-2 py-1.5" required>
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.01" min="0" :name="`items[${i}][mrp]`" x-model.number="row.mrp"
                                           class="w-28 border rounded px-2 py-1.5" required>
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.01" min="0" :name="`items[${i}][selling_price]`" x-model.number="row.selling_price"
                                           class="w-28 border rounded px-2 py-1.5">
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.01" min="0" max="100" :name="`items[${i}][gst_percentage]`" x-model.number="row.gst_percentage"
                                           class="w-20 border rounded px-2 py-1.5">
                                </td>
                                <td class="p-2 text-right font-medium" x-text="lineTotal(row).toFixed(2)"></td>
                                <td class="p-2">
                                    <button type="button" @click="removeRow(i)" x-show="rows.length > 1"
                                            class="text-rose-600 hover:underline">✕</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="border-t bg-gray-50">
                            <td colspan="8" class="p-2 text-right font-semibold">Grand Total</td>
                            <td class="p-2 text-right font-bold">₹<span x-text="grandTotal().toFixed(2)"></span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex gap-3">
            <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Record Purchase</button>
            <a href="{{ route('admin.purchases.index') }}" class="px-4 py-2 rounded border">Cancel</a>
        </div>
    </form>
</div>

<script>
    function purchaseForm(initialRows) {
        return {
            rows: initialRows,
            addRow() {
                this.rows.push({
                    product_id: '', batch_number: '', expiry_date: '',
                    quantity: 1, purchase_price: '', mrp: '',
                    selling_price: '', gst_percentage: 0,
                });
            },
            removeRow(i) {
                this.rows.splice(i, 1);
            },
            lineTotal(row) {
                const base = (Number(row.quantity) || 0) * (Number(row.purchase_price) || 0);
                const gst = base * (Number(row.gst_percentage) || 0) / 100;
                return base + gst;
            },
            grandTotal() {
                return this.rows.reduce((sum, row) => sum + this.lineTotal(row), 0);
            },
        };
    }
</script>
@endsection
