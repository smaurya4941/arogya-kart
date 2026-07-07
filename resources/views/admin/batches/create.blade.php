@extends('layouts.admin')

@section('title', 'Add Batch')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold mb-4">Add Batch for {{ $product->name }}</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <div class="font-semibold mb-2">Please fix the following:</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.batches.store', $product) }}" class="bg-white shadow rounded p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Batch Number</label>
            <input type="text" name="batch_number" value="{{ old('batch_number') }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Expiry Date</label>
            <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Purchase Price</label>
                <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">MRP</label>
                <input type="number" step="0.01" name="mrp" value="{{ old('mrp') }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Quantity</label>
            <input type="number" name="quantity" min="0" value="{{ old('quantity') }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="flex gap-3">
            <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
                Save Batch
            </button>
            <a href="{{ route('admin.products.show', $product) }}" class="px-4 py-2 rounded border">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
