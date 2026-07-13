@extends('layouts.superadmin')

@section('title', 'Operations · Products')

@section('content')
    @include('superadmin.operations._tabs')

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap gap-2">
                @include('superadmin.operations._tenant_select')
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, SKU, generic…" class="form-input min-w-[200px] flex-1">
                <button class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Pharmacy</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th class="text-right">Stock</th>
                        <th class="text-right">Selling price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="text-on-surface-variant">{{ $product->pharmacy?->name ?? '—' }}</td>
                            <td>
                                <div class="font-medium">{{ $product->name }}</div>
                                <div class="text-xs text-on-surface-variant">{{ $product->sku }}</div>
                            </td>
                            <td class="text-on-surface-variant">{{ $product->category?->name ?? '—' }}</td>
                            <td class="text-right {{ (int) $product->stock_qty <= 0 ? 'text-error font-semibold' : '' }}">{{ number_format((int) $product->stock_qty) }}</td>
                            <td class="text-right">₹{{ number_format($product->selling_price, 2) }}</td>
                            <td><span class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">No products found.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="card-footer">{{ $products->links() }}</div>
        @endif
    </div>
@endsection
