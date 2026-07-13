@extends('layouts.admin')

@section('title', 'Add Customer')

@section('content')
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Add Customer</h1>
    </div>

    <form method="POST" action="{{ route('admin.customers.store') }}" class="card card-pad space-y-4">
        @csrf
        @include('admin.customers._form')

        <div class="flex gap-2 pt-2">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
