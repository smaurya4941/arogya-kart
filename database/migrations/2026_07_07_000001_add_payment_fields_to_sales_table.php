<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase B (POS): the original sales table only tracked totals and a payment
 * method. A real till needs to know how much was actually collected so we can
 * support part-paid credit bills, so we add the paid/due split and a derived
 * status alongside a free-text notes field.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('paid_amount', 12, 2)->default(0)->after('total_amount');
            $table->decimal('due_amount', 12, 2)->default(0)->after('paid_amount');
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])
                ->default('paid')
                ->after('due_amount');
            $table->text('notes')->nullable()->after('payment_status');

            $table->index(['pharmacy_id', 'sale_date']);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['pharmacy_id', 'sale_date']);
            $table->dropColumn(['paid_amount', 'due_amount', 'payment_status', 'notes']);
        });
    }
};
