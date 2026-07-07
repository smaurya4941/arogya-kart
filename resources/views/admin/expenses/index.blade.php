@extends('layouts.admin')

@section('title', 'Expenses')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">Expenses</h1>
        <p class="text-sm text-gray-600">Operating costs that feed your profit &amp; loss.</p>
    </div>
    <a href="{{ route('admin.expenses.create') }}"
       class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
        Record Expense
    </a>
</div>

<form method="GET" action="{{ route('admin.expenses.index') }}" class="bg-white rounded shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Vendor or description"
                   class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Category</label>
            <select name="category" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from', $start->toDateString()) }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to', $end->toDateString()) }}" class="w-full border rounded px-3 py-2">
        </div>
    </div>
    <div class="flex items-center gap-3 mt-4">
        <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Apply</button>
        <a href="{{ route('admin.expenses.index') }}" class="px-4 py-2 rounded border">Reset</a>
        <span class="ml-auto text-sm text-gray-600">
            Total for period: <span class="font-bold text-gray-900">₹{{ number_format($total, 2) }}</span>
        </span>
    </div>
</form>

<div class="bg-white rounded shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-3">Date</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Vendor</th>
                    <th class="p-3">Description</th>
                    <th class="p-3 text-right">Amount</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr class="border-t">
                        <td class="p-3 text-gray-600">{{ $expense->date->format('d M Y') }}</td>
                        <td class="p-3 font-medium">{{ $expense->category->name ?? '-' }}</td>
                        <td class="p-3 text-gray-600">{{ $expense->vendor ?? '-' }}</td>
                        <td class="p-3 text-gray-600">{{ \Illuminate\Support\Str::limit($expense->description, 40) ?: '-' }}</td>
                        <td class="p-3 text-right font-medium">₹{{ number_format((float) $expense->amount, 2) }}</td>
                        <td class="p-3 space-x-2">
                            <a class="text-emerald-700 hover:underline" href="{{ route('admin.expenses.show', $expense) }}">View</a>
                            <a class="text-blue-600 hover:underline" href="{{ route('admin.expenses.edit', $expense) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-gray-600" colspan="6">No expenses in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $expenses->links() }}
    </div>
</div>
@endsection
