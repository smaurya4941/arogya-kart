<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Role-based access middleware alias
        $middleware->alias([
            'role' => App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        /*
        |--------------------------------------------------------------------------
        | Model Not Found → 404
        |--------------------------------------------------------------------------
        | Converts Eloquent's ModelNotFoundException (thrown by findOrFail, etc.)
        | into a clean 404 rather than an unhandled exception page.
        */
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Resource not found.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        /*
        |--------------------------------------------------------------------------
        | Route Not Found → 404
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'The requested URL was not found.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        /*
        |--------------------------------------------------------------------------
        | Authorization Failure → 403
        |--------------------------------------------------------------------------
        | Converts Gate/Policy denials into a branded 403 page instead of the
        | default Laravel forbidden screen.
        */
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not authorized to perform this action.'], 403);
            }
            return response()->view('errors.403', [], 403);
        });

        /*
        |--------------------------------------------------------------------------
        | Log all server errors (5xx) with context
        |--------------------------------------------------------------------------
        | Attaches user ID, URL and a safe subset of inputs so that logs are
        | actionable without leaking passwords or payment data.
        */
        $exceptions->reportable(function (\Throwable $e) {
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

            if ($statusCode >= 500 || !method_exists($e, 'getStatusCode')) {
                Log::channel('daily')->error('Unhandled exception', [
                    'exception' => get_class($e),
                    'message'   => $e->getMessage(),
                    'url'       => request()->fullUrl(),
                    'method'    => request()->method(),
                    'user_id'   => auth()->id(),
                    'inputs'    => request()->except(['password', 'password_confirmation', 'card_number']),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                ]);
            }
        });

    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule): void {
        // Send low-stock & expiry alerts to pharmacy admins every morning
        $schedule->command('pharmacy:stock-alerts')->dailyAt('08:00');

        // Nightly database backup, pruning copies older than 30 days
        $schedule->command('pharmacy:backup')->dailyAt('02:00');
    })
    ->create();
