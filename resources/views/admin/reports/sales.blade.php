@extends('layouts.admin')

@section('title', 'Sales Report')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Sales Report</h1>
    <p class="text-sm text-gray-600">
        {{ $start->format('d M Y') }} &ndash; {{ $end->format('d M Y') }}
    </p>
</div>

@include('admin.reports._filters', ['action' => 'admin.reports.sales', 'pdfRoute' => 'admin.reports.sales.pdf'])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Invoices</p>
        <p class="text-2xl font-bold mt-1">{{ number_format($summary['invoices']) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Gross Sales</p>
        <p class="text-2xl font-bold mt-1">₹{{ number_format($summary['total'], 2) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Collected</p>
        <p class="text-2xl font-bold mt-1 text-emerald-600">₹{{ number_format($summary['paid'], 2) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Outstanding Due</p>
        <p class="text-2xl font-bold mt-1 text-rose-600">₹{{ number_format($summary['due'], 2) }}</p>
    </div>
</div>

<div class="bg-white rounded shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">Invoice</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Customer</th>
                    <th class="p-3">Method</th>
                    <th class="p-3 text-right">Subtotal</th>
                    <th class="p-3 text-right">Tax</th>
                    <th class="p-3 text-right">Total</th>
                    <th class="p-3 text-right">Due</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr class="border-t">
                        <td class="p-3 font-medium">
                            <a class="text-emerald-700 hover:underline" href="{{ route('admin.sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
                        </td>
                        <td class="p-3 text-gray-600">{{ $sale->sale_date->format('d M Y') }}</td>
                        <td class="p-3 text-gray-600">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        <td class="p-3 text-gray-600 capitalize">{{ $sale->payment_method }}</td>
                        <td class="p-3 text-right">₹{{ number_format((float) $sale->subtotal, 2) }}</td>
                        <td class="p-3 text-right">₹{{ number_format((float) $sale->tax_amount, 2) }}</td>
                        <td class="p-3 text-right font-medium">₹{{ number_format((float) $sale->total_amount, 2) }}</td>
                        <td class="p-3 text-right {{ (float) $sale->due_amount > 0 ? 'text-rose-600' : 'text-gray-400' }}">₹{{ number_format((float) $sale->due_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-gray-600" colspan="8">No sales in this period.</td></tr>
                @endforelse
            </tbody>
            @if($sales->count())
                <tfoot class="bg-gray-50 font-semibold">
                    <tr class="border-t">
                        <td class="p-3" colspan="4">Totals (this period)</td>
                        <td class="p-3 text-right">₹{{ number_format($summary['subtotal'], 2) }}</td>
                        <td class="p-3 text-right">₹{{ number_format($summary['tax'], 2) }}</td>
                        <td class="p-3 text-right">₹{{ number_format($summary['total'], 2) }}</td>
                        <td class="p-3 text-right">₹{{ number_format($summary['due'], 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
    <div class="p-4">
        {{ $sales->links() }}
    </div>
</div>
@endsection
