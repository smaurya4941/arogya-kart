<?php

namespace App\Enums;

enum UserRole: string
{
    /**
     * Platform owner. Operates the SaaS itself (all tenants, plans, billing).
     * A Super Admin is NOT bound to any single pharmacy — see BelongsToPharmacy,
     * which lets this role read/write across every tenant.
     */
    case SUPER_ADMIN = 'super_admin';

    /** Pharmacy owner/admin — full control over a single tenant. */
    case ADMIN = 'admin';

    /** Staff member with limited, tenant-scoped access. */
    case STAFF = 'staff';

    /** End customer of a pharmacy. */
    case CLIENT = 'client';

    /** Human-readable label for UI. */
    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN       => 'Pharmacy Owner',
            self::STAFF       => 'Staff',
            self::CLIENT      => 'Customer',
        };
    }
}
