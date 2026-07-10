@extends('layouts.superadmin')

@section('title', 'Subscriptions')

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <form method="GET" class="flex gap-3 mb-5">
            <select name="status" class="rounded-lg border-gray-300 text-sm">
                <option value="">All statuses</option>
                @foreach(['trial','active','expired','cancelled','suspended'] as $status)
                    <option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="py-2 pr-4">Pharmacy</th>
                        <th class="py-2 pr-4">Plan</th>
                        <th class="py-2 pr-4">Cycle</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 pr-4">Started</th>
                        <th class="py-2 pr-4">Ends</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subscriptions as $sub)
                        <tr>
                            <td class="py-3 pr-4">
                                <a href="{{ route('superadmin.pharmacies.show', $sub->pharmacy_id) }}" class="text-gray-900 font-medium hover:text-blue-600">{{ $sub->pharmacy?->name ?? '—' }}</a>
                            </td>
                            <td class="py-3 pr-4">{{ $sub->plan?->name ?? '—' }}</td>
                            <td class="py-3 pr-4">{{ ucfirst($sub->billing_cycle) }}</td>
                            <td class="py-3 pr-4">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs
                                    {{ in_array($sub->status, ['active','trial']) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="py-3 pr-4 text-gray-500">{{ optional($sub->starts_at)->format('d M Y') ?? '—' }}</td>
                            <td class="py-3 pr-4 text-gray-500">{{ optional($sub->currentPeriodEnd())->format('d M Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-400">No subscriptions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $subscriptions->links() }}</div>
    </div>
@endsection
