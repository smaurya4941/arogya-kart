<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'hsn_code')) {
                $table->string('hsn_code')->nullable()->after('barcode');
            }
            if (!Schema::hasColumn('products', 'storage_conditions')) {
                $table->string('storage_conditions')->nullable()->after('hsn_code');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'consent_given')) {
                $table->boolean('consent_given')->default(false)->after('outstanding_balance');
            }
            if (!Schema::hasColumn('customers', 'consent_date')) {
                $table->timestamp('consent_date')->nullable()->after('consent_given');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['hsn_code', 'storage_conditions']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['consent_given', 'consent_date']);
        });
    }
};
