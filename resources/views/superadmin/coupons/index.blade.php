@extends('layouts.superadmin')

@section('title', 'Coupons')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h2 class="section-title">Coupons</h2>
        <a href="{{ route('superadmin.coupons.create') }}" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> New coupon
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Redemptions</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td>
                                <div class="font-mono-data font-medium text-on-surface">{{ $coupon->code }}</div>
                                @if($coupon->description)<div class="text-xs text-on-surface-variant">{{ $coupon->description }}</div>@endif
                            </td>
                            <td>{{ $coupon->type === 'percent' ? rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') . '%' : '₹' . number_format($coupon->value, 2) }}</td>
                            <td class="text-on-surface-variant">{{ $coupon->redeemed_count }}{{ $coupon->max_redemptions ? ' / ' . $coupon->max_redemptions : '' }}</td>
                            <td class="text-on-surface-variant">{{ optional($coupon->expires_at)->format('d M Y') ?? '—' }}</td>
                            <td>
                                @if($coupon->isRedeemable())
                                    <span class="badge badge-success">Live</span>
                                @else
                                    <span class="badge badge-neutral">{{ $coupon->is_active ? 'Depleted/Expired' : 'Inactive' }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('superadmin.coupons.edit', $coupon) }}" class="btn btn-xs btn-outline">Edit</a>
                                    <form method="POST" action="{{ route('superadmin.coupons.toggle', $coupon) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-xs bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25">{{ $coupon->is_active ? 'Deactivate' : 'Activate' }}</button>
                                    </form>
                                    @if($coupon->redeemed_count === 0)
                                        <form method="POST" action="{{ route('superadmin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Delete this coupon?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">No coupons yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="card-footer">{{ $coupons->links() }}</div>
        @endif
    </div>
@endsection
