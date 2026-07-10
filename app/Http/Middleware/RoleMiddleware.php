<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Gate a route to one or more enum roles.
     *
     * Usage: `role:admin` (single) or `role:admin,staff` (any of). Passing several
     * roles lets the owner and their staff share the same operational routes while
     * the controller policies enforce the finer, per-action rules.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $userRole  = auth()->user()->role;
        $roleValue = $userRole instanceof \BackedEnum ? $userRole->value : $userRole;

        if (! in_array($roleValue, $roles, true)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
