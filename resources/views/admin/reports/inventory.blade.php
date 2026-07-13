@extends('layouts.admin')

@section('title', 'Inventory Valuation')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Inventory Valuation</h1>
            <p class="page-subtitle">Current stock on hand valued at cost and retail.</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Units in Stock</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">{{ number_format($valuation['units']) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Cost Value</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹{{ number_format($valuation['cost_value'], 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Retail Value</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹{{ number_format($valuation['retail_value'], 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Potential Margin</p>
            <p class="mt-1 text-2xl font-bold text-tertiary">₹{{ number_format($valuation['potential_margin'], 2) }}</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th class="text-right">Units</th>
                        <th class="text-right">Cost Value</th>
                        <th class="text-right">Retail Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $units = (int) $product->batches->sum('quantity');
                            $cost = $product->batches->sum(fn ($b) => $b->quantity * (float) $b->purchase_price);
                            $retail = $product->batches->sum(fn ($b) => $b->quantity * (float) $b->mrp);
                        @endphp
                        <tr>
                            <td class="font-medium">
                                <a class="text-primary hover:underline" href="{{ route('admin.products.show', $product) }}">{{ $product->name }}</a>
                            </td>
                            <td class="text-on-surface-variant">{{ $product->sku ?? '-' }}</td>
                            <td class="text-on-surface-variant">{{ $product->category->name ?? '-' }}</td>
                            <td class="text-right">{{ number_format($units) }}</td>
                            <td class="text-right">₹{{ number_format($cost, 2) }}</td>
                            <td class="text-right">₹{{ number_format($retail, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">inventory_2</span>
                                    No products found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="card-footer">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection
