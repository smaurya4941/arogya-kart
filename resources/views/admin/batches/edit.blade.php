@extends('layouts.admin')

@section('title', 'Edit Batch')

@section('content')
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Batch {{ $batch->batch_number }}</h1>
            <p class="page-subtitle">Product: {{ $batch->product->name }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-error/30 bg-error-container/40 p-3 text-sm text-on-error-container">
            <div class="mb-1 font-semibold">Please fix the following:</div>
            <ul class="list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.batches.update', $batch) }}" class="card card-pad space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="form-label">Batch Number</label>
            <input type="text" name="batch_number" value="{{ old('batch_number', $batch->batch_number) }}" class="form-input" required>
        </div>
        <div>
            <label class="form-label">Expiry Date</label>
            <input type="date" name="expiry_date" value="{{ old('expiry_date', $batch->expiry_date->format('Y-m-d')) }}" class="form-input" required>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="form-label">Purchase Price</label>
                <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', $batch->purchase_price) }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">MRP</label>
                <input type="number" step="0.01" name="mrp" value="{{ old('mrp', $batch->mrp) }}" class="form-input" required>
            </div>
        </div>
        <div>
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" min="0" value="{{ old('quantity', $batch->quantity) }}" class="form-input" required>
        </div>

        <div class="flex gap-2 pt-2">
            <button class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.products.show', $batch->product) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
