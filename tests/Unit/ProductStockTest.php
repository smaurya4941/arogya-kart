<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_stock_is_sum_of_batch_quantities(): void
    {
        $product = Product::create([
            'name' => 'Paracetamol',
            'sku' => 'PCM-100',
            'description' => 'Pain relief',
        ]);

        ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'B-001',
            'expiry_date' => now()->addMonths(6),
            'purchase_price' => 10.00,
            'mrp' => 15.00,
            'quantity' => 20,
        ]);

        ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'B-002',
            'expiry_date' => now()->addMonths(3),
            'purchase_price' => 10.00,
            'mrp' => 15.00,
            'quantity' => 30,
        ]);

        $product->refresh();

        $this->assertSame(50, $product->total_stock);
        $this->assertFalse($product->isOutOfStock());
    }

    public function test_product_is_out_of_stock_when_total_is_zero(): void
    {
        $product = Product::create([
            'name' => 'Ibuprofen',
            'sku' => 'IBU-200',
            'description' => null,
        ]);

        ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'B-003',
            'expiry_date' => now()->addMonths(12),
            'purchase_price' => 8.00,
            'mrp' => 12.00,
            'quantity' => 0,
        ]);

        $product->refresh();

        $this->assertSame(0, $product->total_stock);
        $this->assertTrue($product->isOutOfStock());
    }
}
