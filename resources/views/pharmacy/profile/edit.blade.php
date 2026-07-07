<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight tracking-tight">
            {{ __('Pharmacy Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Alert Component -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-xl p-4 flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-8 md:p-10">
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Business Information</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update your pharmacy's legal and contact details. This information will appear on invoices.</p>
                    </div>

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- General Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pharmacy Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $pharmacy->name) }}" required class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors" placeholder="e.g. Apollo Pharmacy">
                                @error('name') <span class="text-sm text-red-600 dark:text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="owner_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Owner Name</label>
                                <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name', $pharmacy->owner_name) }}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors" placeholder="e.g. John Doe">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Number</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $pharmacy->phone) }}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors" placeholder="+91 9876543210">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $pharmacy->email) }}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors" placeholder="contact@pharmacy.com">
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700">

                        <!-- Legal Information -->
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Legal Details</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="drug_license_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Drug License No. (DL)</label>
                                    <input type="text" name="drug_license_number" id="drug_license_number" value="{{ old('drug_license_number', $pharmacy->drug_license_number) }}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors" placeholder="MH-MZ1-XXXX">
                                </div>

                                <div>
                                    <label for="gst" class="block text-sm font-medium text-gray-700 dark:text-gray-300">GST Number</label>
                                    <input type="text" name="gst" id="gst" value="{{ old('gst', $pharmacy->gst) }}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors" placeholder="27XXXXX1234X1ZX">
                                </div>

                                <div>
                                    <label for="pan_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">PAN Number</label>
                                    <input type="text" name="pan_number" id="pan_number" value="{{ old('pan_number', $pharmacy->pan_number) }}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors" placeholder="ABCDE1234F">
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700">

                        <!-- Address -->
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Location Information</h4>
                            <div class="space-y-6">
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Complete Address</label>
                                    <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors" placeholder="Shop No. 1, Main Street...">{{ old('address', $pharmacy->address) }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                                        <input type="text" name="city" id="city" value="{{ old('city', $pharmacy->city) }}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                    </div>
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">State</label>
                                        <input type="text" name="state" id="state" value="{{ old('state', $pharmacy->state) }}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                    </div>
                                    <div>
                                        <label for="pincode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pincode</label>
                                        <input type="text" name="pincode" id="pincode" value="{{ old('pincode', $pharmacy->pincode) }}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-6">
                            <button type="submit" class="inline-flex justify-center rounded-xl border border-transparent bg-blue-600 py-3 px-8 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-105">
                                Save Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
