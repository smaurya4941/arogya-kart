@extends('layouts.admin')

@section('title', 'Edit Customer')

@section('content')
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Edit Customer</h1>
    </div>

    <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="card card-pad space-y-4">
        @csrf
        @method('PUT')
        @include('admin.customers._form')

        <div class="flex gap-2 pt-2">
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
