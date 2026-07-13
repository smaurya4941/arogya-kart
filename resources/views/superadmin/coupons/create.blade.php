@extends('layouts.superadmin')

@section('title', 'New Coupon')

@section('content')
    <a href="{{ route('superadmin.coupons.index') }}" class="text-sm font-medium text-primary hover:underline">&larr; Back to coupons</a>
    <div class="card card-pad mt-4 max-w-2xl">
        <form method="POST" action="{{ route('superadmin.coupons.store') }}">
            @csrf
            @include('superadmin.coupons._form')
        </form>
    </div>
@endsection
