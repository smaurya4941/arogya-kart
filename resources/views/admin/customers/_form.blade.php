@php($customer = $customer ?? null)

@if ($errors->any())
    <div class="rounded-lg border border-error/30 bg-error-container/40 p-3 text-sm text-on-error-container">
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="form-label">Name <span class="text-error">*</span></label>
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" class="form-input" required>
    </div>

    <div>
        <label class="form-label">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="form-input">
    </div>

    <div>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}" class="form-input">
    </div>

    <div>
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select">
            <option value="">—</option>
            @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected(old('gender', $customer->gender ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">Date of Birth</label>
        <input type="date" name="dob" value="{{ old('dob', optional($customer->dob ?? null)->toDateString()) }}" class="form-input">
    </div>

    <div class="md:col-span-2">
        <label class="form-label">Address</label>
        <textarea name="address" rows="3" class="form-textarea">{{ old('address', $customer->address ?? '') }}</textarea>
    </div>
</div>
