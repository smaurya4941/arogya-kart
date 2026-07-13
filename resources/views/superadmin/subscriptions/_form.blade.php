@php
    use App\Models\Subscription;
    // Pre-fill the single "period end" field from whichever column applies.
    $periodEnd = old('period_end', optional($subscription->currentPeriodEnd())->format('Y-m-d'));
@endphp

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="form-label">Pharmacy</label>
        <select name="pharmacy_id" required class="form-select">
            <option value="">— Select pharmacy —</option>
            @foreach($pharmacies as $pharmacy)
                <option value="{{ $pharmacy->id }}" @selected((string) old('pharmacy_id', $subscription->pharmacy_id) === (string) $pharmacy->id)>{{ $pharmacy->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">Plan</label>
        <select name="plan_id" required class="form-select">
            <option value="">— Select plan —</option>
            @foreach($plans as $plan)
                <option value="{{ $plan->id }}" @selected((string) old('plan_id', $subscription->plan_id) === (string) $plan->id)>
                    {{ $plan->name }} (₹{{ number_format($plan->price_monthly) }}/mo)
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">Status</label>
        <select name="status" required class="form-select">
            @foreach($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $subscription->status) === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">Billing cycle</label>
        <select name="billing_cycle" required class="form-select">
            @foreach($cycles as $cycle)
                <option value="{{ $cycle }}" @selected(old('billing_cycle', $subscription->billing_cycle) === $cycle)>{{ ucfirst($cycle) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">Starts at</label>
        <input type="date" name="starts_at" value="{{ old('starts_at', optional($subscription->starts_at)->format('Y-m-d')) }}" class="form-input">
    </div>
    <div>
        <label class="form-label">Current period end</label>
        <input type="date" name="period_end" value="{{ $periodEnd }}" class="form-input">
        <p class="mt-1 text-xs text-on-surface-variant">Applies to the trial end for trials, otherwise the paid period end.</p>
    </div>
</div>

<div class="mt-6 flex gap-2">
    <button class="btn btn-primary">{{ $subscription->exists ? 'Save changes' : 'Create subscription' }}</button>
    <a href="{{ route('superadmin.subscriptions.index') }}" class="btn btn-outline">Cancel</a>
</div>
