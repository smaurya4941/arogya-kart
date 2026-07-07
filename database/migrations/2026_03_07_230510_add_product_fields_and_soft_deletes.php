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
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete()->after('id');
            $table->string('barcode')->nullable()->unique()->after('sku');
            $table->string('drug_type')->nullable()->after('description');
            $table->string('image_path')->nullable()->after('drug_type');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'barcode', 'drug_type', 'image_path']);
        });
    }
};
