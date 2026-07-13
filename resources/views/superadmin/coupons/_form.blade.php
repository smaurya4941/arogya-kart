@php use App\Models\Coupon; @endphp
<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="form-label">Code</label>
        <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required class="form-input font-mono uppercase" placeholder="WELCOME20">
    </div>
    <div>
        <label class="form-label">Description</label>
        <input type="text" name="description" value="{{ old('description', $coupon->description) }}" class="form-input">
    </div>
    <div>
        <label class="form-label">Type</label>
        <select name="type" class="form-select">
            <option value="percent" @selected(old('type', $coupon->type) === Coupon::TYPE_PERCENT)>Percentage (%)</option>
            <option value="fixed" @selected(old('type', $coupon->type) === Coupon::TYPE_FIXED)>Fixed amount (₹)</option>
        </select>
    </div>
    <div>
        <label class="form-label">Value</label>
        <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $coupon->value) }}" required class="form-input">
    </div>
    <div>
        <label class="form-label">Max redemptions (blank = unlimited)</label>
        <input type="number" min="1" name="max_redemptions" value="{{ old('max_redemptions', $coupon->max_redemptions) }}" class="form-input">
    </div>
    <div>
        <label class="form-label">Expires at (optional)</label>
        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d\TH:i')) }}" class="form-input">
    </div>
    <label class="flex items-center gap-2 text-sm text-on-surface">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true)) class="rounded border-outline-variant text-primary focus:ring-primary/30">
        Active
    </label>
</div>

<div class="mt-6 flex gap-2">
    <button class="btn btn-primary">{{ $coupon->exists ? 'Save changes' : 'Create coupon' }}</button>
    <a href="{{ route('superadmin.coupons.index') }}" class="btn btn-outline">Cancel</a>
</div>
