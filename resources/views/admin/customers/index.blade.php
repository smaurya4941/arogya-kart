@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">Customers</h1>
        <p class="text-sm text-gray-600">People you sell to — their contact details and purchase history.</p>
    </div>
    <a href="{{ route('admin.customers.create') }}"
       class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
        Add Customer
    </a>
</div>

<form method="GET" action="{{ route('admin.customers.index') }}" class="bg-white rounded shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-3">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Name, phone or email"
                   class="w-full border rounded px-3 py-2">
        </div>
        <div class="flex items-end gap-3">
            <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Apply</button>
            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 rounded border">Reset</a>
        </div>
    </div>
</form>

<div class="bg-white rounded shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Email</th>
                    <th class="p-3 text-right">Purchases</th>
                    <th class="p-3 text-right">Lifetime Value</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $customer->name }}</td>
                        <td class="p-3 text-gray-600">{{ $customer->phone ?? '-' }}</td>
                        <td class="p-3 text-gray-600">{{ $customer->email ?? '-' }}</td>
                        <td class="p-3 text-right">{{ $customer->sales_count }}</td>
                        <td class="p-3 text-right">₹{{ number_format((float) $customer->sales_total, 2) }}</td>
                        <td class="p-3 space-x-2">
                            <a class="text-emerald-700 hover:underline" href="{{ route('admin.customers.show', $customer) }}">View</a>
                            <a class="text-blue-600 hover:underline" href="{{ route('admin.customers.edit', $customer) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3 text-gray-600" colspan="6">No customers yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $customers->links() }}
    </div>
</div>
@endsection
