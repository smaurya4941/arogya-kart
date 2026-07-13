<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates a platform-panel route to a specific Super-Admin capability.
 *
 * Usage: `admin.can:billing`. Runs after `role:super_admin`, so the caller is
 * already known to be a super admin; this narrows access to the restricted
 * (support-style) super admins who hold only a subset of capabilities. A full
 * super admin (User::admin_capabilities null) passes every check.
 */
class SuperAdminCapability
{
    public function handle(Request $request, Closure $next, string $capability): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasAdminCapability($capability)) {
            abort(403, 'You do not have access to this area.');
        }

        return $next($request);
    }
}
