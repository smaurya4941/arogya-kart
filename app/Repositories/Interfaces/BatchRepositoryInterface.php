<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Support\Collection;

interface BatchRepositoryInterface
{
    public function getExpiringBatches(int $days = 30): Collection;

    public function getExpiringBatchesForProduct(Product $product, int $days = 30): Collection;

    public function createForProduct(Product $product, array $data): ProductBatch;

    public function update(ProductBatch $batch, array $data): ProductBatch;

    public function delete(ProductBatch $batch): void;
}
