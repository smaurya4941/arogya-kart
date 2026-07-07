@extends('layouts.admin')

@section('title', 'Edit Expense')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold mb-4">Edit Expense</h1>

    <form method="POST" action="{{ route('admin.expenses.update', $expense) }}"
          enctype="multipart/form-data"
          class="bg-white shadow rounded p-6 space-y-4">
        @csrf
        @method('PUT')
        @include('admin.expenses._form')

        <div class="flex gap-3 pt-2">
            <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Update</button>
            <a href="{{ route('admin.expenses.show', $expense) }}" class="px-4 py-2 rounded border">Cancel</a>
        </div>
    </form>
</div>
@endsection
