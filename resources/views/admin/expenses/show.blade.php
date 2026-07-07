@extends('layouts.admin')

@section('title', 'Expense')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">₹{{ number_format((float) $expense->amount, 2) }}</h1>
        <p class="text-sm text-gray-600">{{ $expense->category->name ?? 'Uncategorised' }} &middot; {{ $expense->date->format('d M Y') }}</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.expenses.edit', $expense) }}" class="px-4 py-2 rounded border">Edit</a>
        <a href="{{ route('admin.expenses.index') }}" class="px-4 py-2 rounded border">Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white shadow rounded p-6 space-y-3">
        <h2 class="font-semibold">Details</h2>
        <dl class="text-sm space-y-2">
            <div class="flex justify-between"><dt class="text-gray-500">Category</dt><dd>{{ $expense->category->name ?? '-' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Amount</dt><dd>₹{{ number_format((float) $expense->amount, 2) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Date</dt><dd>{{ $expense->date->format('d M Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Vendor</dt><dd>{{ $expense->vendor ?? '-' }}</dd></div>
            <div class="pt-2"><dt class="text-gray-500 mb-1">Description</dt><dd>{{ $expense->description ?? '-' }}</dd></div>
        </dl>
    </div>

    <div class="bg-white shadow rounded p-6">
        <h2 class="font-semibold mb-3">Receipt</h2>
        @if ($expense->receipt_path)
            <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank"
               class="inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded hover:bg-slate-800 text-sm">
                Open receipt
            </a>
        @else
            <p class="text-sm text-gray-500">No receipt attached.</p>
        @endif
    </div>
</div>

<form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" class="mt-6"
      onsubmit="return confirm('Delete this expense?')">
    @csrf
    @method('DELETE')
    <button class="text-rose-600 hover:underline text-sm">Delete expense</button>
</form>
@endsection
