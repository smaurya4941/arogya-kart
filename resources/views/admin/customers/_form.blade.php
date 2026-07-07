@php($customer = $customer ?? null)

@if ($errors->any())
    <div class="rounded border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Name <span class="text-rose-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}"
               class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Gender</label>
        <select name="gender" class="w-full border rounded px-3 py-2">
            <option value="">—</option>
            @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected(old('gender', $customer->gender ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Date of Birth</label>
        <input type="date" name="dob"
               value="{{ old('dob', optional($customer->dob ?? null)->toDateString()) }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Address</label>
        <textarea name="address" rows="3"
                  class="w-full border rounded px-3 py-2">{{ old('address', $customer->address ?? '') }}</textarea>
    </div>
</div>
