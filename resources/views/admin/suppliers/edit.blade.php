@extends('layouts.admin')

@section('title', 'Edit Supplier')

@section('content')
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Edit Supplier</h1>
    </div>

    <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" class="card card-pad space-y-4">
        @csrf
        @method('PUT')
        @include('admin.suppliers._form')

        <div class="flex gap-2 pt-2">
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
