@extends('layouts.dashboard')

@section('title', 'Client Dashboard')
@section('subtitle', 'A future-ready portal for orders, prescriptions, and delivery tracking.')

@section('overview')
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <x-card title="Orders" value="0" description="Order history will appear here" />
        <x-card title="Prescriptions" value="0" description="Uploaded prescriptions can be managed here" />
        <x-card title="Support Status" value="Available" description="Client service module can be connected later" />
    </div>
@endsection

@section('dashboard-content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Client Experience Foundation</h2>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            The client area now shares the same production-ready shell as admin and staff. That gives you a clean path to add online
            ordering, prescription uploads, and delivery visibility without rebuilding the UI layer later.
        </p>
    </section>
@endsection
