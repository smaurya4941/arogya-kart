@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')
@section('subtitle', 'Track live pharmacy operations, stock health, and upcoming inventory risk.')

@section('actions')
    <a
        href="{{ route('admin.products.create') }}"
        class="inline-flex rounded-2xl bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
    >
        Add Product
    </a>
@endsection

@section('overview')
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
        <x-card title="Total Products" :value="$totalProducts" description="Medicines currently in catalog" />
        <x-card title="Total Stock" :value="$totalStock" description="Units available across all active batches" />
        <x-card title="Expiring Soon" :value="$expiringSoon" description="Batches expiring within configured alert window" />
        <x-card title="Low Stock" :value="$lowStockProducts" description="Products at or below the stock threshold" />
    </div>
@endsection

@section('dashboard-content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-3xl border border-amber-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Expiry Alerts</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ $expiringSoon }} batches are expiring in the next {{ $expiringDays }} days.
                    </p>
                </div>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">
                    Action Needed
                </span>
            </div>

            <a
                href="{{ route('admin.products.index', ['q' => '', 'drug_type' => '', 'sku' => '']) }}"
                class="mt-5 inline-flex text-sm font-semibold text-amber-700 transition hover:text-amber-800"
            >
                Review expiring items
            </a>
        </section>

        <section class="rounded-3xl border border-rose-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Low Stock Alerts</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ $lowStockProducts }} products are at or below {{ $lowStockThreshold }} units.
                    </p>
                </div>
                <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-rose-700">
                    Monitor
                </span>
            </div>

            <a
                href="{{ route('admin.products.index') }}"
                class="mt-5 inline-flex text-sm font-semibold text-rose-700 transition hover:text-rose-800"
            >
                View low stock products
            </a>
        </section>
    </div>
@endsection
