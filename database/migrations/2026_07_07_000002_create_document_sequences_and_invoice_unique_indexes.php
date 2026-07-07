<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_sequences', function (Blueprint $table) {
            $table->id();
            // Nullable so users without a pharmacy still get a distinct series.
            $table->unsignedBigInteger('pharmacy_id')->nullable();
            $table->string('type');            // e.g. 'sale', 'purchase'
            $table->string('period')->default('all'); // reset bucket
            $table->unsignedBigInteger('next_value')->default(1);
            $table->timestamps();

            // One counter row per tenant/type/period; the lock target lives here.
            $table->unique(['pharmacy_id', 'type', 'period']);
        });

        // Seed counters from existing data so freshly issued numbers continue the
        // series instead of colliding with already-issued invoice numbers.
        $this->seedFrom('sales', 'sale');
        $this->seedFrom('purchase_invoices', 'purchase');

        // Defense-in-depth: even if application logic ever slips, the database
        // refuses two identical invoice numbers within the same tenant.
        Schema::table('sales', function (Blueprint $table) {
            $table->unique(['pharmacy_id', 'invoice_number'], 'sales_pharmacy_invoice_unique');
        });
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->unique(['pharmacy_id', 'invoice_number'], 'purchase_invoices_pharmacy_invoice_unique');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique('sales_pharmacy_invoice_unique');
        });
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropUnique('purchase_invoices_pharmacy_invoice_unique');
        });
        Schema::dropIfExists('document_sequences');
    }

    /**
     * Prime the counter for each pharmacy from the count of existing rows, so the
     * next number issued is (existing count + 1) — matching the prior semantics.
     */
    private function seedFrom(string $sourceTable, string $type): void
    {
        DB::table($sourceTable)
            ->selectRaw('pharmacy_id, COUNT(*) as total')
            ->groupBy('pharmacy_id')
            ->get()
            ->each(function ($row) use ($type) {
                DB::table('document_sequences')->updateOrInsert(
                    ['pharmacy_id' => $row->pharmacy_id, 'type' => $type, 'period' => 'all'],
                    ['next_value' => $row->total + 1, 'created_at' => now(), 'updated_at' => now()]
                );
            });
    }
};
