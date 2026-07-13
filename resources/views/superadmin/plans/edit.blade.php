@extends('layouts.superadmin')

@section('title', 'Edit ' . $plan->name)

@section('content')
    <div class="card card-pad max-w-2xl">
        <form method="POST" action="{{ route('superadmin.plans.update', $plan) }}">
            @csrf @method('PUT')
            @include('superadmin.plans._form')
        </form>
    </div>
@endsection
