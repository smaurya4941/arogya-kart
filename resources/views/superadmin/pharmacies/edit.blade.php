@extends('layouts.superadmin')

@section('title', 'Edit ' . $pharmacy->name)

@section('content')
    <a href="{{ route('superadmin.pharmacies.show', $pharmacy) }}" class="text-sm font-medium text-primary hover:underline">&larr; Back to pharmacy</a>

    <div class="card card-pad mt-4 max-w-3xl">
        <form method="POST" action="{{ route('superadmin.pharmacies.update', $pharmacy) }}">
            @csrf
            @method('PUT')
            @include('superadmin.pharmacies._form')
        </form>
    </div>
@endsection
