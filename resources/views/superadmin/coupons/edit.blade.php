@extends('layouts.superadmin')

@section('title', 'Edit Coupon')

@section('content')
    <a href="{{ route('superadmin.coupons.index') }}" class="text-sm font-medium text-primary hover:underline">&larr; Back to coupons</a>
    <div class="card card-pad mt-4 max-w-2xl">
        <form method="POST" action="{{ route('superadmin.coupons.update', $coupon) }}">
            @csrf @method('PUT')
            @include('superadmin.coupons._form')
        </form>
    </div>
@endsection
