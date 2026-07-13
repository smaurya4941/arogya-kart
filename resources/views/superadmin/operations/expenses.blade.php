@extends('layouts.superadmin')

@section('title', 'Operations · Expenses')

@section('content')
    @include('superadmin.operations._tabs')

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap items-center gap-2">
                @include('superadmin.operations._tenant_select')
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Vendor or description" class="form-input min-w-[180px] flex-1">
                <input type="date" name="from" value="{{ request('from') }}" class="form-input w-auto">
                <input type="date" name="to" value="{{ request('to') }}" class="form-input w-auto">
                <button class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Pharmacy</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th>Description</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td class="text-on-surface-variant">{{ $expense->pharmacy?->name ?? '—' }}</td>
                            <td class="text-on-surface-variant">{{ optional($expense->date)->format('d M Y') }}</td>
                            <td>{{ $expense->category?->name ?? '—' }}</td>
                            <td class="text-on-surface-variant">{{ $expense->vendor ?? '—' }}</td>
                            <td class="text-on-surface-variant">{{ Str::limit($expense->description, 40) ?: '—' }}</td>
                            <td class="text-right font-semibold">₹{{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">No expenses found.</div></td></tr>
                    @endforelse
                </tbody>
                @if($expenses->isNotEmpty())
                    <tfoot>
                        <tr class="font-semibold">
                            <td colspan="5" class="text-right">Total (all matching)</td>
                            <td class="text-right">₹{{ number_format($total, 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="card-footer">{{ $expenses->links() }}</div>
        @endif
    </div>
@endsection
