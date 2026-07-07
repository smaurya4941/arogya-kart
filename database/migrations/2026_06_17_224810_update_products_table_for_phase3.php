<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'generic_name')) {
                $table->string('generic_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete()->after('category_id');
            }
            if (!Schema::hasColumn('products', 'manufacturer_id')) {
                $table->foreignId('manufacturer_id')->nullable()->constrained()->nullOnDelete()->after('unit_id');
            }
            if (!Schema::hasColumn('products', 'purchase_price')) {
                $table->decimal('purchase_price', 10, 2)->nullable()->after('drug_type');
                $table->decimal('selling_price', 10, 2)->nullable()->after('purchase_price');
                $table->decimal('tax_percentage', 5, 2)->default(0)->after('selling_price');
                $table->integer('reorder_level')->default(10)->after('tax_percentage');
                $table->integer('min_stock_alert')->default(5)->after('reorder_level');
                $table->string('medicine_code')->nullable()->after('barcode');
                $table->enum('status', ['active', 'inactive'])->default('active')->after('min_stock_alert');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['manufacturer_id']);
            $table->dropColumn([
                'generic_name', 'unit_id', 'manufacturer_id', 'purchase_price', 
                'selling_price', 'tax_percentage', 'reorder_level', 'min_stock_alert', 
                'medicine_code', 'status'
            ]);
        });
    }
};
