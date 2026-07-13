<x-app-layout>
    <div class="page mx-auto max-w-4xl">
        <div class="page-header">
            <div>
                <h1 class="page-title">Business Settings</h1>
                <p class="page-subtitle">Manage currency, timezone, and invoice formats.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="flex items-center gap-2 rounded-lg border border-tertiary/30 bg-tertiary-container/15 p-3 text-sm text-tertiary">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" class="card card-pad space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="currency" class="form-label">Currency Settings</label>
                <p class="mb-2 text-xs text-on-surface-variant">Select the default currency for billing and reports.</p>
                <select name="currency" id="currency" class="form-select">
                    <option value="INR" {{ ($settings['currency'] ?? '') == 'INR' ? 'selected' : '' }}>INR (₹) - Indian Rupee</option>
                    <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD ($) - US Dollar</option>
                </select>
            </div>

            <div>
                <label for="date_format" class="form-label">Date Format</label>
                <p class="mb-2 text-xs text-on-surface-variant">Choose how dates are displayed across the system.</p>
                <select name="date_format" id="date_format" class="form-select">
                    <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (e.g. 31/12/2026)</option>
                    <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (e.g. 12/31/2026)</option>
                    <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (e.g. 2026-12-31)</option>
                </select>
            </div>

            <div>
                <label for="timezone" class="form-label">Timezone</label>
                <p class="mb-2 text-xs text-on-surface-variant">Set your local timezone for accurate timestamps.</p>
                <select name="timezone" id="timezone" class="form-select">
                    <option value="Asia/Kolkata" {{ ($settings['timezone'] ?? '') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                    <option value="UTC" {{ ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                </select>
            </div>

            <div>
                <label for="invoice_prefix" class="form-label">Invoice Prefix</label>
                <p class="mb-2 text-xs text-on-surface-variant">The prefix added to all your invoice numbers.</p>
                <input type="text" name="invoice_prefix" id="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV-' }}" class="form-input" placeholder="INV-">
            </div>

            <div>
                <label for="starting_invoice_number" class="form-label">Starting Invoice Number</label>
                <p class="mb-2 text-xs text-on-surface-variant">Set the starting sequence for your next invoice.</p>
                <input type="number" name="starting_invoice_number" id="starting_invoice_number" value="{{ $settings['starting_invoice_number'] ?? '1001' }}" class="form-input" placeholder="1001">
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</x-app-layout>
