@extends('layouts.app')

@section('sidebar')
<div class="w-64 bg-gray-700 text-white min-h-screen p-5">
    <h2 class="text-xl font-bold mb-6">Client Panel</h2>

    <ul class="space-y-3">
        <li><a href="/client/dashboard">Dashboard</a></li>
        <li><a href="#">My Orders</a></li>
    </ul>
</div>
@endsection