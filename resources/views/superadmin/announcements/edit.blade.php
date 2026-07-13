@extends('layouts.superadmin')

@section('title', 'Edit Announcement')

@section('content')
    <a href="{{ route('superadmin.announcements.index') }}" class="text-sm font-medium text-primary hover:underline">&larr; Back to announcements</a>
    <div class="card card-pad mt-4 max-w-2xl">
        <form method="POST" action="{{ route('superadmin.announcements.update', $announcement) }}">
            @csrf @method('PUT')
            @include('superadmin.announcements._form')
        </form>
    </div>
@endsection
