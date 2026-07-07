@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
        <p class="text-sm text-gray-600">SKU: {{ $product->sku }}</p>
        <p class="text-xs text-gray-500 mt-1">Category: {{ $product->category?->name ?? '-' }}</p>
        <p class="text-xs text-gray-500">Barcode: {{ $product->barcode ?? '-' }}</p>
        <p class="text-xs text-gray-500">Drug Type: {{ $product->drug_type ?? '-' }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.products.edit', $product) }}"
           class="px-4 py-2 rounded border">Edit</a>
        <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
              onsubmit="return confirm('Delete this product and its batches?');">
            @csrf
            @method('DELETE')
            <button class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Delete</button>
        </form>
    </div>
</div>

@if($product->image_path)
    <div class="bg-white shadow rounded p-4 mb-6">
        <h2 class="font-semibold mb-3">Product Image</h2>
        <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="max-w-xs rounded">
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white shadow rounded p-4">
        <div class="text-sm text-gray-500">Total Stock</div>
        <div class="text-3xl font-bold mt-2">{{ $product->total_stock }}</div>
    </div>
    <div class="bg-white shadow rounded p-4">
        <div class="text-sm text-gray-500">Batches</div>
        <div class="text-3xl font-bold mt-2">{{ $product->batches->count() }}</div>
    </div>
    <div class="bg-white shadow rounded p-4">
        <div class="text-sm text-gray-500">Expiring Soon</div>
        <div class="text-3xl font-bold mt-2">{{ $expiringBatches->count() }}</div>
    </div>
</div>

<div class="bg-white shadow rounded p-6 mb-6">
    <h2 class="font-semibold mb-3">Issue Stock (FEFO)</h2>
    <form method="POST" action="{{ route('admin.products.issue-stock', $product) }}" class="flex flex-col sm:flex-row gap-3">
        @csrf
        <input type="number" name="quantity" min="1" placeholder="Quantity"
               class="border rounded px-3 py-2 w-full sm:w-48" required>
        <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
            Issue Stock
        </button>
    </form>
</div>

<div class="bg-white shadow rounded mb-6">
    <div class="flex items-center justify-between p-4 border-b">
        <h2 class="font-semibold">Batches</h2>
        <a href="{{ route('admin.products.batches.create', $product) }}"
           class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
            Add Batch
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">Batch</th>
                    <th class="p-3">Expiry</th>
                    <th class="p-3">Purchase</th>
                    <th class="p-3">MRP</th>
                    <th class="p-3">Qty</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($product->batches as $batch)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $batch->batch_number }}</td>
                        <td class="p-3">{{ $batch->expiry_date->format('M d, Y') }}</td>
                        <td class="p-3">{{ number_format($batch->purchase_price, 2) }}</td>
                        <td class="p-3">{{ number_format($batch->mrp, 2) }}</td>
                        <td class="p-3">{{ $batch->quantity }}</td>
                        <td class="p-3 space-x-2">
                            <a class="text-blue-600 hover:underline"
                               href="{{ route('admin.batches.edit', $batch) }}">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.batches.destroy', $batch) }}"
                                  class="inline" onsubmit="return confirm('Delete this batch?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3 text-gray-600" colspan="6">No batches yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white shadow rounded p-4">
    <h2 class="font-semibold mb-3">Expiring Batches</h2>
    <div class="space-y-3">
        @forelse($expiringBatches as $batch)
            <div class="border rounded p-3">
                <div class="font-medium">Batch {{ $batch->batch_number }}</div>
                <div class="text-xs text-gray-600">Expiry: {{ $batch->expiry_date->format('M d, Y') }}</div>
                <div class="text-xs text-gray-600">Qty: {{ $batch->quantity }}</div>
            </div>
        @empty
            <p class="text-sm text-gray-600">No expiring batches.</p>
        @endforelse
    </div>
</div>
@endsection
