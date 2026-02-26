@extends('layouts.app')

@section('sidebar')
<div class="w-64 bg-blue-600 text-white min-h-screen p-5">
    <h2 class="text-xl font-bold mb-6">Staff Panel</h2>

    <ul class="space-y-3">
        <li><a href="/staff/dashboard">Dashboard</a></li>
        <li><a href="#">Billing</a></li>
        <li><a href="#">Inventory</a></li>
    </ul>
</div>
@endsection