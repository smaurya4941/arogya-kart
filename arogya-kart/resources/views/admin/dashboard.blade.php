@extends('layouts.admin')

@section('content')
<h1 class="text-3xl font-bold mb-4">Admin Dashboard</h1>
<p>Welcome {{ auth()->user()->name }}</p>
@endsection