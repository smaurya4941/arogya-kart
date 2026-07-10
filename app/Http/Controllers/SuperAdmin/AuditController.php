<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

/**
 * Platform-wide activity log. Defaults to impersonation events (the support-access
 * trail) but can show every recorded action.
 */
class AuditController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->string('filter')->toString() ?: 'impersonation';

        $logs = AuditLog::query()
            ->with('user')
            ->when($filter === 'impersonation', fn ($q) => $q->where('action', 'like', 'impersonation_%'))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->string('action')))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('superadmin.audit.index', compact('logs', 'filter'));
    }
}
