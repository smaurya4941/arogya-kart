@extends('admin.reports.pdf._layout')

@section('doc-title', 'GST Report')

@section('body')
<table class="summary">
    <tr>
        <td><div class="label">Output GST (Sales)</div><div class="value">Rs. {{ number_format($gst['output_tax'], 2) }}</div></td>
        <td><div class="label">Input GST (Purchases)</div><div class="value">Rs. {{ number_format($gst['input_tax'], 2) }}</div></td>
        <td><div class="label">Net GST Payable</div><div class="value">Rs. {{ number_format($gst['net_payable'], 2) }}</div></td>
    </tr>
</table>

<h3 style="margin:14px 0 0; font-size:12px;">Output Tax &mdash; Sales</h3>
<table>
    <thead>
        <tr><th>GST Slab</th><th class="right">Taxable Value</th><th class="right">Tax</th></tr>
    </thead>
    <tbody>
        @forelse($gst['output_slabs'] as $slab)
            <tr>
                <td>{{ number_format((float) $slab->rate, 2) }}%</td>
                <td class="right">{{ number_format((float) $slab->taxable_total - (float) $slab->tax_amount, 2) }}</td>
                <td class="right">{{ number_format((float) $slab->tax_amount, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="3">No taxable sales in this period.</td></tr>
        @endforelse
    </tbody>
    @if($gst['output_slabs']->count())
        <tfoot><tr><td colspan="2">Total Output GST</td><td class="right">{{ number_format($gst['output_tax'], 2) }}</td></tr></tfoot>
    @endif
</table>

<h3 style="margin:14px 0 0; font-size:12px;">Input Tax &mdash; Purchases</h3>
<table>
    <thead>
        <tr><th>GST Slab</th><th class="right">Taxable Value</th><th class="right">Tax</th></tr>
    </thead>
    <tbody>
        @forelse($gst['input_slabs'] as $slab)
            <tr>
                <td>{{ number_format((float) $slab->rate, 2) }}%</td>
                <td class="right">{{ number_format((float) $slab->taxable_total - (float) $slab->tax_amount, 2) }}</td>
                <td class="right">{{ number_format((float) $slab->tax_amount, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="3">No taxable purchases in this period.</td></tr>
        @endforelse
    </tbody>
    @if($gst['input_slabs']->count())
        <tfoot><tr><td colspan="2">Total Input GST</td><td class="right">{{ number_format($gst['input_tax'], 2) }}</td></tr></tfoot>
    @endif
</table>
@endsection
