@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Category</h1>
            <p class="page-subtitle">Update the details of this category.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="card max-w-2xl">
        @csrf
        @method('PUT')
        <div class="card-pad space-y-4">
            <div>
                <label for="name" class="form-label">Category Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" class="form-input @error('name') border-error @enderror" required autofocus>
                @error('name')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="card-footer bg-surface-container-lowest flex justify-end gap-2">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Category</button>
        </div>
    </form>
</div>
@endsection
