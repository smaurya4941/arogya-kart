@extends('layouts.superadmin')

@section('title', $pharmacy->name)

@section('content')
    <a href="{{ route('superadmin.pharmacies.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to pharmacies</a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
        {{-- Profile --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Profile</h2>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $pharmacy->isActive() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($pharmacy->status) }}</span>
            </div>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Owner</dt><dd class="text-gray-900">{{ $pharmacy->owner_name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd class="text-gray-900">{{ $pharmacy->email ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Phone</dt><dd class="text-gray-900">{{ $pharmacy->phone ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">GST</dt><dd class="text-gray-900">{{ $pharmacy->gst ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Drug License</dt><dd class="text-gray-900">{{ $pharmacy->drug_license_number ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Joined</dt><dd class="text-gray-900">{{ $pharmacy->created_at->format('d M Y') }}</dd></div>
            </dl>
            <form method="POST" action="{{ route('superadmin.pharmacies.toggle-status', $pharmacy) }}" class="mt-5"
                  onsubmit="return confirm('{{ $pharmacy->isActive() ? 'Suspend' : 'Reactivate' }} this pharmacy?')">
                @csrf @method('PATCH')
                <button class="w-full px-3 py-2 rounded-lg text-sm font-medium {{ $pharmacy->isActive() ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                    {{ $pharmacy->isActive() ? 'Suspend pharmacy' : 'Reactivate pharmacy' }}
                </button>
            </form>

            <form method="POST" action="{{ route('superadmin.pharmacies.impersonate', $pharmacy) }}" class="mt-3"
                  onsubmit="return confirm('Log in as this pharmacy to provide support?')">
                @csrf
                <button class="w-full px-3 py-2 rounded-lg text-sm font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                    Impersonate owner
                </button>
            </form>
        </div>

        {{-- Subscription + users --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Current Subscription</h2>
                @if($sub = $pharmacy->currentSubscription)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div><p class="text-gray-500">Plan</p><p class="font-semibold">{{ $sub->plan?->name ?? '—' }}</p></div>
                        <div><p class="text-gray-500">Status</p><p class="font-semibold">{{ ucfirst($sub->status) }}</p></div>
                        <div><p class="text-gray-500">Cycle</p><p class="font-semibold">{{ ucfirst($sub->billing_cycle) }}</p></div>
                        <div><p class="text-gray-500">Ends</p><p class="font-semibold">{{ optional($sub->currentPeriodEnd())->format('d M Y') ?? '—' }}</p></div>
                    </div>
                @else
                    <p class="text-sm text-gray-400">No subscription on record.</p>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Users ({{ $pharmacy->users->count() }})</h2>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500 border-b border-gray-100">
                        <tr><th class="py-2 pr-4">Name</th><th class="py-2 pr-4">Email</th><th class="py-2 pr-4">Role</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pharmacy->users as $user)
                            <tr><td class="py-2 pr-4">{{ $user->name }}</td><td class="py-2 pr-4 text-gray-500">{{ $user->email }}</td><td class="py-2 pr-4">{{ ucfirst(str_replace('_',' ', $user->role?->value ?? $user->role)) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Recent Invoices</h2>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500 border-b border-gray-100">
                        <tr><th class="py-2 pr-4">Invoice</th><th class="py-2 pr-4">Date</th><th class="py-2 pr-4">Total</th><th class="py-2 pr-4">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pharmacy->invoices as $invoice)
                            <tr>
                                <td class="py-2 pr-4 font-mono">{{ $invoice->invoice_number }}</td>
                                <td class="py-2 pr-4 text-gray-500">{{ $invoice->created_at->format('d M Y') }}</td>
                                <td class="py-2 pr-4">₹{{ number_format($invoice->total, 2) }}</td>
                                <td class="py-2 pr-4">{{ ucfirst($invoice->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-400">No invoices.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
