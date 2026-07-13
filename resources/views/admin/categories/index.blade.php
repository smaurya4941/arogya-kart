@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Categories</h1>
            <p class="page-subtitle">Manage product categories to organize your inventory.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">add</span> Add Category
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4 text-green-600 font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4 text-error font-semibold">{{ session('error') }}</div>
    @endif

    <div class="card overflow-hidden">
        <div class="card-header">
            <h2 class="section-title">Category List</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Products Count</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="font-medium">{{ $category->name }}</td>
                            <td class="text-on-surface-variant">{{ $category->slug }}</td>
                            <td>
                                <span class="badge badge-neutral">{{ $category->products_count }}</span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a class="btn-icon" title="Edit" href="{{ route('admin.categories.edit', $category) }}">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon text-error hover:bg-error/10 hover:text-error" title="Delete">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">category</span>
                                    No categories found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="card-footer">{{ $categories->links() }}</div>
        @endif
    </div>
</div>
@endsection
