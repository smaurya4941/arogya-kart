@extends('layouts.superadmin')

@section('title', 'Manage Subscription')

@php
    use App\Models\Invoice;
    $invoiceBadge = [
        Invoice::STATUS_PAID     => 'badge-success',
        Invoice::STATUS_PENDING  => 'badge-neutral',
        Invoice::STATUS_FAILED   => 'badge-danger',
        Invoice::STATUS_REFUNDED => 'badge-neutral',
        Invoice::STATUS_VOID     => 'badge-danger',
    ];
@endphp

@section('content')
    <a href="{{ route('superadmin.subscriptions.index') }}" class="text-sm font-medium text-primary hover:underline">&larr; Back to subscriptions</a>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Main edit form --}}
        <div class="card card-pad lg:col-span-2">
            <h2 class="section-title mb-4">{{ $subscription->pharmacy?->name }} — {{ $subscription->plan?->name ?? 'No plan' }}</h2>
            <form method="POST" action="{{ route('superadmin.subscriptions.update', $subscription) }}">
                @csrf @method('PUT')
                @include('superadmin.subscriptions._form')
            </form>
        </div>

        {{-- Side actions --}}
        <div class="space-y-4">
            {{-- Extend trial --}}
            <div class="card card-pad">
                <h3 class="section-title mb-3">Extend trial</h3>
                <form method="POST" action="{{ route('superadmin.subscriptions.extend-trial', $subscription) }}" class="flex gap-2">
                    @csrf
                    <input type="number" name="days" min="1" max="365" value="14" class="form-input w-24" required>
                    <button class="btn btn-outline flex-1">Add days</button>
                </form>
                <p class="mt-2 text-xs text-on-surface-variant">Pushes the trial end forward and sets status to trial.</p>
            </div>

            {{-- Generate invoice --}}
            <div class="card card-pad">
                <h3 class="section-title mb-3">Generate invoice</h3>
                <form method="POST" action="{{ route('superadmin.invoices.store') }}" class="space-y-2">
                    @csrf
                    <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                    <input type="number" step="0.01" min="0" name="amount" class="form-input"
                           placeholder="Amount (blank = plan price)">
                    <select name="status" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                    </select>
                    <button class="btn btn-primary w-full">Raise invoice</button>
                </form>
            </div>

            {{-- Danger zone --}}
            <div class="card card-pad">
                <h3 class="section-title mb-3">Danger zone</h3>
                @if($subscription->status !== 'cancelled')
                    <form method="POST" action="{{ route('superadmin.subscriptions.cancel', $subscription) }}"
                          onsubmit="return confirm('Cancel this subscription?')">
                        @csrf
                        <button class="btn w-full bg-error-container text-on-error-container hover:opacity-90">Cancel subscription</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('superadmin.subscriptions.destroy', $subscription) }}" class="mt-2"
                      onsubmit="return confirm('Delete this subscription record? History is retained but it is removed from lists.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline w-full">Delete record</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Recent invoices --}}
    <div class="card mt-4 overflow-hidden">
        <div class="card-header"><h2 class="section-title">Recent Invoices</h2></div>
        <table class="table-saas">
            <thead>
                <tr><th>Invoice</th><th>Total</th><th>Status</th><th>Issued</th><th class="text-right">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($subscription->invoices as $invoice)
                    <tr>
                        <td class="font-mono-data">{{ $invoice->invoice_number }}</td>
                        <td>₹{{ number_format($invoice->total, 2) }}</td>
                        <td><span class="badge {{ $invoiceBadge[$invoice->status] ?? 'badge-neutral' }}">{{ ucfirst($invoice->status) }}</span></td>
                        <td class="text-on-surface-variant">{{ $invoice->created_at->format('d M Y') }}</td>
                        <td class="text-right">
                            <a href="{{ route('superadmin.invoices.pdf', $invoice) }}" target="_blank" class="btn btn-xs btn-outline">PDF</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty-state">No invoices for this subscription.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
