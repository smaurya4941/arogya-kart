<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\RazorpayService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * System health & operations dashboard: infrastructure checks, background-queue
 * status, failed-job management and payment-webhook readiness. Read-only apart
 * from the failed-job retry/flush actions.
 */
class SystemController extends Controller
{
    public function index(RazorpayService $razorpay)
    {
        return view('superadmin.system.index', [
            'checks'      => $this->healthChecks($razorpay),
            'queue'       => $this->queueStatus(),
            'failedJobs'  => $this->recentFailedJobs(),
            'environment' => [
                'PHP'         => PHP_VERSION,
                'Laravel'     => app()->version(),
                'Environment' => app()->environment(),
                'Debug mode'  => config('app.debug') ? 'On' : 'Off',
                'Queue driver'=> config('queue.default'),
                'Cache driver'=> config('cache.default'),
            ],
        ]);
    }

    /** Retry all failed jobs, then refresh. */
    public function retryFailed()
    {
        Artisan::call('queue:retry', ['id' => ['all']]);

        return back()->with('success', 'Queued all failed jobs for retry.');
    }

    /** Permanently clear the failed-jobs table. */
    public function flushFailed()
    {
        Artisan::call('queue:flush');

        return back()->with('success', 'Failed-jobs log cleared.');
    }

    /*
    |--------------------------------------------------------------------------
    | Checks
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<int,array{label:string,ok:bool,detail:string}>
     */
    private function healthChecks(RazorpayService $razorpay): array
    {
        return [
            $this->check('Database', function () {
                DB::connection()->getPdo();
                return 'Connected (' . DB::connection()->getDatabaseName() . ')';
            }),
            $this->check('Cache', function () {
                Cache::put('health_probe', '1', 5);
                return Cache::get('health_probe') === '1' ? 'Read/write OK' : throw new \RuntimeException('probe failed');
            }),
            $this->check('Storage (local)', function () {
                Storage::disk('local')->put('health_probe.txt', 'ok');
                $ok = Storage::disk('local')->get('health_probe.txt') === 'ok';
                Storage::disk('local')->delete('health_probe.txt');
                return $ok ? 'Writable' : throw new \RuntimeException('not writable');
            }),
            [
                'label'  => 'Payment gateway',
                'ok'     => $razorpay->isConfigured(),
                'detail' => $razorpay->isConfigured() ? 'Razorpay keys configured' : 'Not configured',
            ],
            [
                'label'  => 'Webhook secret',
                'ok'     => ! empty(config('services.razorpay.webhook_secret')),
                'detail' => ! empty(config('services.razorpay.webhook_secret')) ? 'Set — signatures verifiable' : 'Missing — webhooks will be rejected',
            ],
            [
                'label'  => 'Mail',
                'ok'     => ! empty(config('mail.from.address')),
                'detail' => 'Driver: ' . config('mail.default') . ' · From: ' . (config('mail.from.address') ?: 'unset'),
            ],
        ];
    }

    /**
     * @return array{pending:int,failed:int}
     */
    private function queueStatus(): array
    {
        return [
            'pending' => $this->safeCount('jobs'),
            'failed'  => $this->safeCount('failed_jobs'),
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int,object>
     */
    private function recentFailedJobs()
    {
        if (! $this->tableExists('failed_jobs')) {
            return collect();
        }

        return DB::table('failed_jobs')
            ->latest('failed_at')
            ->limit(10)
            ->get()
            ->map(function ($job) {
                $payload = json_decode($job->payload, true);
                $exception = (string) $job->exception;

                return (object) [
                    'uuid'      => $job->uuid,
                    'name'      => $payload['displayName'] ?? 'Unknown job',
                    'queue'     => $job->queue,
                    'failed_at' => $job->failed_at,
                    'error'     => str($exception)->before("\n")->limit(160)->value(),
                ];
            });
    }

    /**
     * Wrap a probe so a thrown exception becomes a failed check rather than a 500.
     *
     * @param  callable():string  $probe
     * @return array{label:string,ok:bool,detail:string}
     */
    private function check(string $label, callable $probe): array
    {
        try {
            return ['label' => $label, 'ok' => true, 'detail' => $probe()];
        } catch (\Throwable $e) {
            return ['label' => $label, 'ok' => false, 'detail' => $e->getMessage()];
        }
    }

    private function safeCount(string $table): int
    {
        return $this->tableExists($table) ? (int) DB::table($table)->count() : 0;
    }

    private function tableExists(string $table): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }
}
