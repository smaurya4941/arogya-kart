<x-app-layout>
    <div class="page mx-auto max-w-4xl">
        <div class="page-header">
            <div>
                <h1 class="page-title">Pharmacy Profile</h1>
                <p class="page-subtitle">Update your pharmacy's legal and contact details. This information appears on invoices.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="flex items-center gap-2 rounded-lg border border-tertiary/30 bg-tertiary-container/15 p-3 text-sm text-tertiary">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="card card-pad space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h4 class="section-title mb-4">Business Information</h4>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="name" class="form-label">Pharmacy Name <span class="text-error">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $pharmacy->name) }}" required class="form-input" placeholder="e.g. Apollo Pharmacy">
                        @error('name') <span class="mt-1 block text-sm text-error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="owner_name" class="form-label">Owner Name</label>
                        <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name', $pharmacy->owner_name) }}" class="form-input" placeholder="e.g. John Doe">
                    </div>
                    <div>
                        <label for="phone" class="form-label">Contact Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $pharmacy->phone) }}" class="form-input" placeholder="+91 9876543210">
                    </div>
                    <div>
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $pharmacy->email) }}" class="form-input" placeholder="contact@pharmacy.com">
                    </div>
                </div>
            </div>

            <hr class="border-outline-variant/30">

            <div>
                <h4 class="section-title mb-4">Legal Details</h4>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label for="drug_license_number" class="form-label">Drug License No. (DL)</label>
                        <input type="text" name="drug_license_number" id="drug_license_number" value="{{ old('drug_license_number', $pharmacy->drug_license_number) }}" class="form-input" placeholder="MH-MZ1-XXXX">
                    </div>
                    <div>
                        <label for="gst" class="form-label">GST Number</label>
                        <input type="text" name="gst" id="gst" value="{{ old('gst', $pharmacy->gst) }}" class="form-input" placeholder="27XXXXX1234X1ZX">
                    </div>
                    <div>
                        <label for="pan_number" class="form-label">PAN Number</label>
                        <input type="text" name="pan_number" id="pan_number" value="{{ old('pan_number', $pharmacy->pan_number) }}" class="form-input" placeholder="ABCDE1234F">
                    </div>
                </div>
            </div>

            <hr class="border-outline-variant/30">

            <div>
                <h4 class="section-title mb-4">Location Information</h4>
                <div class="space-y-4">
                    <div>
                        <label for="address" class="form-label">Complete Address</label>
                        <textarea name="address" id="address" rows="3" class="form-textarea" placeholder="Shop No. 1, Main Street...">{{ old('address', $pharmacy->address) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label for="city" class="form-label">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $pharmacy->city) }}" class="form-input">
                        </div>
                        <div>
                            <label for="state" class="form-label">State</label>
                            <input type="text" name="state" id="state" value="{{ old('state', $pharmacy->state) }}" class="form-input">
                        </div>
                        <div>
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="text" name="pincode" id="pincode" value="{{ old('pincode', $pharmacy->pincode) }}" class="form-input">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="submit" class="btn btn-primary">Save Profile</button>
            </div>
        </form>
    </div>
</x-app-layout>
