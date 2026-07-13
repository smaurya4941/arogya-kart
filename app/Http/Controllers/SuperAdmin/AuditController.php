<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Platform-wide activity log. Supports filtering by actor, action, quick group
 * (impersonation) and date range, plus CSV export of the filtered set.
 */
class AuditController extends Controller
{
    public function index(Request $request)
    {
        $logs = $this->filtered($request)
            ->with('user')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('superadmin.audit.index', [
            'logs'    => $logs,
            'filter'  => $request->string('filter')->toString() ?: 'all',
            'actions' => $this->distinctActions(),
            'actors'  => User::whereIn('id', AuditLog::query()->distinct()->pluck('user_id')->filter())
                ->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $logs = $this->filtered($request)->with('user')->latest()->limit(5000)->get();

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['When', 'Actor', 'Action', 'Subject', 'Details', 'IP', 'User agent']);

            foreach ($logs as $log) {
                fputcsv($out, [
                    $log->created_at?->format('Y-m-d H:i:s'),
                    $log->user?->name ?? 'System',
                    $log->action,
                    trim(($log->auditable_type ? class_basename($log->auditable_type) : '') . ' ' . ($log->auditable_id ?? '')),
                    is_array($log->meta) ? json_encode($log->meta) : (string) $log->meta,
                    $log->ip_address,
                    $log->user_agent,
                ]);
            }

            fclose($out);
        }, 'audit-log-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Shared filter query used by both the list and the export.
     */
    private function filtered(Request $request): Builder
    {
        $filter = $request->string('filter')->toString();

        return AuditLog::query()
            ->when($filter === 'impersonation', fn ($q) => $q->where('action', 'like', 'impersonation_%'))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->string('action')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')));
    }

    /**
     * @return array<int,string>
     */
    private function distinctActions(): array
    {
        return AuditLog::query()->distinct()->orderBy('action')->pluck('action')->all();
    }
}
