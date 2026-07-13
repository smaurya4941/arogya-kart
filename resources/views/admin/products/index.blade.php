@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Products</h1>
            <p class="page-subtitle">Manage catalog items and batch inventory.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">add</span> Add Product
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.products.index') }}" class="card card-pad">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
            <div class="md:col-span-2">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, SKU, or Barcode" class="form-input">
            </div>
            <div>
                <label class="form-label">SKU</label>
                <input type="text" name="sku" value="{{ request('sku') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Drug Type</label>
                <input type="text" name="drug_type" value="{{ request('drug_type') }}" class="form-input">
            </div>
        </div>
        <div class="mt-3 flex gap-2">
            <button class="btn btn-primary btn-sm">Apply</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline btn-sm">Reset</a>
        </div>
    </form>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <!-- Product list -->
        <div class="card col-span-1 overflow-hidden lg:col-span-2">
            <div class="card-header">
                <h2 class="section-title">Product List</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Batches</th>
                            <th>Stock</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="font-medium">{{ $product->name }}</td>
                                <td class="text-on-surface-variant">{{ $product->sku }}</td>
                                <td class="text-on-surface-variant">{{ $product->category?->name ?? '-' }}</td>
                                <td class="text-on-surface-variant">{{ $product->drug_type ?? '-' }}</td>
                                <td>{{ $product->batches_count ?? 0 }}</td>
                                <td>
                                    @php $stock = $product->total_stock ?? 0; @endphp
                                    <span class="badge {{ $stock > 0 ? 'badge-success' : 'badge-danger' }}">{{ $stock }}</span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a class="btn-icon" title="View" href="{{ route('admin.products.show', $product) }}">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>
                                        <a class="btn-icon" title="Edit" href="{{ route('admin.products.edit', $product) }}">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <span class="material-symbols-outlined text-[32px] opacity-40">inventory_2</span>
                                        No products yet.
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

        <!-- Expiring soon -->
        <div class="card col-span-1">
            <div class="card-header">
                <div>
                    <h2 class="section-title">Expiring Soon</h2>
                    <p class="text-xs text-on-surface-variant">Next 30 days</p>
                </div>
            </div>
            <div class="space-y-2 p-3">
                @forelse($expiringBatches as $batch)
                    <div class="rounded-lg border border-outline-variant/30 bg-surface-container-low/50 p-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="truncate text-sm font-semibold text-on-surface">{{ $batch->product->name ?? 'Unknown Product' }}</span>
                            <span class="badge badge-warning">Qty {{ $batch->quantity }}</span>
                        </div>
                        <div class="mt-1 text-xs text-on-surface-variant">
                            Batch {{ $batch->batch_number }} · Exp {{ $batch->expiry_date->format('M d, Y') }}
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <span class="material-symbols-outlined text-[32px] opacity-40">event_available</span>
                        No expiring batches.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
