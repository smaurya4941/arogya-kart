<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Contracts\Auth\Authenticatable;

class AuditLogService
{
    public function log(?Authenticatable $user, string $action, $auditable = null, array $meta = []): void
    {
        $data = [
            'user_id' => $user?->getAuthIdentifier(),
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->id ?? null,
            'meta' => $meta,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ];

        AuditLog::create($data);
    }
}
