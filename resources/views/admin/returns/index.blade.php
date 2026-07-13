@extends('layouts.admin')

@section('title', 'Returns')

@section('content')
<div class="page mx-auto max-w-6xl">
    <div class="page-header">
        <div>
            <h1 class="page-title">Returns &amp; Refunds</h1>
            <p class="page-subtitle">Credit notes issued against sales.</p>
        </div>
        <form method="GET" class="w-full sm:w-72">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search return / invoice #" class="form-input">
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Return #</th>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Processed by</th>
                        <th class="text-right">Refunded</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                        <tr>
                            <td>
                                <a href="{{ route('admin.returns.show', $return) }}" class="font-mono-data font-medium text-primary hover:underline">{{ $return->return_number }}</a>
                            </td>
                            <td>
                                <a href="{{ route('admin.sales.show', $return->sale_id) }}" class="text-on-surface hover:underline">{{ $return->sale?->invoice_number ?? '—' }}</a>
                            </td>
                            <td class="text-on-surface-variant">{{ $return->created_at->format('d M Y, h:i A') }}</td>
                            <td class="text-on-surface-variant">{{ $return->processor?->name ?? '—' }}</td>
                            <td class="text-right font-semibold">₹{{ number_format($return->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">assignment_return</span>
                                    No returns yet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($returns->hasPages())
            <div class="card-footer">{{ $returns->links() }}</div>
        @endif
    </div>
</div>
@endsection
