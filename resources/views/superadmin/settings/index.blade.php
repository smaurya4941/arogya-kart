@extends('layouts.superadmin')

@section('title', 'Platform Settings')

@section('content')
    <form method="POST" action="{{ route('superadmin.settings.update') }}" class="space-y-4">
        @csrf @method('PUT')

        {{-- Billing defaults --}}
        <div class="card card-pad">
            <h2 class="section-title mb-4">Billing defaults</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label">GST percent (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="gst_percent"
                           value="{{ old('gst_percent', $settings->get('gst_percent', config('saas.gst_percent'))) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Free trial length (days)</label>
                    <input type="number" min="0" max="365" name="trial_days"
                           value="{{ old('trial_days', $settings->get('trial_days', config('saas.trial_days'))) }}" class="form-input">
                </div>
            </div>
        </div>

        {{-- Payment gateway --}}
        <div class="card card-pad">
            <h2 class="section-title mb-1">Payment gateway (Razorpay)</h2>
            <p class="mb-4 text-xs text-on-surface-variant">Secrets are stored encrypted. Leave a secret blank to keep the current value.</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label">Key ID</label>
                    <input type="text" name="razorpay_key" value="{{ old('razorpay_key', $settings->get('razorpay_key')) }}" class="form-input" autocomplete="off">
                </div>
                <div>
                    <label class="form-label">Key secret {!! $settings->hasSecret('razorpay_secret') ? '<span class="text-tertiary">(set)</span>' : '' !!}</label>
                    <input type="password" name="razorpay_secret" placeholder="••••••••" class="form-input" autocomplete="new-password">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Webhook secret {!! $settings->hasSecret('razorpay_webhook_secret') ? '<span class="text-tertiary">(set)</span>' : '' !!}</label>
                    <input type="password" name="razorpay_webhook_secret" placeholder="••••••••" class="form-input" autocomplete="new-password">
                </div>
            </div>
        </div>

        {{-- Mail identity --}}
        <div class="card card-pad">
            <h2 class="section-title mb-4">Email identity</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label">From name</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings->get('mail_from_name', config('mail.from.name'))) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">From address</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings->get('mail_from_address', config('mail.from.address'))) }}" class="form-input">
                </div>
            </div>
        </div>

        {{-- Feature flags --}}
        <div class="card card-pad">
            <h2 class="section-title mb-4">Feature flags</h2>
            <div class="space-y-3">
                @foreach($featureFlags as $key => $label)
                    <label class="flex items-center gap-2 text-sm text-on-surface">
                        <input type="checkbox" name="{{ $key }}" value="1" @checked($settings->bool($key)) class="rounded border-outline-variant text-primary focus:ring-primary/30">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Maintenance mode --}}
        <div class="card card-pad border-l-4 border-amber-500">
            <h2 class="section-title mb-4">Maintenance mode</h2>
            <label class="flex items-center gap-2 text-sm text-on-surface">
                <input type="checkbox" name="maintenance_mode" value="1" @checked($settings->bool('maintenance_mode')) class="rounded border-outline-variant text-primary focus:ring-primary/30">
                Enable maintenance mode (blocks all tenants; Super Admin stays in)
            </label>
            <div class="mt-3">
                <label class="form-label">Message shown to tenants</label>
                <textarea name="maintenance_message" rows="2" class="form-textarea">{{ old('maintenance_message', $settings->get('maintenance_message')) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button class="btn btn-primary">Save settings</button>
        </div>
    </form>
@endsection
