@php($supplier = $supplier ?? null)

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Name <span class="text-rose-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}"
               class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Company Name</label>
        <input type="text" name="company_name" value="{{ old('company_name', $supplier->company_name ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Contact Person</label>
        <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">GST Number</label>
        <input type="text" name="gst_number" value="{{ old('gst_number', $supplier->gst_number ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">City</label>
        <input type="text" name="city" value="{{ old('city', $supplier->city ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">State</label>
        <input type="text" name="state" value="{{ old('state', $supplier->state ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Address</label>
        <textarea name="address" rows="3"
                  class="w-full border rounded px-3 py-2">{{ old('address', $supplier->address ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1"
                   @checked(old('is_active', $supplier->is_active ?? true))>
            <span class="text-sm font-medium">Active</span>
        </label>
    </div>
</div>
