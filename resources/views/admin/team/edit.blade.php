@extends('layouts.admin')

@section('title', 'Edit Team Member')

@section('content')
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Edit {{ $member->name }}</h1>
    </div>
    <div class="card card-pad">
        <form method="POST" action="{{ route('admin.team.update', $member) }}">
            @csrf @method('PUT')
            @include('admin.team._form')
        </form>
    </div>
</div>
@endsection
