@php
    // Works for both create (new Plan) and edit. old() takes precedence so
    // validation errors don't wipe the operator's input.
    $features = old('features', is_array($plan->features) ? implode("\n", $plan->features) : '');
@endphp

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="form-label">Plan name</label>
        <input type="text" name="name" value="{{ old('name', $plan->name) }}" required class="form-input">
    </div>

    <div class="md:col-span-2">
        <label class="form-label">Description</label>
        <textarea name="description" rows="2" class="form-textarea">{{ old('description', $plan->description) }}</textarea>
    </div>

    <div>
        <label class="form-label">Monthly price (₹)</label>
        <input type="number" step="0.01" min="0" name="price_monthly" value="{{ old('price_monthly', $plan->price_monthly) }}" required class="form-input">
    </div>
    <div>
        <label class="form-label">Yearly price (₹)</label>
        <input type="number" step="0.01" min="0" name="price_yearly" value="{{ old('price_yearly', $plan->price_yearly) }}" required class="form-input">
    </div>

    <div>
        <label class="form-label">Max users</label>
        <input type="number" min="1" name="max_users" value="{{ old('max_users', $plan->max_users ?? 1) }}" required class="form-input">
    </div>
    <div>
        <label class="form-label">Max branches</label>
        <input type="number" min="1" name="max_branches" value="{{ old('max_branches', $plan->max_branches ?? 1) }}" required class="form-input">
    </div>

    <div class="md:col-span-2">
        <label class="form-label">Features (one per line)</label>
        <textarea name="features" rows="4" class="form-textarea font-mono">{{ $features }}</textarea>
    </div>

    <label class="flex items-center gap-2 text-sm text-on-surface">
        <input type="checkbox" name="api_access" value="1" @checked(old('api_access', $plan->api_access)) class="rounded border-outline-variant text-primary focus:ring-primary/30">
        API access
    </label>
    <label class="flex items-center gap-2 text-sm text-on-surface">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan->is_active ?? true)) class="rounded border-outline-variant text-primary focus:ring-primary/30">
        Active (visible to pharmacies)
    </label>
</div>

<div class="mt-6 flex gap-2">
    <button class="btn btn-primary">Save plan</button>
    <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline">Cancel</a>
</div>
