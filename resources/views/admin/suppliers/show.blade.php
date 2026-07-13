@extends('layouts.admin')

@section('title', 'Supplier · ' . $supplier->name)

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $supplier->name }}</h1>
            <p class="page-subtitle">{{ $supplier->company_name ?? 'Supplier details' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad space-y-3">
            <h2 class="section-title border-b border-outline-variant/30 pb-2">Details</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-on-surface-variant">Contact Person</dt><dd>{{ $supplier->contact_person ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Phone</dt><dd>{{ $supplier->phone ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Email</dt><dd>{{ $supplier->email ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">GST</dt><dd>{{ $supplier->gst_number ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">City / State</dt><dd>{{ trim(($supplier->city ?? '') . ' ' . ($supplier->state ?? '')) ?: '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Status</dt><dd>{!! $supplier->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-neutral">Inactive</span>' !!}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Outstanding</dt><dd class="font-semibold">₹{{ number_format($supplier->balance, 2) }}</dd></div>
            </dl>
            @if($supplier->address)
                <div class="pt-2 text-sm">
                    <p class="text-on-surface-variant">Address</p>
                    <p>{{ $supplier->address }}</p>
                </div>
            @endif
        </div>

        <div class="card overflow-hidden lg:col-span-2">
            <div class="card-header">
                <h2 class="section-title">Recent Purchases ({{ $supplier->purchase_invoices_count }})</h2>
                <a href="{{ route('admin.purchases.create', ['supplier_id' => $supplier->id]) }}" class="btn btn-primary btn-sm">
                    <span class="material-symbols-outlined text-[16px]">add</span> New Purchase
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPurchases as $purchase)
                            <tr>
                                <td class="font-medium">{{ $purchase->invoice_number }}</td>
                                <td class="text-on-surface-variant">{{ $purchase->purchase_date->format('M d, Y') }}</td>
                                <td class="text-right font-semibold">₹{{ number_format($purchase->total_amount, 2) }}</td>
                                <td class="text-right">
                                    <a class="btn-icon ml-auto" title="View" href="{{ route('admin.purchases.show', $purchase) }}">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <span class="material-symbols-outlined text-[32px] opacity-40">shopping_cart_checkout</span>
                                        No purchases recorded yet.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}"
          onsubmit="return confirm('Delete this supplier?');">
        @csrf
        @method('DELETE')
        <button class="text-sm font-medium text-error hover:underline">Delete supplier</button>
    </form>
</div>
@endsection
