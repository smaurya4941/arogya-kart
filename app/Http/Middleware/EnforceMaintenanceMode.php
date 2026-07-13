<?php

namespace App\Http\Middleware;

use App\Services\PlatformSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When the platform is in maintenance mode, tenant traffic is served a 503
 * maintenance page. The Super Admin is always exempt (so the platform stays
 * operable), as are the auth screens and the Super Admin panel itself — this
 * keeps a route back in for whoever needs to turn maintenance off.
 */
class EnforceMaintenanceMode
{
    public function __construct(
        private readonly PlatformSettings $settings
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->settings->bool('maintenance_mode')) {
            return $next($request);
        }

        $user = $request->user();

        $exempt = ($user && $user->isSuperAdmin())
            || $request->is('superadmin/*')
            || $request->routeIs('login', 'logout')
            || $request->is('up'); // health check endpoint

        if ($exempt) {
            return $next($request);
        }

        $message = $this->settings->get('maintenance_message')
            ?: 'We are performing scheduled maintenance. Please check back shortly.';

        return response()->view('errors.maintenance', ['message' => $message], 503);
    }
}
