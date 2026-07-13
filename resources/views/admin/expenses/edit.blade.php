@extends('layouts.admin')

@section('title', 'Edit Expense')

@section('content')
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Edit Expense</h1>
    </div>

    <form method="POST" action="{{ route('admin.expenses.update', $expense) }}" enctype="multipart/form-data" class="card card-pad space-y-4">
        @csrf
        @method('PUT')
        @include('admin.expenses._form')

        <div class="flex gap-2 pt-2">
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.expenses.show', $expense) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
