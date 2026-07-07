@extends('layouts.admin')

@section('title', 'GST Report')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">GST Report</h1>
    <p class="text-sm text-gray-600">
        {{ $start->format('d M Y') }} &ndash; {{ $end->format('d M Y') }}
    </p>
</div>

@include('admin.reports._filters', ['action' => 'admin.reports.gst', 'pdfRoute' => 'admin.reports.gst.pdf'])

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Output GST (on sales)</p>
        <p class="text-2xl font-bold mt-1">₹{{ number_format($gst['output_tax'], 2) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Input GST (on purchases)</p>
        <p class="text-2xl font-bold mt-1">₹{{ number_format($gst['input_tax'], 2) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Net GST Payable</p>
        <p class="text-2xl font-bold mt-1 {{ $gst['net_payable'] >= 0 ? 'text-rose-600' : 'text-emerald-600' }}">₹{{ number_format($gst['net_payable'], 2) }}</p>
        @if($gst['net_payable'] < 0)
            <p class="text-xs text-gray-500 mt-1">Input credit carried forward</p>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white shadow rounded">
        <div class="p-4 border-b font-semibold">Output Tax &mdash; Sales</div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">GST Slab</th>
                    <th class="p-3 text-right">Taxable Value</th>
                    <th class="p-3 text-right">Tax</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gst['output_slabs'] as $slab)
                    <tr class="border-t">
                        <td class="p-3">{{ number_format((float) $slab->rate, 2) }}%</td>
                        <td class="p-3 text-right">₹{{ number_format((float) $slab->taxable_total - (float) $slab->tax_amount, 2) }}</td>
                        <td class="p-3 text-right">₹{{ number_format((float) $slab->tax_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-gray-600" colspan="3">No taxable sales in this period.</td></tr>
                @endforelse
            </tbody>
            @if($gst['output_slabs']->count())
                <tfoot class="bg-gray-50 font-semibold">
                    <tr class="border-t"><td class="p-3" colspan="2">Total Output GST</td><td class="p-3 text-right">₹{{ number_format($gst['output_tax'], 2) }}</td></tr>
                </tfoot>
            @endif
        </table>
    </div>

    <div class="bg-white shadow rounded">
        <div class="p-4 border-b font-semibold">Input Tax &mdash; Purchases</div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">GST Slab</th>
                    <th class="p-3 text-right">Taxable Value</th>
                    <th class="p-3 text-right">Tax</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gst['input_slabs'] as $slab)
                    <tr class="border-t">
                        <td class="p-3">{{ number_format((float) $slab->rate, 2) }}%</td>
                        <td class="p-3 text-right">₹{{ number_format((float) $slab->taxable_total - (float) $slab->tax_amount, 2) }}</td>
                        <td class="p-3 text-right">₹{{ number_format((float) $slab->tax_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-gray-600" colspan="3">No taxable purchases in this period.</td></tr>
                @endforelse
            </tbody>
            @if($gst['input_slabs']->count())
                <tfoot class="bg-gray-50 font-semibold">
                    <tr class="border-t"><td class="p-3" colspan="2">Total Input GST</td><td class="p-3 text-right">₹{{ number_format($gst['input_tax'], 2) }}</td></tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
