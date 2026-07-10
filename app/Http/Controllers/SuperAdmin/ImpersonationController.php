<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Lets the platform owner "log in as" a pharmacy to reproduce/support issues,
 * then return to their own account. The original Super Admin id is stashed in the
 * session so the return trip is authenticated safely — we never trust a client-
 * supplied id to switch back. Every start/stop is written to the audit log so
 * support access to tenant data is fully accountable.
 */
class ImpersonationController extends Controller
{
    public const SESSION_KEY = 'impersonator_id';

    public function __construct(
        private readonly AuditLogService $audit
    ) {}

    /** Begin impersonating a pharmacy's owner. */
    public function start(Request $request, Pharmacy $pharmacy)
    {
        // Refuse to nest impersonation sessions.
        if ($request->session()->has(self::SESSION_KEY)) {
            return back()->with('error', 'You are already impersonating a pharmacy.');
        }

        $owner = $pharmacy->users()
            ->where('role', UserRole::ADMIN->value)
            ->orderBy('id')
            ->first();

        if (! $owner) {
            return back()->with('error', 'This pharmacy has no owner account to impersonate.');
        }

        $superAdmin = Auth::user();

        // Attribute the action to the Super Admin (before the identity switch).
        $this->audit->log($superAdmin, 'impersonation_started', $pharmacy, [
            'pharmacy_id'      => $pharmacy->id,
            'pharmacy_name'    => $pharmacy->name,
            'impersonated_user_id' => $owner->id,
            'impersonated_email'   => $owner->email,
        ]);

        // Regenerate the session id first (guards against fixation on the identity
        // switch); regenerate() preserves session data, so the key we store next
        // survives the login below.
        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, Auth::id());

        Auth::login($owner);

        return redirect('/admin/dashboard')
            ->with('success', "You are now impersonating {$pharmacy->name}.");
    }

    /** Return to the original Super Admin account. */
    public function stop(Request $request)
    {
        $originalId = $request->session()->pull(self::SESSION_KEY);

        if (! $originalId) {
            return redirect('/dashboard');
        }

        $superAdmin = User::find($originalId);

        if (! $superAdmin || ! $superAdmin->isSuperAdmin()) {
            Auth::logout();
            return redirect()->route('login');
        }

        // Capture who was being impersonated before switching back.
        $impersonated = Auth::user();

        Auth::login($superAdmin);
        $request->session()->regenerate();

        $this->audit->log($superAdmin, 'impersonation_stopped', $impersonated, [
            'impersonated_user_id' => $impersonated?->id,
            'impersonated_email'   => $impersonated?->email,
            'pharmacy_id'          => $impersonated?->pharmacy_id,
        ]);

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'Returned to your platform admin account.');
    }
}
