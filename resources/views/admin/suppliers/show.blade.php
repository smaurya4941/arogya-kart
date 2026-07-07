@extends('layouts.admin')

@section('title', 'Supplier · ' . $supplier->name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $supplier->name }}</h1>
        <p class="text-sm text-gray-600">{{ $supplier->company_name ?? 'Supplier details' }}</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('admin.suppliers.edit', $supplier) }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Edit</a>
        <a href="{{ route('admin.suppliers.index') }}" class="px-4 py-2 rounded border">Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded shadow p-6 space-y-3">
        <h2 class="font-semibold border-b pb-2">Details</h2>
        <dl class="text-sm space-y-2">
            <div class="flex justify-between"><dt class="text-gray-500">Contact Person</dt><dd>{{ $supplier->contact_person ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Phone</dt><dd>{{ $supplier->phone ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd>{{ $supplier->email ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">GST</dt><dd>{{ $supplier->gst_number ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">City / State</dt><dd>{{ trim(($supplier->city ?? '') . ' ' . ($supplier->state ?? '')) ?: '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd>{{ $supplier->is_active ? 'Active' : 'Inactive' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Outstanding</dt><dd>₹{{ number_format($supplier->balance, 2) }}</dd></div>
        </dl>
        @if($supplier->address)
            <div class="pt-2 text-sm">
                <p class="text-gray-500">Address</p>
                <p>{{ $supplier->address }}</p>
            </div>
        @endif
    </div>

    <div class="lg:col-span-2 bg-white rounded shadow">
        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="font-semibold">Recent Purchases ({{ $supplier->purchase_invoices_count }})</h2>
            <a href="{{ route('admin.purchases.create', ['supplier_id' => $supplier->id]) }}"
               class="text-sm bg-emerald-600 text-white px-3 py-1.5 rounded hover:bg-emerald-700">New Purchase</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-3">Invoice #</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Amount</th>
                        <th class="p-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPurchases as $purchase)
                        <tr class="border-t">
                            <td class="p-3 font-medium">{{ $purchase->invoice_number }}</td>
                            <td class="p-3 text-gray-600">{{ $purchase->purchase_date->format('M d, Y') }}</td>
                            <td class="p-3">₹{{ number_format($purchase->total_amount, 2) }}</td>
                            <td class="p-3">
                                <a class="text-emerald-700 hover:underline" href="{{ route('admin.purchases.show', $purchase) }}">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="p-3 text-gray-600" colspan="4">No purchases recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}" class="mt-6"
      onsubmit="return confirm('Delete this supplier?');">
    @csrf
    @method('DELETE')
    <button class="text-rose-600 hover:underline text-sm">Delete supplier</button>
</form>
@endsection
