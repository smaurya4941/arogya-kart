@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">Products</h1>
        <p class="text-sm text-gray-600">Manage catalog items and batch inventory.</p>
    </div>
    <a href="{{ route('admin.products.create') }}"
       class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
        Add Product
    </a>
</div>

<form method="GET" action="{{ route('admin.products.index') }}" class="bg-white rounded shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Name, SKU, or Barcode"
                   class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">SKU</label>
            <input type="text" name="sku" value="{{ request('sku') }}"
                   class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Category</label>
            <select name="category_id" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Drug Type</label>
            <input type="text" name="drug_type" value="{{ request('drug_type') }}"
                   class="w-full border rounded px-3 py-2">
        </div>
    </div>
    <div class="flex gap-3 mt-4">
        <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Apply</button>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded border">Reset</a>
    </div>
</form>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded shadow">
        <div class="p-4 border-b">
            <h2 class="font-semibold">Product List</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-3">Name</th>
                        <th class="p-3">SKU</th>
                        <th class="p-3">Category</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Batches</th>
                        <th class="p-3">Stock</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="border-t">
                            <td class="p-3 font-medium">{{ $product->name }}</td>
                            <td class="p-3 text-gray-600">{{ $product->sku }}</td>
                            <td class="p-3 text-gray-600">{{ $product->category?->name ?? '-' }}</td>
                            <td class="p-3 text-gray-600">{{ $product->drug_type ?? '-' }}</td>
                            <td class="p-3">{{ $product->batches_count ?? 0 }}</td>
                            <td class="p-3">{{ $product->total_stock ?? 0 }}</td>
                            <td class="p-3 space-x-2">
                                <a class="text-emerald-700 hover:underline"
                                   href="{{ route('admin.products.show', $product) }}">
                                    View
                                </a>
                                <a class="text-blue-600 hover:underline"
                                   href="{{ route('admin.products.edit', $product) }}">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-3 text-gray-600" colspan="7">No products yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $products->links() }}
        </div>
    </div>

    <div class="bg-white rounded shadow">
        <div class="p-4 border-b">
            <h2 class="font-semibold">Expiring Soon</h2>
            <p class="text-xs text-gray-500">Next 30 days</p>
        </div>
        <div class="p-4 space-y-3">
            @forelse($expiringBatches as $batch)
                <div class="border rounded p-3">
                    <div class="font-medium">{{ $batch->product->name ?? 'Unknown Product' }}</div>
                    <div class="text-xs text-gray-600">Batch: {{ $batch->batch_number }}</div>
                    <div class="text-xs text-gray-600">Expiry: {{ $batch->expiry_date->format('M d, Y') }}</div>
                    <div class="text-xs text-gray-600">Qty: {{ $batch->quantity }}</div>
                </div>
            @empty
                <p class="text-sm text-gray-600">No expiring batches.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
