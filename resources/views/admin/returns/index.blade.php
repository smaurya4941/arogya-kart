@extends('layouts.admin')

@section('title', 'Returns')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Returns &amp; Refunds</h1>
            <p class="text-sm text-gray-600">Credit notes issued against sales.</p>
        </div>
        <form method="GET">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search return / invoice #"
                   class="rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 font-semibold">Return #</th>
                    <th class="px-5 py-3 font-semibold">Invoice</th>
                    <th class="px-5 py-3 font-semibold">Date</th>
                    <th class="px-5 py-3 font-semibold">Processed by</th>
                    <th class="px-5 py-3 font-semibold text-right">Refunded</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($returns as $return)
                    <tr>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.returns.show', $return) }}" class="font-mono font-medium text-emerald-700 hover:underline">{{ $return->return_number }}</a>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.sales.show', $return->sale_id) }}" class="text-gray-700 hover:underline">{{ $return->sale?->invoice_number ?? '—' }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $return->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $return->processor?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right font-medium">₹{{ number_format($return->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No returns yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $returns->links() }}</div>
</div>
@endsection
