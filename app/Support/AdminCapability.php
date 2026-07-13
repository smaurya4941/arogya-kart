<?php

namespace App\Support;

/**
 * The catalogue of granular Super-Admin capabilities. Each key gates one section
 * of the platform panel (its routes and its nav item). A full super admin holds
 * every capability implicitly (User::admin_capabilities is null); a restricted
 * super admin holds only the keys stored on their account.
 *
 * Keep this list, the route middleware (`admin.can:<key>`) and the sidebar nav
 * in lock-step — a capability is only meaningful if all three reference it.
 */
final class AdminCapability
{
    public const PHARMACIES    = 'pharmacies';
    public const OPERATIONS    = 'operations';
    public const USERS         = 'users';
    public const BILLING       = 'billing';
    public const IMPERSONATE   = 'impersonate';
    public const ANNOUNCEMENTS = 'announcements';
    public const AUDIT         = 'audit';
    public const SYSTEM        = 'system';
    public const SETTINGS      = 'settings';

    /**
     * Human-readable label + description for each capability, in display order.
     * Used by the user form (capability checkboxes) and anywhere we list them.
     *
     * @return array<string,array{label:string,description:string}>
     */
    public static function catalogue(): array
    {
        return [
            self::PHARMACIES    => ['label' => 'Tenant management', 'description' => 'View, onboard, edit, suspend and archive pharmacies.'],
            self::OPERATIONS    => ['label' => 'Operational visibility', 'description' => 'Browse cross-tenant products, sales, purchases, customers and expenses.'],
            self::USERS         => ['label' => 'Platform users', 'description' => 'Manage every user account across all tenants.'],
            self::BILLING       => ['label' => 'Billing & plans', 'description' => 'Subscriptions, invoices, plans and coupons.'],
            self::IMPERSONATE   => ['label' => 'Impersonate tenants', 'description' => 'Log in as a pharmacy owner to provide support.'],
            self::ANNOUNCEMENTS => ['label' => 'Announcements', 'description' => 'Broadcast messages to tenants.'],
            self::AUDIT         => ['label' => 'Activity log', 'description' => 'View and export the platform audit trail.'],
            self::SYSTEM        => ['label' => 'System health', 'description' => 'Queue, failed jobs and health diagnostics.'],
            self::SETTINGS      => ['label' => 'Platform settings', 'description' => 'Gateway keys, tax, mail, feature flags and maintenance mode.'],
        ];
    }

    /** @return array<int,string> All capability keys. */
    public static function all(): array
    {
        return array_keys(self::catalogue());
    }

    public static function label(string $key): string
    {
        return self::catalogue()[$key]['label'] ?? ucfirst($key);
    }
}
