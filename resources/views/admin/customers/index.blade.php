@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Customers</h1>
            <p class="page-subtitle">People you sell to — their contact details and purchase history.</p>
        </div>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">person_add</span> Add Customer
        </a>
    </div>

    <form method="GET" action="{{ route('admin.customers.index') }}" class="card card-pad">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div class="md:col-span-3">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, phone or email" class="form-input">
            </div>
            <div class="flex items-end gap-2">
                <button class="btn btn-primary btn-sm">Apply</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th class="text-right">Purchases</th>
                        <th class="text-right">Lifetime Value</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td class="font-medium">{{ $customer->name }}</td>
                            <td class="text-on-surface-variant">{{ $customer->phone ?? '-' }}</td>
                            <td class="text-on-surface-variant">{{ $customer->email ?? '-' }}</td>
                            <td class="text-right">{{ $customer->sales_count }}</td>
                            <td class="text-right font-semibold">₹{{ number_format((float) $customer->sales_total, 2) }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a class="btn-icon" title="View" href="{{ route('admin.customers.show', $customer) }}">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a class="btn-icon" title="Edit" href="{{ route('admin.customers.edit', $customer) }}">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">groups</span>
                                    No customers yet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="card-footer">{{ $customers->links() }}</div>
        @endif
    </div>
</div>
@endsection
