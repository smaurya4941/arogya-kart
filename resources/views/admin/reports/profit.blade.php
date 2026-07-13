@extends('layouts.admin')

@section('title', 'Profit & Loss')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Profit &amp; Loss</h1>
            <p class="page-subtitle">{{ $start->format('d M Y') }} &ndash; {{ $end->format('d M Y') }}</p>
        </div>
    </div>

    @include('admin.reports._filters', ['action' => 'admin.reports.profit'])

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Net Revenue</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹{{ number_format($pnl['revenue'], 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Gross Profit</p>
            <p class="mt-1 text-2xl font-bold text-tertiary">₹{{ number_format($pnl['gross_profit'], 2) }}</p>
            <p class="mt-1 text-xs text-on-surface-variant">Margin {{ number_format($pnl['gross_margin'], 1) }}%</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Net Profit</p>
            <p class="mt-1 text-2xl font-bold {{ $pnl['net_profit'] >= 0 ? 'text-tertiary' : 'text-error' }}">₹{{ number_format($pnl['net_profit'], 2) }}</p>
            <p class="mt-1 text-xs text-on-surface-variant">Margin {{ number_format($pnl['net_margin'], 1) }}%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="card-header"><h2 class="section-title">Statement</h2></div>
            <dl class="divide-y divide-outline-variant/20 text-sm">
                <div class="flex justify-between px-4 py-3"><dt class="text-on-surface-variant">Gross Sales</dt><dd>₹{{ number_format($pnl['gross_sales'], 2) }}</dd></div>
                <div class="flex justify-between px-4 py-3"><dt class="text-on-surface-variant">Less: Tax Collected (pass-through)</dt><dd>&minus;₹{{ number_format($pnl['tax_collected'], 2) }}</dd></div>
                <div class="flex justify-between px-4 py-3 font-medium"><dt>Net Revenue</dt><dd>₹{{ number_format($pnl['revenue'], 2) }}</dd></div>
                <div class="flex justify-between px-4 py-3"><dt class="text-on-surface-variant">Less: Cost of Goods Sold</dt><dd>&minus;₹{{ number_format($pnl['cogs'], 2) }}</dd></div>
                <div class="flex justify-between px-4 py-3 font-medium"><dt>Gross Profit</dt><dd class="text-tertiary">₹{{ number_format($pnl['gross_profit'], 2) }}</dd></div>
                <div class="flex justify-between px-4 py-3"><dt class="text-on-surface-variant">Less: Operating Expenses</dt><dd>&minus;₹{{ number_format($pnl['expenses_total'], 2) }}</dd></div>
                <div class="flex justify-between bg-surface-container-low/60 px-4 py-3 text-base font-bold">
                    <dt>Net Profit</dt>
                    <dd class="{{ $pnl['net_profit'] >= 0 ? 'text-tertiary' : 'text-error' }}">₹{{ number_format($pnl['net_profit'], 2) }}</dd>
                </div>
            </dl>
        </div>

        <div class="card overflow-hidden">
            <div class="card-header"><h2 class="section-title">Expenses by Category</h2></div>
            <table class="table-saas">
                <tbody>
                    @forelse($pnl['expenses_by_category'] as $row)
                        <tr>
                            <td>{{ $row->category }}</td>
                            <td class="text-right">₹{{ number_format((float) $row->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-on-surface-variant">No expenses recorded in this period.</td></tr>
                    @endforelse
                </tbody>
                @if($pnl['expenses_by_category']->count())
                    <tfoot class="bg-surface-container-low/60 font-semibold">
                        <tr><td class="px-4 py-3">Total</td><td class="px-4 py-3 text-right">₹{{ number_format($pnl['expenses_total'], 2) }}</td></tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
