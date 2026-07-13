@extends('layouts.superadmin')

@section('title', 'Operations · Customers')

@section('content')
    @include('superadmin.operations._tabs')

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap gap-2">
                @include('superadmin.operations._tenant_select')
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, phone, email…" class="form-input min-w-[200px] flex-1">
                <button class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Pharmacy</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th class="text-right">Sales</th>
                        <th class="text-right">Outstanding</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td class="text-on-surface-variant">{{ $customer->pharmacy?->name ?? '—' }}</td>
                            <td class="font-medium">{{ $customer->name }}</td>
                            <td class="text-on-surface-variant">{{ $customer->phone ?? '—' }}</td>
                            <td class="text-on-surface-variant">{{ $customer->email ?? '—' }}</td>
                            <td class="text-right text-on-surface-variant">{{ number_format($customer->sales_count) }}</td>
                            <td class="text-right {{ (float) $customer->outstanding_balance > 0 ? 'text-error font-semibold' : '' }}">₹{{ number_format($customer->outstanding_balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">No customers found.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="card-footer">{{ $customers->links() }}</div>
        @endif
    </div>
@endsection
