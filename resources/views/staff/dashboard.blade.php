@extends('layouts.dashboard')

@section('title', 'Staff Dashboard')
@section('subtitle', 'Keep billing, counter operations, and inventory lookup fast and accurate.')

@section('overview')
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <x-card title="Shift Status" value="Active" description="Ready for pharmacy floor operations" />
        <x-card title="Inventory Access" value="Enabled" description="Search products and batch availability" />
        <x-card title="Billing Module" value="Upcoming" description="POS tools can be connected next" />
    </div>
@endsection

@section('dashboard-content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Staff Workspace</h2>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            This workspace is ready for billing, stock lookup, and day-to-day pharmacy operations. As we continue, we can plug in POS,
            invoice history, and controlled inventory actions without changing the shared layout architecture.
        </p>
    </section>
@endsection
