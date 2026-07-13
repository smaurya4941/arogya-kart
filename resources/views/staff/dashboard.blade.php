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
    <section class="card card-pad">
        <h2 class="section-title mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">

            @can('create', \App\Models\Sale::class)
                <a href="{{ route('admin.pos.index') }}" class="flex items-center gap-3 rounded-xl border border-outline-variant/40 p-4 transition hover:border-primary hover:bg-primary/5">
                    <span class="icon-tile bg-primary/10 text-primary"><span class="material-symbols-outlined text-[20px]">point_of_sale</span></span>
                    <div>
                        <p class="font-semibold text-on-surface">New Sale (POS)</p>
                        <p class="text-xs text-on-surface-variant">Bill a customer</p>
                    </div>
                </a>
            @endcan

            @can('viewAny', \App\Models\Sale::class)
                <a href="{{ route('admin.sales.index') }}" class="flex items-center gap-3 rounded-xl border border-outline-variant/40 p-4 transition hover:border-primary hover:bg-primary/5">
                    <span class="icon-tile bg-primary/10 text-primary"><span class="material-symbols-outlined text-[20px]">receipt_long</span></span>
                    <div>
                        <p class="font-semibold text-on-surface">Sales History</p>
                        <p class="text-xs text-on-surface-variant">View past bills</p>
                    </div>
                </a>
            @endcan

            @can('viewAny', \App\Models\Product::class)
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 rounded-xl border border-outline-variant/40 p-4 transition hover:border-primary hover:bg-primary/5">
                    <span class="icon-tile bg-primary/10 text-primary"><span class="material-symbols-outlined text-[20px]">inventory_2</span></span>
                    <div>
                        <p class="font-semibold text-on-surface">Inventory</p>
                        <p class="text-xs text-on-surface-variant">Look up medicines &amp; stock</p>
                    </div>
                </a>
            @endcan

        </div>

        @unless(auth()->user()->can('create', \App\Models\Sale::class) || auth()->user()->can('viewAny', \App\Models\Product::class))
            <p class="mt-4 text-sm text-on-surface-variant">Your account doesn't have any operational tools assigned yet. Ask your pharmacy owner to update your position.</p>
        @endunless
    </section>
@endsection
