@extends('layouts.admin')

@section('title', 'Add Supplier')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold mb-4">Add Supplier</h1>

    <form method="POST" action="{{ route('admin.suppliers.store') }}"
          class="bg-white shadow rounded p-6 space-y-4">
        @csrf
        @include('admin.suppliers._form')

        <div class="flex gap-3 pt-2">
            <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Save</button>
            <a href="{{ route('admin.suppliers.index') }}" class="px-4 py-2 rounded border">Cancel</a>
        </div>
    </form>
</div>
@endsection
