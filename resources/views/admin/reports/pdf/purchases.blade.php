@extends('admin.reports.pdf._layout')

@section('doc-title', 'Purchase Report')

@section('body')
<table class="summary">
    <tr>
        <td><div class="label">Invoices</div><div class="value">{{ number_format($summary['invoices']) }}</div></td>
        <td><div class="label">Total Purchases</div><div class="value">Rs. {{ number_format($summary['total'], 2) }}</div></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>Invoice</th>
            <th>Supplier Inv.</th>
            <th>Date</th>
            <th>Supplier</th>
            <th class="right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($purchases as $purchase)
            <tr>
                <td>{{ $purchase->invoice_number }}</td>
                <td>{{ $purchase->supplier_invoice_number ?? '-' }}</td>
                <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                <td>{{ $purchase->supplier->name ?? '-' }}</td>
                <td class="right">{{ number_format((float) $purchase->total_amount, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="5">No purchases in this period.</td></tr>
        @endforelse
    </tbody>
    @if($purchases->count())
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td class="right">{{ number_format($summary['total'], 2) }}</td>
            </tr>
        </tfoot>
    @endif
</table>
@endsection
