@extends('layouts.superadmin')

@section('title', 'Subscriptions')

@php
    $statusBadge = [
        'active'    => 'badge-success',
        'trial'     => 'badge-success',
        'expired'   => 'badge-neutral',
        'cancelled' => 'badge-danger',
        'suspended' => 'badge-danger',
    ];
@endphp

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h2 class="section-title">Subscriptions</h2>
        <a href="{{ route('superadmin.subscriptions.create') }}" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> New subscription
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap gap-2">
                <select name="status" class="form-select w-auto">
                    <option value="">All statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <select name="plan_id" class="form-select w-auto">
                    <option value="">All plans</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" @selected((string) request('plan_id') === (string) $plan->id)>{{ $plan->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm">Filter</button>
                @if(request()->hasAny(['status', 'plan_id']))
                    <a href="{{ route('superadmin.subscriptions.index') }}" class="btn btn-outline btn-sm">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Pharmacy</th>
                        <th>Plan</th>
                        <th>Cycle</th>
                        <th>Status</th>
                        <th>Period end</th>
                        <th>Invoices</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                        <tr>
                            <td>
                                <a href="{{ route('superadmin.pharmacies.show', $sub->pharmacy_id) }}" class="font-medium text-on-surface hover:text-primary">{{ $sub->pharmacy?->name ?? '—' }}</a>
                            </td>
                            <td>{{ $sub->plan?->name ?? '—' }}</td>
                            <td>{{ ucfirst($sub->billing_cycle) }}</td>
                            <td><span class="badge {{ $statusBadge[$sub->status] ?? 'badge-neutral' }}">{{ ucfirst($sub->status) }}</span></td>
                            <td class="text-on-surface-variant">{{ optional($sub->currentPeriodEnd())->format('d M Y') ?? '—' }}</td>
                            <td class="text-on-surface-variant">{{ $sub->invoices_count }}</td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('superadmin.subscriptions.edit', $sub) }}" class="btn btn-xs btn-outline">Manage</a>
                                    @if($sub->status !== 'cancelled')
                                        <form method="POST" action="{{ route('superadmin.subscriptions.cancel', $sub) }}" class="inline"
                                              onsubmit="return confirm('Cancel this subscription? The pharmacy will lose access.')">
                                            @csrf
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty-state">No subscriptions found.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subscriptions->hasPages())
            <div class="card-footer">{{ $subscriptions->links() }}</div>
        @endif
    </div>
@endsection
