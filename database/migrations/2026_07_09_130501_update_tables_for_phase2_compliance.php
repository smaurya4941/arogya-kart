<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('schedule_type')->nullable()->after('drug_type');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('outstanding_balance', 12, 2)->default(0)->after('address');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('doctor_name')->nullable()->after('payment_status');
            $table->string('doctor_registration_number')->nullable()->after('doctor_name');
        });

        Schema::table('pharmacies', function (Blueprint $table) {
            $table->date('drug_license_expiry')->nullable()->after('drug_license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->dropColumn('drug_license_expiry');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['doctor_name', 'doctor_registration_number']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('outstanding_balance');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('schedule_type');
        });
    }
};
