@extends('layouts.admin')

@section('title', 'GST Report')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">GST Report</h1>
            <p class="page-subtitle">{{ $start->format('d M Y') }} &ndash; {{ $end->format('d M Y') }}</p>
        </div>
    </div>

    @include('admin.reports._filters', ['action' => 'admin.reports.gst', 'pdfRoute' => 'admin.reports.gst.pdf'])

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Output GST (on sales)</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹{{ number_format($gst['output_tax'], 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Input GST (on purchases)</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹{{ number_format($gst['input_tax'], 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Net GST Payable</p>
            <p class="mt-1 text-2xl font-bold {{ $gst['net_payable'] >= 0 ? 'text-error' : 'text-tertiary' }}">₹{{ number_format($gst['net_payable'], 2) }}</p>
            @if($gst['net_payable'] < 0)
                <p class="mt-1 text-xs text-on-surface-variant">Input credit carried forward</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="card-header"><h2 class="section-title">Output Tax &mdash; Sales</h2></div>
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>GST Slab</th>
                        <th class="text-right">Taxable Value</th>
                        <th class="text-right">Tax</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gst['output_slabs'] as $slab)
                        <tr>
                            <td>{{ number_format((float) $slab->rate, 2) }}%</td>
                            <td class="text-right">₹{{ number_format((float) $slab->taxable_total - (float) $slab->tax_amount, 2) }}</td>
                            <td class="text-right">₹{{ number_format((float) $slab->tax_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-on-surface-variant">No taxable sales in this period.</td></tr>
                    @endforelse
                </tbody>
                @if($gst['output_slabs']->count())
                    <tfoot class="bg-surface-container-low/60 font-semibold">
                        <tr><td class="px-4 py-3" colspan="2">Total Output GST</td><td class="px-4 py-3 text-right">₹{{ number_format($gst['output_tax'], 2) }}</td></tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="card overflow-hidden">
            <div class="card-header"><h2 class="section-title">Input Tax &mdash; Purchases</h2></div>
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>GST Slab</th>
                        <th class="text-right">Taxable Value</th>
                        <th class="text-right">Tax</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gst['input_slabs'] as $slab)
                        <tr>
                            <td>{{ number_format((float) $slab->rate, 2) }}%</td>
                            <td class="text-right">₹{{ number_format((float) $slab->taxable_total - (float) $slab->tax_amount, 2) }}</td>
                            <td class="text-right">₹{{ number_format((float) $slab->tax_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-on-surface-variant">No taxable purchases in this period.</td></tr>
                    @endforelse
                </tbody>
                @if($gst['input_slabs']->count())
                    <tfoot class="bg-surface-container-low/60 font-semibold">
                        <tr><td class="px-4 py-3" colspan="2">Total Input GST</td><td class="px-4 py-3 text-right">₹{{ number_format($gst['input_tax'], 2) }}</td></tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
