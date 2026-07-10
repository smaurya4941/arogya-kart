@extends('layouts.admin')

@section('title', 'Edit Team Member')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <h1 class="text-2xl font-bold mb-6">Edit {{ $member->name }}</h1>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.team.update', $member) }}">
            @csrf @method('PUT')
            @include('admin.team._form')
        </form>
    </div>
</div>
@endsection
