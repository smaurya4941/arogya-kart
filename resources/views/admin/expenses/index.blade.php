@extends('layouts.admin')

@section('title', 'Expenses')

@section('content')
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Expenses</h1>
            <p class="page-subtitle">Operating costs that feed your profit &amp; loss.</p>
        </div>
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">add</span> Record Expense
        </a>
    </div>

    <form method="GET" action="{{ route('admin.expenses.index') }}" class="card card-pad">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
            <div class="md:col-span-2">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Vendor or description" class="form-input">
            </div>
            <div>
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">All</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">From</label>
                <input type="date" name="from" value="{{ request('from', $start->toDateString()) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">To</label>
                <input type="date" name="to" value="{{ request('to', $end->toDateString()) }}" class="form-input">
            </div>
        </div>
        <div class="mt-3 flex items-center gap-2">
            <button class="btn btn-primary btn-sm">Apply</button>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline btn-sm">Reset</a>
            <span class="ml-auto text-sm text-on-surface-variant">
                Total for period: <span class="font-bold text-on-surface">₹{{ number_format($total, 2) }}</span>
            </span>
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th>Description</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td class="text-on-surface-variant">{{ $expense->date->format('d M Y') }}</td>
                            <td class="font-medium">{{ $expense->category->name ?? '-' }}</td>
                            <td class="text-on-surface-variant">{{ $expense->vendor ?? '-' }}</td>
                            <td class="text-on-surface-variant">{{ \Illuminate\Support\Str::limit($expense->description, 40) ?: '-' }}</td>
                            <td class="text-right font-semibold">₹{{ number_format((float) $expense->amount, 2) }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a class="btn-icon" title="View" href="{{ route('admin.expenses.show', $expense) }}">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a class="btn-icon" title="Edit" href="{{ route('admin.expenses.edit', $expense) }}">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">receipt</span>
                                    No expenses in this period.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer">{{ $expenses->links() }}</div>
        @endif
    </div>
</div>
@endsection
