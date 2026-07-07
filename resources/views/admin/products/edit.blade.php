@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold mb-4">Edit Product</h1>

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

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data"
          class="bg-white shadow rounded p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select name="category_id" class="w-full border rounded px-3 py-2">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">SKU</label>
            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Barcode</label>
            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="4"
                      class="w-full border rounded px-3 py-2">{{ old('description', $product->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Drug Type</label>
            <input type="text" name="drug_type" value="{{ old('drug_type', $product->drug_type) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Image</label>
            <input type="file" name="image" class="w-full border rounded px-3 py-2">
            @if($product->image_path)
                <p class="text-xs text-gray-500 mt-1">Current image: {{ $product->image_path }}</p>
            @endif
        </div>

        <div class="flex gap-3">
            <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
                Save Changes
            </button>
            <a href="{{ route('admin.products.show', $product) }}" class="px-4 py-2 rounded border">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
