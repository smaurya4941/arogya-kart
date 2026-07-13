@extends('layouts.admin')

@section('title', 'Purchases')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Purchases</h1>
            <p class="page-subtitle">Goods received from suppliers. Recording a purchase stocks in new batches.</p>
        </div>
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">add</span> New Purchase
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.purchases.index') }}" class="card card-pad">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Invoice number" class="form-input">
            </div>
            <div>
                <label class="form-label">Supplier</label>
                <select name="supplier_id" class="form-select">
                    <option value="">All</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="btn btn-primary btn-sm">Apply</button>
                <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Supplier</th>
                        <th>Supplier Inv #</th>
                        <th>Date</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="font-medium">{{ $invoice->invoice_number }}</td>
                            <td class="text-on-surface-variant">{{ $invoice->supplier?->name ?? '-' }}</td>
                            <td class="text-on-surface-variant">{{ $invoice->supplier_invoice_number ?? '-' }}</td>
                            <td class="text-on-surface-variant">{{ $invoice->purchase_date->format('M d, Y') }}</td>
                            <td class="text-right font-semibold">₹{{ number_format($invoice->total_amount, 2) }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a class="btn-icon" title="View" href="{{ route('admin.purchases.show', $invoice) }}">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
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
        @if($invoices->hasPages())
            <div class="card-footer">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>
@endsection
