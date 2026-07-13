@php
    $tabs = [
        ['superadmin.operations.index', 'Overview'],
        ['superadmin.operations.products', 'Products'],
        ['superadmin.operations.sales', 'Sales'],
        ['superadmin.operations.purchases', 'Purchases'],
        ['superadmin.operations.customers', 'Customers'],
        ['superadmin.operations.expenses', 'Expenses'],
    ];
@endphp
<div class="mb-4 flex flex-wrap gap-1 border-b border-outline-variant/60">
    @foreach($tabs as [$route, $label])
        @php $active = request()->routeIs($route); @endphp
        <a href="{{ route($route) }}"
           class="-mb-px border-b-2 px-4 py-2 text-sm font-medium transition {{ $active ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface' }}">
            {{ $label }}
        </a>
    @endforeach
</div>
