@extends('layouts.superadmin')

@section('title', 'Invoices')

@php
    use App\Models\Invoice;
    $badge = [
        Invoice::STATUS_PAID           => 'badge-success',
        Invoice::STATUS_PENDING        => 'badge-neutral',
        Invoice::STATUS_FAILED         => 'badge-danger',
        Invoice::STATUS_REFUND_PENDING => 'badge-warning',
        Invoice::STATUS_REFUNDED       => 'badge-neutral',
        Invoice::STATUS_VOID           => 'badge-danger',
    ];
@endphp

@section('content')
    {{-- Totals over the filtered set --}}
    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Invoices</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">{{ number_format($totals->count) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Billed</p>
            <p class="mt-1 text-2xl font-bold text-primary">₹{{ number_format($totals->billed, 2) }}</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Collected (paid)</p>
            <p class="mt-1 text-2xl font-bold text-tertiary">₹{{ number_format($totals->collected, 2) }}</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap items-center gap-2">
                <select name="status" class="form-select w-auto">
                    <option value="">All statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <select name="pharmacy_id" class="form-select w-auto">
                    <option value="">All pharmacies</option>
                    @foreach($pharmacies as $pharmacy)
                        <option value="{{ $pharmacy->id }}" @selected((string) request('pharmacy_id') === (string) $pharmacy->id)>{{ $pharmacy->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="from" value="{{ request('from') }}" class="form-input w-auto" title="From">
                <input type="date" name="to" value="{{ request('to') }}" class="form-input w-auto" title="To">
                <button class="btn btn-primary btn-sm">Filter</button>
                @if(request()->hasAny(['status', 'pharmacy_id', 'from', 'to']))
                    <a href="{{ route('superadmin.invoices.index') }}" class="btn btn-outline btn-sm">Reset</a>
                @endif
                <a href="{{ route('superadmin.invoices.export', request()->query()) }}" class="btn btn-outline btn-sm ml-auto">
                    <span class="material-symbols-outlined text-[18px]">download</span> Export CSV
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Pharmacy</th>
                        <th>Plan</th>
                        <th class="text-right">Total</th>
                        <th>Status</th>
                        <th>Issued</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="font-mono-data">{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->pharmacy?->name ?? '—' }}</td>
                            <td class="text-on-surface-variant">{{ $invoice->subscription?->plan?->name ?? '—' }}</td>
                            <td class="text-right">₹{{ number_format($invoice->total, 2) }}</td>
                            <td><span class="badge {{ $badge[$invoice->status] ?? 'badge-neutral' }}">{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span></td>
                            <td class="text-on-surface-variant">{{ $invoice->created_at->format('d M Y') }}</td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('superadmin.invoices.pdf', $invoice) }}" target="_blank" class="btn btn-xs btn-outline">PDF</a>
                                    @if($invoice->status === Invoice::STATUS_PENDING || $invoice->status === Invoice::STATUS_FAILED)
                                        <form method="POST" action="{{ route('superadmin.invoices.mark-paid', $invoice) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25">Mark paid</button>
                                        </form>
                                        <form method="POST" action="{{ route('superadmin.invoices.void', $invoice) }}" class="inline"
                                              onsubmit="return confirm('Void invoice {{ $invoice->invoice_number }}?')">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Void</button>
                                        </form>
                                    @elseif($invoice->status === Invoice::STATUS_PAID)
                                        <form method="POST" action="{{ route('superadmin.invoices.refund', $invoice) }}" class="inline"
                                              onsubmit="return confirm('Refund invoice {{ $invoice->invoice_number }}? For gateway payments this is submitted to Razorpay and settles asynchronously.')">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Refund</button>
                                        </form>
                                    @elseif($invoice->status === Invoice::STATUS_REFUND_PENDING)
                                        <span class="text-xs text-amber-700" title="Awaiting Razorpay confirmation">Refund pending…</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty-state">No invoices found.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="card-footer">{{ $invoices->links() }}</div>
        @endif
    </div>
@endsection
