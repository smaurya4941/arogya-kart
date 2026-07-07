@extends('layouts.admin')

@section('title', 'Sales')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">Sales</h1>
        <p class="text-sm text-gray-600">Every bill rung up at the counter.</p>
    </div>
    <a href="{{ route('admin.sales.create') }}"
       class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
        + New Sale (POS)
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Bills</p>
        <p class="text-2xl font-bold mt-1">{{ number_format((int) ($totals->count ?? 0)) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Total</p>
        <p class="text-2xl font-bold mt-1">₹{{ number_format((float) ($totals->total ?? 0), 2) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Collected</p>
        <p class="text-2xl font-bold mt-1 text-emerald-600">₹{{ number_format((float) ($totals->paid ?? 0), 2) }}</p>
    </div>
    <div class="bg-white shadow rounded p-5">
        <p class="text-xs uppercase tracking-wide text-gray-500">Outstanding</p>
        <p class="text-2xl font-bold mt-1 text-rose-600">₹{{ number_format((float) ($totals->due ?? 0), 2) }}</p>
    </div>
</div>

<form method="GET" action="{{ route('admin.sales.index') }}" class="bg-white rounded shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Invoice # or customer" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                @foreach (['paid' => 'Paid', 'partial' => 'Partial', 'unpaid' => 'Unpaid'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Method</label>
            <select name="method" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                @foreach (['cash' => 'Cash', 'card' => 'Card', 'upi' => 'UPI', 'credit' => 'Credit'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('method') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border rounded px-3 py-2">
        </div>
    </div>
    <div class="flex gap-3 mt-4">
        <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Apply</button>
        <a href="{{ route('admin.sales.index') }}" class="px-4 py-2 rounded border">Reset</a>
    </div>
</form>

<div class="bg-white rounded shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">Invoice</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Customer</th>
                    <th class="p-3">Cashier</th>
                    <th class="p-3">Method</th>
                    <th class="p-3 text-right">Total</th>
                    <th class="p-3 text-right">Due</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $sale->invoice_number }}</td>
                        <td class="p-3 text-gray-600">{{ $sale->sale_date->format('d M Y, h:i A') }}</td>
                        <td class="p-3 text-gray-600">{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                        <td class="p-3 text-gray-600">{{ $sale->cashier?->name ?? '-' }}</td>
                        <td class="p-3 capitalize">{{ $sale->payment_method }}</td>
                        <td class="p-3 text-right">₹{{ number_format((float) $sale->total_amount, 2) }}</td>
                        <td class="p-3 text-right">₹{{ number_format((float) $sale->due_amount, 2) }}</td>
                        <td class="p-3">
                            <span class="rounded-full px-2 py-0.5 text-xs capitalize {{ $sale->paymentStatusBadge() }}">
                                {{ $sale->payment_status }}
                            </span>
                        </td>
                        <td class="p-3 space-x-2 whitespace-nowrap">
                            <a class="text-emerald-700 hover:underline" href="{{ route('admin.sales.show', $sale) }}">View</a>
                            <a class="text-blue-600 hover:underline" href="{{ route('admin.sales.invoice', $sale) }}" target="_blank">Print</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-gray-600" colspan="9">No sales yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $sales->links() }}
    </div>
</div>
@endsection
