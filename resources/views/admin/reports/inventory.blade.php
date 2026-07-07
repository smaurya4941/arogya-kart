@extends('layouts.admin')

@section('title', 'Inventory Valuation')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Inventory Valuation</h1>
    <p class="text-sm text-gray-600">Current stock on hand valued at cost and retail.</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Units in Stock</p>
        <p class="text-2xl font-bold mt-1">{{ number_format($valuation['units']) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Cost Value</p>
        <p class="text-2xl font-bold mt-1">₹{{ number_format($valuation['cost_value'], 2) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Retail Value</p>
        <p class="text-2xl font-bold mt-1">₹{{ number_format($valuation['retail_value'], 2) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Potential Margin</p>
        <p class="text-2xl font-bold mt-1 text-emerald-600">₹{{ number_format($valuation['potential_margin'], 2) }}</p>
    </div>
</div>

<div class="bg-white rounded shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">Product</th>
                    <th class="p-3">SKU</th>
                    <th class="p-3">Category</th>
                    <th class="p-3 text-right">Units</th>
                    <th class="p-3 text-right">Cost Value</th>
                    <th class="p-3 text-right">Retail Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php
                        $units = (int) $product->batches->sum('quantity');
                        $cost = $product->batches->sum(fn ($b) => $b->quantity * (float) $b->purchase_price);
                        $retail = $product->batches->sum(fn ($b) => $b->quantity * (float) $b->mrp);
                    @endphp
                    <tr class="border-t">
                        <td class="p-3 font-medium">
                            <a class="text-emerald-700 hover:underline" href="{{ route('admin.products.show', $product) }}">{{ $product->name }}</a>
                        </td>
                        <td class="p-3 text-gray-600">{{ $product->sku ?? '-' }}</td>
                        <td class="p-3 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                        <td class="p-3 text-right">{{ number_format($units) }}</td>
                        <td class="p-3 text-right">₹{{ number_format($cost, 2) }}</td>
                        <td class="p-3 text-right">₹{{ number_format($retail, 2) }}</td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-gray-600" colspan="6">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $products->links() }}
    </div>
</div>
@endsection
