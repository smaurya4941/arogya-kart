@extends('layouts.superadmin')

@section('title', 'Edit ' . $plan->name)

@section('content')
    <div class="max-w-2xl bg-white rounded-2xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('superadmin.plans.update', $plan) }}">
            @csrf @method('PUT')
            @include('superadmin.plans._form')
        </form>
    </div>
@endsection
