@extends('layouts.admin')

@section('title', 'Edit Customer')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold mb-4">Edit Customer</h1>

    <form method="POST" action="{{ route('admin.customers.update', $customer) }}"
          class="bg-white shadow rounded p-6 space-y-4">
        @csrf
        @method('PUT')
        @include('admin.customers._form')

        <div class="flex gap-3 pt-2">
            <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Update</button>
            <a href="{{ route('admin.customers.show', $customer) }}" class="px-4 py-2 rounded border">Cancel</a>
        </div>
    </form>
</div>
@endsection
