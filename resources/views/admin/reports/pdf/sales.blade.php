@extends('admin.reports.pdf._layout')

@section('doc-title', 'Sales Report')

@section('body')
<table class="summary">
    <tr>
        <td><div class="label">Invoices</div><div class="value">{{ number_format($summary['invoices']) }}</div></td>
        <td><div class="label">Gross Sales</div><div class="value">Rs. {{ number_format($summary['total'], 2) }}</div></td>
        <td><div class="label">Collected</div><div class="value">Rs. {{ number_format($summary['paid'], 2) }}</div></td>
        <td><div class="label">Outstanding Due</div><div class="value">Rs. {{ number_format($summary['due'], 2) }}</div></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>Invoice</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Method</th>
            <th class="right">Subtotal</th>
            <th class="right">Tax</th>
            <th class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $sale)
            <tr>
                <td>{{ $sale->invoice_number }}</td>
                <td>{{ $sale->sale_date->format('d M Y') }}</td>
                <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                <td>{{ ucfirst($sale->payment_method) }}</td>
                <td class="right">{{ number_format((float) $sale->subtotal, 2) }}</td>
                <td class="right">{{ number_format((float) $sale->tax_amount, 2) }}</td>
                <td class="right">{{ number_format((float) $sale->total_amount, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="7">No sales in this period.</td></tr>
        @endforelse
    </tbody>
    @if($sales->count())
        <tfoot>
            <tr>
                <td colspan="4">Totals</td>
                <td class="right">{{ number_format($summary['subtotal'], 2) }}</td>
                <td class="right">{{ number_format($summary['tax'], 2) }}</td>
                <td class="right">{{ number_format($summary['total'], 2) }}</td>
            </tr>
        </tfoot>
    @endif
</table>
@endsection
