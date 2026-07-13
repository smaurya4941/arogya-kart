@extends('layouts.superadmin')

@section('title', 'New Subscription')

@section('content')
    <a href="{{ route('superadmin.subscriptions.index') }}" class="text-sm font-medium text-primary hover:underline">&larr; Back to subscriptions</a>

    <div class="card card-pad mt-4 max-w-2xl">
        <form method="POST" action="{{ route('superadmin.subscriptions.store') }}">
            @csrf
            @include('superadmin.subscriptions._form')
        </form>
    </div>
@endsection
