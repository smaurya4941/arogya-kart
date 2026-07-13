<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use App\Services\PlatformSettings;
use Illuminate\Http\Request;

/**
 * Global platform configuration: billing defaults, payment-gateway credentials,
 * mail identity, feature flags and maintenance mode. Secrets are write-only in
 * the UI (blank submit = keep current) and stored encrypted by PlatformSettings.
 */
class SettingsController extends Controller
{
    /** Feature flags exposed on the settings page: key => label. */
    public const FEATURE_FLAGS = [
        'feature_coupons'      => 'Coupon redemption at checkout',
        'feature_pdf_reports'  => 'PDF report exports',
        'feature_impersonation' => 'Super Admin impersonation',
    ];

    public function __construct(
        private readonly PlatformSettings $settings,
        private readonly AuditLogService $audit,
    ) {}

    public function index()
    {
        return view('superadmin.settings.index', [
            'settings'     => $this->settings,
            'featureFlags' => self::FEATURE_FLAGS,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'gst_percent'             => ['nullable', 'numeric', 'min:0', 'max:100'],
            'trial_days'              => ['nullable', 'integer', 'min:0', 'max:365'],
            'mail_from_name'          => ['nullable', 'string', 'max:255'],
            'mail_from_address'       => ['nullable', 'email', 'max:255'],
            'razorpay_key'            => ['nullable', 'string', 'max:255'],
            'razorpay_secret'         => ['nullable', 'string', 'max:255'],
            'razorpay_webhook_secret' => ['nullable', 'string', 'max:255'],
            'maintenance_mode'        => ['nullable', 'boolean'],
            'maintenance_message'     => ['nullable', 'string', 'max:500'],
        ]);

        // Plain (non-secret) settings — always overwrite with the submitted value.
        $this->settings->setMany([
            'gst_percent'         => $validated['gst_percent'] ?? null,
            'trial_days'          => $validated['trial_days'] ?? null,
            'mail_from_name'      => $validated['mail_from_name'] ?? null,
            'mail_from_address'   => $validated['mail_from_address'] ?? null,
            'razorpay_key'        => $validated['razorpay_key'] ?? null,
            'maintenance_message' => $validated['maintenance_message'] ?? null,
            'maintenance_mode'    => $request->boolean('maintenance_mode') ? '1' : null,
        ]);

        // Secrets: a blank field means "leave the stored secret untouched" so the
        // operator never has to re-enter credentials just to change another field.
        foreach (['razorpay_secret', 'razorpay_webhook_secret'] as $secret) {
            if (filled($validated[$secret] ?? null)) {
                $this->settings->set($secret, $validated[$secret]);
            }
        }

        // Feature flags (checkboxes).
        foreach (array_keys(self::FEATURE_FLAGS) as $flag) {
            $this->settings->set($flag, $request->boolean($flag) ? '1' : null);
        }

        $this->audit->log(auth()->user(), 'platform_settings_updated', null, [
            'maintenance_mode' => $request->boolean('maintenance_mode'),
        ]);

        return back()->with('success', 'Platform settings saved.');
    }
}
