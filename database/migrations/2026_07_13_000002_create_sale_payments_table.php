<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. We must change the ENUM to add 'split' and 'multiple'.
        // SQLite doesn't support altering ENUM directly well, but this is a Laravel app.
        // Assuming MySQL/PostgreSQL, we can modify it. However, string is safer for compatibility.
        // Since it's a string column on MySQL if created with string, but it was created with enum.
        // Let's use DB statement if needed, or simply modify the column to string.
        Schema::table('sales', function (Blueprint $table) {
            $table->string('payment_method')->change();
        });

        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method');
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
        // We do not revert payment_method change as reverting from string back to restricted ENUM can lose data.
    }
};
