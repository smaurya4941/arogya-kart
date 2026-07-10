@php
    // Works for both create (new Plan) and edit. old() takes precedence so
    // validation errors don't wipe the operator's input.
    $features = old('features', is_array($plan->features) ? implode("\n", $plan->features) : '');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Plan name</label>
        <input type="text" name="name" value="{{ old('name', $plan->name) }}" required
               class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 text-sm">{{ old('description', $plan->description) }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Monthly price (₹)</label>
        <input type="number" step="0.01" min="0" name="price_monthly" value="{{ old('price_monthly', $plan->price_monthly) }}" required
               class="w-full rounded-lg border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Yearly price (₹)</label>
        <input type="number" step="0.01" min="0" name="price_yearly" value="{{ old('price_yearly', $plan->price_yearly) }}" required
               class="w-full rounded-lg border-gray-300 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Max users</label>
        <input type="number" min="1" name="max_users" value="{{ old('max_users', $plan->max_users ?? 1) }}" required
               class="w-full rounded-lg border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Max branches</label>
        <input type="number" min="1" name="max_branches" value="{{ old('max_branches', $plan->max_branches ?? 1) }}" required
               class="w-full rounded-lg border-gray-300 text-sm">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Features (one per line)</label>
        <textarea name="features" rows="4" class="w-full rounded-lg border-gray-300 text-sm font-mono">{{ $features }}</textarea>
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="api_access" value="1" @checked(old('api_access', $plan->api_access)) class="rounded border-gray-300 text-blue-600">
        API access
    </label>
    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan->is_active ?? true)) class="rounded border-gray-300 text-blue-600">
        Active (visible to pharmacies)
    </label>
</div>

<div class="mt-6 flex gap-3">
    <button class="px-5 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">Save plan</button>
    <a href="{{ route('superadmin.plans.index') }}" class="px-5 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200">Cancel</a>
</div>
