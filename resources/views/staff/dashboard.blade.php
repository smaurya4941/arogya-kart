@extends('layouts.dashboard')

@section('title', 'Staff Dashboard')
@section('subtitle', 'Keep billing, counter operations, and inventory lookup fast and accurate.')

@section('overview')
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <x-card title="My Sales Today" value="₹{{ number_format($todayRevenue, 2) }}" description="Rung up by you today" />
        <x-card title="My Invoices Today" value="{{ $todayInvoices }}" description="Bills you generated today" />
        <x-card title="Role" value="{{ auth()->user()->roles->pluck('name')->first() ?? 'Staff' }}" description="Your assigned position" />
    </div>
@endsection

@section('dashboard-content')
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            @can('create', \App\Models\Sale::class)
                <a href="{{ route('admin.pos.index') }}" class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 hover:border-emerald-400 hover:bg-emerald-50 transition">
                    <span class="material-symbols-outlined text-emerald-600">point_of_sale</span>
                    <div>
                        <p class="font-semibold text-slate-900">New Sale (POS)</p>
                        <p class="text-xs text-slate-500">Bill a customer</p>
                    </div>
                </a>
            @endcan

            @can('viewAny', \App\Models\Sale::class)
                <a href="{{ route('admin.sales.index') }}" class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 hover:border-emerald-400 hover:bg-emerald-50 transition">
                    <span class="material-symbols-outlined text-emerald-600">receipt_long</span>
                    <div>
                        <p class="font-semibold text-slate-900">Sales History</p>
                        <p class="text-xs text-slate-500">View past bills</p>
                    </div>
                </a>
            @endcan

            @can('viewAny', \App\Models\Product::class)
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 hover:border-emerald-400 hover:bg-emerald-50 transition">
                    <span class="material-symbols-outlined text-emerald-600">inventory_2</span>
                    <div>
                        <p class="font-semibold text-slate-900">Inventory</p>
                        <p class="text-xs text-slate-500">Look up medicines &amp; stock</p>
                    </div>
                </a>
            @endcan

        </div>

        @unless(auth()->user()->can('create', \App\Models\Sale::class) || auth()->user()->can('viewAny', \App\Models\Product::class))
            <p class="mt-4 text-sm text-slate-500">Your account doesn't have any operational tools assigned yet. Ask your pharmacy owner to update your position.</p>
        @endunless
    </section>
@endsection
