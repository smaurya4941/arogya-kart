<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Soft-delete support for tenants. The platform owner can "delete" a pharmacy
     * from the panel to lock it out (its users lose access via the subscription
     * gate) while retaining all data for a later restore — no hard purge of a
     * tenant's sales/inventory history from an admin click.
     */
    public function up(): void
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
