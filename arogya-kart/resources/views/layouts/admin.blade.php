@extends('layouts.app')

@section('sidebar')
<div class="bg-emerald-700 text-white min-h-screen p-4 w-64">
    <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>

    <ul class="space-y-3">
        <li><a href="/admin/dashboard">Dashboard</a></li>
        <li><a href="#">Manage Products</a></li>
        <li><a href="#">Manage Staff</a></li>
        <li><a href="#">Reports</a></li>
    </ul>
</div>

@endsection
