@extends('layouts.admin')

@section('title', 'Purchase Report')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Purchase Report</h1>
    <p class="text-sm text-gray-600">
        {{ $start->format('d M Y') }} &ndash; {{ $end->format('d M Y') }}
    </p>
</div>

@include('admin.reports._filters', ['action' => 'admin.reports.purchases', 'pdfRoute' => 'admin.reports.purchases.pdf'])

<div class="grid grid-cols-2 gap-4 mb-6 max-w-md">
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Invoices</p>
        <p class="text-2xl font-bold mt-1">{{ number_format($summary['invoices']) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Total Purchases</p>
        <p class="text-2xl font-bold mt-1">₹{{ number_format($summary['total'], 2) }}</p>
    </div>
</div>

<div class="bg-white rounded shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">Invoice</th>
                    <th class="p-3">Supplier Inv.</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Supplier</th>
                    <th class="p-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                    <tr class="border-t">
                        <td class="p-3 font-medium">
                            <a class="text-emerald-700 hover:underline" href="{{ route('admin.purchases.show', $purchase) }}">{{ $purchase->invoice_number }}</a>
                        </td>
                        <td class="p-3 text-gray-600">{{ $purchase->supplier_invoice_number ?? '-' }}</td>
                        <td class="p-3 text-gray-600">{{ $purchase->purchase_date->format('d M Y') }}</td>
                        <td class="p-3 text-gray-600">{{ $purchase->supplier->name ?? '-' }}</td>
                        <td class="p-3 text-right font-medium">₹{{ number_format((float) $purchase->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-gray-600" colspan="5">No purchases in this period.</td></tr>
                @endforelse
            </tbody>
            @if($purchases->count())
                <tfoot class="bg-gray-50 font-semibold">
                    <tr class="border-t">
                        <td class="p-3" colspan="4">Total (this period)</td>
                        <td class="p-3 text-right">₹{{ number_format($summary['total'], 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
    <div class="p-4">
        {{ $purchases->links() }}
    </div>
</div>
@endsection
