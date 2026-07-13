@extends('layouts.admin')

@section('title', 'Record Expense')

@section('content')
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Record Expense</h1>
    </div>

    <form method="POST" action="{{ route('admin.expenses.store') }}" enctype="multipart/form-data" class="card card-pad space-y-4">
        @csrf
        @include('admin.expenses._form')

        <div class="flex gap-2 pt-2">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
