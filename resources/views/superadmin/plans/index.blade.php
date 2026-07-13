@extends('layouts.superadmin')

@section('title', 'Plans')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-on-surface-variant">Manage the subscription plans offered to pharmacies.</p>
        <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined text-[16px]">add</span> New Plan
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($plans as $plan)
            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-bold text-on-surface">{{ $plan->name }}</h3>
                        <p class="text-xs text-on-surface-variant">{{ $plan->subscriptions_count }} subscriptions</p>
                    </div>
                    <span class="badge {{ $plan->is_active ? 'badge-success' : 'badge-neutral' }}">{{ $plan->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <p class="mt-3 text-2xl font-extrabold text-on-surface">₹{{ number_format($plan->price_monthly) }}<span class="text-sm font-normal text-on-surface-variant">/mo</span></p>
                <p class="text-xs text-on-surface-variant">₹{{ number_format($plan->price_yearly) }}/yr</p>
                <ul class="mt-4 space-y-1 text-sm text-on-surface-variant">
                    <li>{{ $plan->max_users }} users · {{ $plan->max_branches }} branches</li>
                    <li>API access: {{ $plan->api_access ? 'Yes' : 'No' }}</li>
                </ul>
                <div class="mt-5 flex gap-2">
                    <a href="{{ route('superadmin.plans.edit', $plan) }}" class="btn btn-outline btn-sm flex-1">Edit</a>
                    <form method="POST" action="{{ route('superadmin.plans.toggle-status', $plan) }}" class="flex-1">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm w-full {{ $plan->is_active ? 'bg-error-container text-on-error-container hover:opacity-90' : 'bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25' }}">
                            {{ $plan->is_active ? 'Archive' : 'Activate' }}
                        </button>
                    </form>
                </div>
                @if($plan->subscriptions_count === 0)
                    <form method="POST" action="{{ route('superadmin.plans.destroy', $plan) }}" class="mt-2" onsubmit="return confirm('Permanently delete this plan?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs w-full text-error hover:underline">Delete permanently</button>
                    </form>
                @endif
            </div>
        @empty
            <p class="text-on-surface-variant">No plans yet.</p>
        @endforelse
    </div>
@endsection
