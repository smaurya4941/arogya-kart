<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight tracking-tight">
            {{ __('Business Settings') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-xl p-4 flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-8 md:p-10">
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">System Configurations</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage currency, timezone, and invoice formats.</p>
                    </div>

                    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Currency -->
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                                <label for="currency" class="block text-sm font-semibold text-gray-900 dark:text-gray-200 mb-1">Currency Settings</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Select the default currency for billing and reports.</p>
                                <select name="currency" id="currency" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                    <option value="INR" {{ ($settings['currency'] ?? '') == 'INR' ? 'selected' : '' }}>INR (₹) - Indian Rupee</option>
                                    <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD ($) - US Dollar</option>
                                </select>
                            </div>

                            <!-- Date Format -->
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                                <label for="date_format" class="block text-sm font-semibold text-gray-900 dark:text-gray-200 mb-1">Date Format</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Choose how dates are displayed across the system.</p>
                                <select name="date_format" id="date_format" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                    <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (e.g. 31/12/2026)</option>
                                    <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (e.g. 12/31/2026)</option>
                                    <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (e.g. 2026-12-31)</option>
                                </select>
                            </div>

                            <!-- Timezone -->
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                                <label for="timezone" class="block text-sm font-semibold text-gray-900 dark:text-gray-200 mb-1">Timezone</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Set your local timezone for accurate timestamps.</p>
                                <select name="timezone" id="timezone" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors">
                                    <option value="Asia/Kolkata" {{ ($settings['timezone'] ?? '') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                                    <option value="UTC" {{ ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <!-- Add more timezones as needed -->
                                </select>
                            </div>

                            <!-- Invoice Prefix -->
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                                <label for="invoice_prefix" class="block text-sm font-semibold text-gray-900 dark:text-gray-200 mb-1">Invoice Prefix</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">The prefix added to all your invoice numbers.</p>
                                <input type="text" name="invoice_prefix" id="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV-' }}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors" placeholder="INV-">
                            </div>

                            <!-- Starting Invoice Number -->
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                                <label for="starting_invoice_number" class="block text-sm font-semibold text-gray-900 dark:text-gray-200 mb-1">Starting Invoice Number</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Set the starting sequence for your next invoice.</p>
                                <input type="number" name="starting_invoice_number" id="starting_invoice_number" value="{{ $settings['starting_invoice_number'] ?? '1001' }}" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors" placeholder="1001">
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-6">
                            <button type="submit" class="inline-flex justify-center rounded-xl border border-transparent bg-blue-600 py-3 px-8 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-105">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
