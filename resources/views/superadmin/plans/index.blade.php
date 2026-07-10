@extends('layouts.superadmin')

@section('title', 'Plans')

@section('content')
    <div class="flex justify-between items-center mb-5">
        <p class="text-sm text-gray-500">Manage the subscription plans offered to pharmacies.</p>
        <a href="{{ route('superadmin.plans.create') }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">+ New Plan</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($plans as $plan)
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $plan->name }}</h3>
                        <p class="text-xs text-gray-400">{{ $plan->subscriptions_count }} subscriptions</p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="mt-3 text-2xl font-extrabold text-gray-900">₹{{ number_format($plan->price_monthly) }}<span class="text-sm font-normal text-gray-400">/mo</span></p>
                <p class="text-xs text-gray-500">₹{{ number_format($plan->price_yearly) }}/yr</p>
                <ul class="mt-4 text-sm text-gray-600 space-y-1">
                    <li>{{ $plan->max_users }} users · {{ $plan->max_branches }} branches</li>
                    <li>API access: {{ $plan->api_access ? 'Yes' : 'No' }}</li>
                </ul>
                <div class="mt-5 flex gap-2">
                    <a href="{{ route('superadmin.plans.edit', $plan) }}" class="flex-1 text-center px-3 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200">Edit</a>
                    <form method="POST" action="{{ route('superadmin.plans.destroy', $plan) }}" class="flex-1" onsubmit="return confirm('Delete this plan?')">
                        @csrf @method('DELETE')
                        <button class="w-full px-3 py-2 rounded-lg bg-red-50 text-red-700 text-sm font-medium hover:bg-red-100">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-400">No plans yet.</p>
        @endforelse
    </div>
@endsection
