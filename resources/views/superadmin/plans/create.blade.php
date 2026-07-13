@extends('layouts.superadmin')

@section('title', 'New Plan')

@section('content')
    <div class="card card-pad max-w-2xl">
        <form method="POST" action="{{ route('superadmin.plans.store') }}">
            @csrf
            @include('superadmin.plans._form')
        </form>
    </div>
@endsection
