@extends('layouts.admin')

@section('title', 'Profit & Loss')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Profit &amp; Loss</h1>
    <p class="text-sm text-gray-600">
        {{ $start->format('d M Y') }} &ndash; {{ $end->format('d M Y') }}
    </p>
</div>

@include('admin.reports._filters', ['action' => 'admin.reports.profit'])

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Net Revenue</p>
        <p class="text-2xl font-bold mt-1">₹{{ number_format($pnl['revenue'], 2) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Gross Profit</p>
        <p class="text-2xl font-bold mt-1 text-emerald-600">₹{{ number_format($pnl['gross_profit'], 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Margin {{ number_format($pnl['gross_margin'], 1) }}%</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Net Profit</p>
        <p class="text-2xl font-bold mt-1 {{ $pnl['net_profit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">₹{{ number_format($pnl['net_profit'], 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Margin {{ number_format($pnl['net_margin'], 1) }}%</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white shadow rounded">
        <div class="p-4 border-b font-semibold">Statement</div>
        <dl class="text-sm divide-y">
            <div class="flex justify-between p-3"><dt class="text-gray-600">Gross Sales</dt><dd>₹{{ number_format($pnl['gross_sales'], 2) }}</dd></div>
            <div class="flex justify-between p-3"><dt class="text-gray-600">Less: Tax Collected (pass-through)</dt><dd>&minus;₹{{ number_format($pnl['tax_collected'], 2) }}</dd></div>
            <div class="flex justify-between p-3 font-medium"><dt>Net Revenue</dt><dd>₹{{ number_format($pnl['revenue'], 2) }}</dd></div>
            <div class="flex justify-between p-3"><dt class="text-gray-600">Less: Cost of Goods Sold</dt><dd>&minus;₹{{ number_format($pnl['cogs'], 2) }}</dd></div>
            <div class="flex justify-between p-3 font-medium"><dt>Gross Profit</dt><dd class="text-emerald-600">₹{{ number_format($pnl['gross_profit'], 2) }}</dd></div>
            <div class="flex justify-between p-3"><dt class="text-gray-600">Less: Operating Expenses</dt><dd>&minus;₹{{ number_format($pnl['expenses_total'], 2) }}</dd></div>
            <div class="flex justify-between p-3 font-bold text-base bg-gray-50">
                <dt>Net Profit</dt>
                <dd class="{{ $pnl['net_profit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">₹{{ number_format($pnl['net_profit'], 2) }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white shadow rounded">
        <div class="p-4 border-b font-semibold">Expenses by Category</div>
        <table class="min-w-full text-sm">
            <tbody>
                @forelse($pnl['expenses_by_category'] as $row)
                    <tr class="border-t">
                        <td class="p-3">{{ $row->category }}</td>
                        <td class="p-3 text-right">₹{{ number_format((float) $row->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-gray-600" colspan="2">No expenses recorded in this period.</td></tr>
                @endforelse
            </tbody>
            @if($pnl['expenses_by_category']->count())
                <tfoot class="bg-gray-50 font-semibold">
                    <tr class="border-t"><td class="p-3">Total</td><td class="p-3 text-right">₹{{ number_format($pnl['expenses_total'], 2) }}</td></tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
