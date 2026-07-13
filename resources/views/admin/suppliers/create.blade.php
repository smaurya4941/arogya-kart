@extends('layouts.admin')

@section('title', 'Add Supplier')

@section('content')
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Add Supplier</h1>
    </div>

    <form method="POST" action="{{ route('admin.suppliers.store') }}" class="card card-pad space-y-4">
        @csrf
        @include('admin.suppliers._form')

        <div class="flex gap-2 pt-2">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
