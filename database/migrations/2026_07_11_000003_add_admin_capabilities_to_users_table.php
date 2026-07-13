<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Granular Super-Admin RBAC. A null value means a full platform owner (every
     * capability) — so existing super admins are unaffected. A JSON array scopes a
     * restricted super admin (e.g. a support operator who may view tenants and
     * impersonate but not touch billing) to just the listed capability keys.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('admin_capabilities')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin_capabilities');
        });
    }
};
