<x-app-layout>
    <div class="page">
        <div class="page-header">
            <h1 class="page-title">Subscription Management</h1>
        </div>

        <!-- Current Plan -->
        <div class="card card-pad">
            <h3 class="section-title mb-4">Current Subscription</h3>
            @if($currentSubscription && $currentSubscription->isValid())
                @php $onTrial = $currentSubscription->onTrial(); @endphp
                <div class="flex flex-col gap-3 rounded-xl border p-5 sm:flex-row sm:items-center sm:justify-between {{ $onTrial ? 'border-amber-200 bg-amber-50' : 'border-tertiary/20 bg-tertiary-container/10' }}">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider {{ $onTrial ? 'text-amber-600' : 'text-tertiary' }}">
                            {{ $onTrial ? 'Free Trial' : 'Active Plan' }}
                        </p>
                        <h4 class="mt-1 text-xl font-bold text-on-surface">{{ $currentSubscription->plan->name }} ({{ ucfirst($currentSubscription->billing_cycle) }})</h4>
                        <p class="mt-2 text-sm text-on-surface-variant">
                            {{ $onTrial ? 'Trial ends' : 'Valid until' }}:
                            {{ optional($currentSubscription->currentPeriodEnd())->format('d M, Y') ?? 'N/A' }}
                            <span class="ml-1 font-medium">({{ $currentSubscription->daysRemaining() }} days left)</span>
                        </p>
                    </div>
                    <span class="badge {{ $onTrial ? 'badge-warning' : 'badge-success' }}">{{ $onTrial ? 'Trial' : 'Active' }}</span>
                </div>
            @else
                <div class="rounded-xl border border-error/30 bg-error-container/40 p-5">
                    <p class="font-bold text-on-error-container">You do not have an active subscription.</p>
                    <p class="mt-1 text-sm text-on-error-container/80">Please select a plan below to continue using the software.</p>
                </div>
            @endif
        </div>

        <!-- Coupon -->
        @if($couponsEnabled)
            <div class="card card-pad">
                <label for="coupon-code-input" class="section-title mb-2 block">Have a coupon?</label>
                <div class="flex max-w-sm items-center gap-2">
                    <input type="text" id="coupon-code-input" value="{{ old('coupon_code') }}" placeholder="Enter code (e.g. WELCOME20)"
                           class="form-input font-mono uppercase" oninput="this.value = this.value.toUpperCase()">
                </div>
                @error('coupon_code')
                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-on-surface-variant">Your discount is applied when you click <strong>Subscribe Now</strong>.</p>
            </div>
        @endif

        <!-- Available Plans -->
        <div>
            <h3 class="section-title mb-4">Available Plans</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                @foreach($plans as $plan)
                    @php $isCurrent = $currentSubscription && $currentSubscription->plan_id == $plan->id; @endphp
                    <div class="card relative overflow-hidden transition hover:shadow-md {{ $isCurrent ? 'ring-2 ring-primary' : '' }}">
                        @if($plan->name == 'Professional')
                            <div class="absolute right-0 top-0 rounded-bl-lg bg-primary px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-on-primary">Most Popular</div>
                        @endif
                        <div class="card-pad">
                            <h4 class="text-xl font-bold text-on-surface">{{ $plan->name }}</h4>
                            <p class="h-10 text-sm text-on-surface-variant">{{ $plan->description }}</p>

                            <div class="mb-6 mt-5">
                                <span class="text-3xl font-extrabold text-on-surface">₹{{ number_format($plan->price_monthly) }}</span>
                                <span class="text-on-surface-variant">/mo</span>
                            </div>

                            <ul class="mb-6 space-y-3 text-sm text-on-surface">
                                <li class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-tertiary">check_circle</span> {{ $plan->max_users }} Users
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-tertiary">check_circle</span> {{ $plan->max_branches }} Branches
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] {{ $plan->api_access ? 'text-tertiary' : 'text-outline-variant' }}">{{ $plan->api_access ? 'check_circle' : 'cancel' }}</span> API Access
                                </li>
                            </ul>

                            <form action="{{ route('admin.subscription.subscribe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <input type="hidden" name="billing_cycle" value="monthly">
                                @if($couponsEnabled)
                                    <input type="hidden" name="coupon_code" class="coupon-code-field" value="{{ old('coupon_code') }}">
                                @endif
                                <button type="submit" class="btn w-full {{ $isCurrent ? 'btn-outline cursor-not-allowed' : 'btn-primary' }}" {{ $isCurrent ? 'disabled' : '' }}>
                                    {{ $isCurrent ? 'Current Plan' : 'Subscribe Now' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Billing History -->
        @if(isset($invoices) && $invoices->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="card-header"><h3 class="section-title">Billing History</h3></div>
                <div class="overflow-x-auto">
                    <table class="table-saas">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td class="font-mono-data">{{ $invoice->invoice_number }}</td>
                                    <td class="text-on-surface-variant">{{ $invoice->created_at->format('d M, Y') }}</td>
                                    <td>₹{{ number_format($invoice->total, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $invoice->status === 'paid' ? 'badge-success' : ($invoice->status === 'failed' ? 'badge-danger' : 'badge-neutral') }}">{{ ucfirst($invoice->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    @if($couponsEnabled)
        <script>
            // Keep every plan form's hidden coupon field in sync with the shared input.
            (function () {
                const input = document.getElementById('coupon-code-input');
                if (!input) return;
                const sync = () => document.querySelectorAll('.coupon-code-field')
                    .forEach(field => field.value = input.value.trim());
                input.addEventListener('input', sync);
                sync();
            })();
        </script>
    @endif
</x-app-layout>
