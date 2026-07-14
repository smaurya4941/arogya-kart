<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Pharmacy;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards the onboarding blocker fix end-to-end: a pharmacy admin with a
 * pharmacy_id can add stock and complete a sale. Before the fix, users had no
 * pharmacy and the NOT NULL pharmacy_id on `sales` made this impossible.
 */
class SaleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsPharmacyAdmin(): User
    {
        $pharmacy = Pharmacy::create([
            'name' => 'Green Cross Pharmacy',
            'owner_name' => 'Owner',
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Owner',
            'email' => 'owner@greencross.test',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMIN,
            'pharmacy_id' => $pharmacy->id,
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        return $admin;
    }

    public function test_admin_with_pharmacy_can_complete_a_sale_and_stock_decrements(): void
    {
        $admin = $this->actingAsPharmacyAdmin();

        // Stock in — pharmacy_id is auto-stamped by the BelongsToPharmacy trait.
        $product = Product::create([
            'name' => 'Amoxicillin 500mg',
            'sku' => 'AMX-500',
            'description' => 'Antibiotic',
        ]);

        ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'BATCH-A',
            'expiry_date' => now()->addYear(),
            'purchase_price' => 5.00,
            'mrp' => 10.00,
            'quantity' => 100,
        ]);

        $sale = app(SaleService::class)->createSale([
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
        ]);

        // The sale persisted with the tenant stamped — no NOT NULL violation.
        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'pharmacy_id' => $admin->pharmacy_id,
        ]);
        $this->assertSame(30.0, (float) $sale->total_amount); // 3 × 10.00, 0% GST
        $this->assertSame('paid', $sale->payment_status);

        // Stock went down by exactly what was sold.
        $this->assertSame(97, (int) $product->batches()->sum('quantity'));

        // Invoice number was issued from the atomic per-tenant sequence.
        $this->assertNotEmpty($sale->invoice_number);
    }

    public function test_sale_is_blocked_when_stock_is_insufficient(): void
    {
        $this->actingAsPharmacyAdmin();

        $product = Product::create([
            'name' => 'Cetirizine',
            'sku' => 'CTZ-10',
            'description' => null,
        ]);

        ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'BATCH-B',
            'expiry_date' => now()->addYear(),
            'purchase_price' => 1.00,
            'mrp' => 2.00,
            'quantity' => 2,
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        app(SaleService::class)->createSale([
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ]);
    }

    public function test_sale_is_blocked_when_selling_above_batch_mrp(): void
    {
        $this->actingAsPharmacyAdmin();

        $product = Product::create([
            'name' => 'Paracetamol 650mg',
            'sku' => 'PCM-650',
            'description' => null,
        ]);

        ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'BATCH-C',
            'expiry_date' => now()->addYear(),
            'purchase_price' => 8.00,
            'mrp' => 12.00,
            'quantity' => 50,
        ]);

        // Selling above the printed MRP is illegal — the whole sale must be rejected.
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        app(SaleService::class)->createSale([
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 15.00],
            ],
        ]);
    }

    public function test_sale_at_exactly_mrp_is_allowed(): void
    {
        $this->actingAsPharmacyAdmin();

        $product = Product::create([
            'name' => 'Ibuprofen 400mg',
            'sku' => 'IBU-400',
            'description' => null,
        ]);

        ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'BATCH-D',
            'expiry_date' => now()->addYear(),
            'purchase_price' => 6.00,
            'mrp' => 9.00,
            'quantity' => 20,
        ]);

        // Selling exactly at MRP is legal and must go through.
        $sale = app(SaleService::class)->createSale([
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 9.00],
            ],
        ]);

        $this->assertSame(18.0, (float) $sale->total_amount); // 2 × 9.00, 0% GST
    }
}
