@extends('layouts.admin')

@section('title', 'Expense')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">₹{{ number_format((float) $expense->amount, 2) }}</h1>
            <p class="page-subtitle">{{ $expense->category->name ?? 'Uncategorised' }} &middot; {{ $expense->date->format('d M Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad space-y-3 lg:col-span-2">
            <h2 class="section-title">Details</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-on-surface-variant">Category</dt><dd>{{ $expense->category->name ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Amount</dt><dd class="font-semibold">₹{{ number_format((float) $expense->amount, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Date</dt><dd>{{ $expense->date->format('d M Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Vendor</dt><dd>{{ $expense->vendor ?? '-' }}</dd></div>
                <div class="pt-2"><dt class="mb-1 text-on-surface-variant">Description</dt><dd>{{ $expense->description ?? '-' }}</dd></div>
            </dl>
        </div>

        <div class="card card-pad">
            <h2 class="section-title mb-3">Receipt</h2>
            @if ($expense->receipt_path)
                <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="btn btn-outline">
                    <span class="material-symbols-outlined text-[18px]">description</span> Open receipt
                </a>
            @else
                <p class="text-sm text-on-surface-variant">No receipt attached.</p>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}"
          onsubmit="return confirm('Delete this expense?')">
        @csrf
        @method('DELETE')
        <button class="text-sm font-medium text-error hover:underline">Delete expense</button>
    </form>
</div>
@endsection
