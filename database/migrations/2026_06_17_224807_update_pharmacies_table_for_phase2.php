<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->string('owner_name')->nullable();
            $table->string('drug_license_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('invoice_header')->nullable();
            $table->string('footer_text')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->dropColumn([
                'owner_name', 'drug_license_number', 'pan_number', 
                'address', 'city', 'state', 'pincode', 
                'logo_path', 'invoice_header', 'footer_text'
            ]);
        });
    }
};
