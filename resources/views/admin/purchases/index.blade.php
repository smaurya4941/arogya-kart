@extends('layouts.admin')

@section('title', 'Purchases')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">Purchases</h1>
        <p class="text-sm text-gray-600">Goods received from suppliers. Recording a purchase stocks in new batches.</p>
    </div>
    <a href="{{ route('admin.purchases.create') }}"
       class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
        New Purchase
    </a>
</div>

<form method="GET" action="{{ route('admin.purchases.index') }}" class="bg-white rounded shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Invoice number"
                   class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Supplier</label>
            <select name="supplier_id" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-3">
            <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Apply</button>
            <a href="{{ route('admin.purchases.index') }}" class="px-4 py-2 rounded border">Reset</a>
        </div>
    </div>
</form>

<div class="bg-white rounded shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">Invoice #</th>
                    <th class="p-3">Supplier</th>
                    <th class="p-3">Supplier Inv #</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Amount</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $invoice->invoice_number }}</td>
                        <td class="p-3 text-gray-600">{{ $invoice->supplier?->name ?? '-' }}</td>
                        <td class="p-3 text-gray-600">{{ $invoice->supplier_invoice_number ?? '-' }}</td>
                        <td class="p-3 text-gray-600">{{ $invoice->purchase_date->format('M d, Y') }}</td>
                        <td class="p-3">₹{{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="p-3">
                            <a class="text-emerald-700 hover:underline" href="{{ route('admin.purchases.show', $invoice) }}">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-gray-600" colspan="6">No purchases recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
