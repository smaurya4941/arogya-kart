@extends('layouts.admin')

@section('title', 'Sales Report')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Sales Report</h1>
            <p class="page-subtitle">{{ $start->format('d M Y') }} &ndash; {{ $end->format('d M Y') }}</p>
        </div>
    </div>

    @include('admin.reports._filters', ['action' => 'admin.reports.sales', 'pdfRoute' => 'admin.reports.sales.pdf'])

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Invoices</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">{{ number_format($summary['invoices']) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Gross Sales</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹{{ number_format($summary['total'], 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Collected</p>
            <p class="mt-1 text-2xl font-bold text-tertiary">₹{{ number_format($summary['paid'], 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Outstanding Due</p>
            <p class="mt-1 text-2xl font-bold text-error">₹{{ number_format($summary['due'], 2) }}</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Method</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Tax</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Due</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td class="font-medium">
                                <a class="text-primary hover:underline" href="{{ route('admin.sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
                            </td>
                            <td class="text-on-surface-variant">{{ $sale->sale_date->format('d M Y') }}</td>
                            <td class="text-on-surface-variant">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td class="capitalize text-on-surface-variant">{{ $sale->payment_method }}</td>
                            <td class="text-right">₹{{ number_format((float) $sale->subtotal, 2) }}</td>
                            <td class="text-right">₹{{ number_format((float) $sale->tax_amount, 2) }}</td>
                            <td class="text-right font-medium">₹{{ number_format((float) $sale->total_amount, 2) }}</td>
                            <td class="text-right {{ (float) $sale->due_amount > 0 ? 'text-error' : 'text-outline' }}">₹{{ number_format((float) $sale->due_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">monitoring</span>
                                    No sales in this period.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($sales->count())
                    <tfoot class="bg-surface-container-low/60 font-semibold">
                        <tr>
                            <td class="px-4 py-3" colspan="4">Totals (this period)</td>
                            <td class="px-4 py-3 text-right">₹{{ number_format($summary['subtotal'], 2) }}</td>
                            <td class="px-4 py-3 text-right">₹{{ number_format($summary['tax'], 2) }}</td>
                            <td class="px-4 py-3 text-right">₹{{ number_format($summary['total'], 2) }}</td>
                            <td class="px-4 py-3 text-right">₹{{ number_format($summary['due'], 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        @if($sales->hasPages())
            <div class="card-footer">{{ $sales->links() }}</div>
        @endif
    </div>
</div>
@endsection
