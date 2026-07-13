@extends('layouts.admin')

@section('title', 'Add Team Member')

@section('content')
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Add Team Member</h1>
    </div>
    <div class="card card-pad">
        <form method="POST" action="{{ route('admin.team.store') }}">
            @csrf
            @include('admin.team._form')
        </form>
    </div>
</div>
@endsection
