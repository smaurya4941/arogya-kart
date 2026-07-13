@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Create Category</h1>
            <p class="page-subtitle">Add a new category to organize your products.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="card max-w-2xl">
        @csrf
        <div class="card-pad space-y-4">
            <div>
                <label for="name" class="form-label">Category Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input @error('name') border-error @enderror" required autofocus>
                @error('name')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="card-footer bg-surface-container-lowest flex justify-end gap-2">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Category</button>
        </div>
    </form>
</div>
@endsection
