@extends('layouts.admin')

@section('title', 'Purchase Report')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Purchase Report</h1>
            <p class="page-subtitle">{{ $start->format('d M Y') }} &ndash; {{ $end->format('d M Y') }}</p>
        </div>
    </div>

    @include('admin.reports._filters', ['action' => 'admin.reports.purchases', 'pdfRoute' => 'admin.reports.purchases.pdf'])

    <div class="grid max-w-md grid-cols-2 gap-4">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Invoices</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">{{ number_format($summary['invoices']) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Total Purchases</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹{{ number_format($summary['total'], 2) }}</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Supplier Inv.</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td class="font-medium">
                                <a class="text-primary hover:underline" href="{{ route('admin.purchases.show', $purchase) }}">{{ $purchase->invoice_number }}</a>
                            </td>
                            <td class="text-on-surface-variant">{{ $purchase->supplier_invoice_number ?? '-' }}</td>
                            <td class="text-on-surface-variant">{{ $purchase->purchase_date->format('d M Y') }}</td>
                            <td class="text-on-surface-variant">{{ $purchase->supplier->name ?? '-' }}</td>
                            <td class="text-right font-medium">₹{{ number_format((float) $purchase->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">shopping_cart_checkout</span>
                                    No purchases in this period.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($purchases->count())
                    <tfoot class="bg-surface-container-low/60 font-semibold">
                        <tr>
                            <td class="px-4 py-3" colspan="4">Total (this period)</td>
                            <td class="px-4 py-3 text-right">₹{{ number_format($summary['total'], 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        @if($purchases->hasPages())
            <div class="card-footer">{{ $purchases->links() }}</div>
        @endif
    </div>
</div>
@endsection
