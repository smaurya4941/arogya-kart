@extends('layouts.superadmin')

@section('title', 'Onboard Pharmacy')

@section('content')
    <a href="{{ route('superadmin.pharmacies.index') }}" class="text-sm font-medium text-primary hover:underline">&larr; Back to pharmacies</a>

    <div class="card card-pad mt-4 max-w-3xl">
        <form method="POST" action="{{ route('superadmin.pharmacies.store') }}">
            @csrf
            @include('superadmin.pharmacies._form')
        </form>
    </div>
@endsection
