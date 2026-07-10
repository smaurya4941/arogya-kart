<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDrugLicenseExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pharmacy = auth()->user()?->pharmacy;

        if ($pharmacy && $pharmacy->drug_license_expiry && $pharmacy->drug_license_expiry < now()->startOfDay()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Pharmacy drug license has expired.'], 403);
            }
            return redirect()->route('admin.dashboard')->with('error', 'Your drug license has expired. Billing operations are suspended.');
        }

        return $next($request);
    }
}
