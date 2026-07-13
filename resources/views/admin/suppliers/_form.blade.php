@php($supplier = $supplier ?? null)

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
        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" class="form-input" required>
    </div>

    <div>
        <label class="form-label">Company Name</label>
        <input type="text" name="company_name" value="{{ old('company_name', $supplier->company_name ?? '') }}" class="form-input">
    </div>

    <div>
        <label class="form-label">Contact Person</label>
        <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" class="form-input">
    </div>

    <div>
        <label class="form-label">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" class="form-input">
    </div>

    <div>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}" class="form-input">
    </div>

    <div>
        <label class="form-label">GST Number</label>
        <input type="text" name="gst_number" value="{{ old('gst_number', $supplier->gst_number ?? '') }}" class="form-input">
    </div>

    <div>
        <label class="form-label">City</label>
        <input type="text" name="city" value="{{ old('city', $supplier->city ?? '') }}" class="form-input">
    </div>

    <div>
        <label class="form-label">State</label>
        <input type="text" name="state" value="{{ old('state', $supplier->state ?? '') }}" class="form-input">
    </div>

    <div class="md:col-span-2">
        <label class="form-label">Address</label>
        <textarea name="address" rows="3" class="form-textarea">{{ old('address', $supplier->address ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" class="rounded border-outline-variant text-primary focus:ring-primary/30" @checked(old('is_active', $supplier->is_active ?? true))>
            <span class="text-sm font-medium text-on-surface">Active</span>
        </label>
    </div>
</div>
