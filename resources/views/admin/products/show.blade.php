@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-subtitle">SKU: {{ $product->sku }}</p>
            <div class="mt-1 flex flex-wrap gap-x-4 text-xs text-on-surface-variant">
                <span>Category: {{ $product->category?->name ?? '-' }}</span>
                <span>Barcode: {{ $product->barcode ?? '-' }}</span>
                <span>Drug Type: {{ $product->drug_type ?? '-' }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline">Edit</a>
            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product and its batches?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>

    @if($product->image_path)
        <div class="card card-pad">
            <h2 class="section-title mb-3">Product Image</h2>
            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="max-w-xs rounded-lg">
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="card card-pad">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Total Stock</div>
            <div class="mt-1 text-2xl font-bold text-on-surface">{{ $product->total_stock }}</div>
        </div>
        <div class="card card-pad">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Batches</div>
            <div class="mt-1 text-2xl font-bold text-on-surface">{{ $product->batches->count() }}</div>
        </div>
        <div class="card card-pad">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Expiring Soon</div>
            <div class="mt-1 text-2xl font-bold text-on-surface">{{ $expiringBatches->count() }}</div>
        </div>
    </div>

    <div class="card card-pad">
        <h2 class="section-title mb-3">Issue Stock (FEFO)</h2>
        <form method="POST" action="{{ route('admin.products.issue-stock', $product) }}" class="flex flex-col gap-2 sm:flex-row">
            @csrf
            <input type="number" name="quantity" min="1" placeholder="Quantity" class="form-input w-full sm:w-48" required>
            <button class="btn btn-primary">Issue Stock</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <h2 class="section-title">Batches</h2>
            <a href="{{ route('admin.products.batches.create', $product) }}" class="btn btn-primary btn-sm">
                <span class="material-symbols-outlined text-[16px]">add</span> Add Batch
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Batch</th>
                        <th>Expiry</th>
                        <th>Purchase</th>
                        <th>MRP</th>
                        <th>Qty</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->batches as $batch)
                        <tr>
                            <td class="font-medium">{{ $batch->batch_number }}</td>
                            <td>{{ $batch->expiry_date->format('M d, Y') }}</td>
                            <td>{{ number_format($batch->purchase_price, 2) }}</td>
                            <td>{{ number_format($batch->mrp, 2) }}</td>
                            <td>{{ $batch->quantity }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a class="btn-icon" title="Edit" href="{{ route('admin.batches.edit', $batch) }}">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.batches.destroy', $batch) }}" class="inline" onsubmit="return confirm('Delete this batch?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-icon hover:text-error" title="Delete">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">layers</span>
                                    No batches yet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-pad">
        <h2 class="section-title mb-3">Expiring Batches</h2>
        <div class="space-y-2">
            @forelse($expiringBatches as $batch)
                <div class="rounded-lg border border-outline-variant/30 bg-surface-container-low/50 p-3">
                    <div class="font-medium text-on-surface">Batch {{ $batch->batch_number }}</div>
                    <div class="text-xs text-on-surface-variant">Expiry: {{ $batch->expiry_date->format('M d, Y') }} · Qty: {{ $batch->quantity }}</div>
                </div>
            @empty
                <p class="text-sm text-on-surface-variant">No expiring batches.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
