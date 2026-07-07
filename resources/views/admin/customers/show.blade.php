@extends('layouts.admin')

@section('title', $customer->name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $customer->name }}</h1>
        <p class="text-sm text-gray-600">Customer profile &amp; purchase history.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.sales.create', ['customer_id' => $customer->id]) }}"
           class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">New Sale</a>
        <a href="{{ route('admin.customers.edit', $customer) }}" class="px-4 py-2 rounded border">Edit</a>
        <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 rounded border">Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white shadow rounded p-6 space-y-3">
        <h2 class="font-semibold">Details</h2>
        <dl class="text-sm space-y-2">
            <div class="flex justify-between"><dt class="text-gray-500">Phone</dt><dd>{{ $customer->phone ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd>{{ $customer->email ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Gender</dt><dd class="capitalize">{{ $customer->gender ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Date of Birth</dt><dd>{{ optional($customer->dob)->format('d M Y') ?? '-' }}</dd></div>
            <div class="pt-2"><dt class="text-gray-500 mb-1">Address</dt><dd>{{ $customer->address ?? '-' }}</dd></div>
        </dl>
    </div>

    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 content-start">
        <div class="bg-white shadow rounded p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total Bills</p>
            <p class="text-2xl font-bold mt-1">{{ $customer->sales_count }}</p>
        </div>
        <div class="bg-white shadow rounded p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500">Lifetime Value</p>
            <p class="text-2xl font-bold mt-1">₹{{ number_format((float) $customer->sales_total, 2) }}</p>
        </div>
        <div class="bg-white shadow rounded p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500">Outstanding Due</p>
            <p class="text-2xl font-bold mt-1 text-rose-600">₹{{ number_format((float) $customer->sales_due, 2) }}</p>
        </div>

        <div class="sm:col-span-3 bg-white shadow rounded">
            <div class="p-4 border-b font-semibold">Recent Sales</div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="p-3">Invoice</th>
                            <th class="p-3">Date</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3 text-right">Due</th>
                            <th class="p-3">Status</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                            <tr class="border-t">
                                <td class="p-3 font-medium">{{ $sale->invoice_number }}</td>
                                <td class="p-3 text-gray-600">{{ $sale->sale_date->format('d M Y, h:i A') }}</td>
                                <td class="p-3 text-right">₹{{ number_format((float) $sale->total_amount, 2) }}</td>
                                <td class="p-3 text-right">₹{{ number_format((float) $sale->due_amount, 2) }}</td>
                                <td class="p-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs capitalize {{ $sale->paymentStatusBadge() }}">
                                        {{ $sale->payment_status }}
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <a class="text-emerald-700 hover:underline" href="{{ route('admin.sales.show', $sale) }}">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="p-3 text-gray-600" colspan="6">No sales yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(! $customer->sales_count)
    <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" class="mt-6"
          onsubmit="return confirm('Delete this customer?')">
        @csrf
        @method('DELETE')
        <button class="text-rose-600 hover:underline text-sm">Delete customer</button>
    </form>
@endif
@endsection
