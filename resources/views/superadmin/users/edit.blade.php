@extends('layouts.superadmin')

@section('title', 'Edit User')

@section('content')
    <a href="{{ route('superadmin.users.index') }}" class="text-sm font-medium text-primary hover:underline">&larr; Back to users</a>

    <div class="card card-pad mt-4 max-w-2xl">
        <form method="POST" action="{{ route('superadmin.users.update', $user) }}">
            @csrf @method('PUT')
            @include('superadmin.users._form')
        </form>
    </div>
@endsection
