@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Create Product</h1>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-error/30 bg-error-container/40 p-3 text-sm text-on-error-container">
            <div class="mb-1 font-semibold">Please fix the following:</div>
            <ul class="list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="card card-pad space-y-4">
        @csrf

        <div>
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
        </div>
        <div>
            <label class="form-label">SKU</label>
            <input type="text" name="sku" value="{{ old('sku') }}" class="form-input" required>
        </div>
        <div>
            <label class="form-label">Barcode</label>
            <input type="text" name="barcode" value="{{ old('barcode') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-textarea">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="form-label">Drug Type</label>
            <input type="text" name="drug_type" value="{{ old('drug_type') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-input h-auto py-1.5">
        </div>

        <div class="flex gap-2 pt-2">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
