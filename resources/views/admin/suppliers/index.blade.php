@extends('layouts.admin')

@section('title', 'Suppliers')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">Suppliers</h1>
        <p class="text-sm text-gray-600">Manage the vendors you purchase stock from.</p>
    </div>
    <a href="{{ route('admin.suppliers.create') }}"
       class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
        Add Supplier
    </a>
</div>

<form method="GET" action="{{ route('admin.suppliers.index') }}" class="bg-white rounded shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Name, company, phone or GST"
                   class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
        </div>
        <div class="flex items-end gap-3">
            <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Apply</button>
            <a href="{{ route('admin.suppliers.index') }}" class="px-4 py-2 rounded border">Reset</a>
        </div>
    </div>
</form>

<div class="bg-white rounded shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Company</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">GST</th>
                    <th class="p-3">Purchases</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $supplier->name }}</td>
                        <td class="p-3 text-gray-600">{{ $supplier->company_name ?? '-' }}</td>
                        <td class="p-3 text-gray-600">{{ $supplier->phone ?? '-' }}</td>
                        <td class="p-3 text-gray-600">{{ $supplier->gst_number ?? '-' }}</td>
                        <td class="p-3">{{ $supplier->purchase_invoices_count }}</td>
                        <td class="p-3">
                            @if($supplier->is_active)
                                <span class="rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 text-xs">Active</span>
                            @else
                                <span class="rounded-full bg-gray-200 text-gray-600 px-2 py-0.5 text-xs">Inactive</span>
                            @endif
                        </td>
                        <td class="p-3 space-x-2">
                            <a class="text-emerald-700 hover:underline" href="{{ route('admin.suppliers.show', $supplier) }}">View</a>
                            <a class="text-blue-600 hover:underline" href="{{ route('admin.suppliers.edit', $supplier) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3 text-gray-600" colspan="7">No suppliers yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
