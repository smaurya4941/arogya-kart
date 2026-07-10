<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The paywall. Blocks a tenant from the app once its trial/paid subscription
 * lapses (or if the pharmacy itself has been suspended by the platform owner),
 * redirecting to the billing page so they can renew.
 *
 * A small allow-list keeps the billing, profile and logout routes reachable even
 * when access has lapsed — otherwise the user would be trapped in a redirect
 * loop with no way to pay. Super Admins are never subject to this gate.
 */
class EnsureSubscriptionActive
{
    /**
     * Route-name suffixes always permitted regardless of subscription state.
     * These are checked against the segment after the "admin." prefix.
     */
    private const ALLOWED = [
        'subscription.index',
        'subscription.subscribe',
        'subscription.checkout',
        'subscription.callback',
        'billing.index',
        'profile.edit',
        'profile.update',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Unauthenticated or platform owner → nothing to gate here.
        if (! $user || $user->isSuperAdmin()) {
            return $next($request);
        }

        // Always let the user reach billing/profile/logout so they can recover.
        $routeName = optional($request->route())->getName() ?? '';
        foreach (self::ALLOWED as $allowed) {
            if (str_ends_with($routeName, $allowed)) {
                return $next($request);
            }
        }

        $pharmacy = $user->pharmacy;

        // Platform owner disabled the tenant entirely.
        if ($pharmacy && ! $pharmacy->isActive()) {
            return $this->deny($request, 'Your pharmacy account has been suspended. Please contact support.');
        }

        if (! $pharmacy || ! $pharmacy->hasValidSubscription()) {
            return $this->deny($request, 'Your subscription has expired. Please choose a plan to continue.');
        }

        return $next($request);
    }

    private function deny(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 402); // Payment Required
        }

        return redirect()
            ->route('admin.subscription.index')
            ->with('warning', $message);
    }
}
