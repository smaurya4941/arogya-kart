<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Repositories\Interfaces\BatchRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentBatchRepository implements BatchRepositoryInterface
{
    public function getExpiringBatches(int $days = 30): Collection
    {
        return ProductBatch::with('product')
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get();
    }

    public function getExpiringBatchesForProduct(Product $product, int $days = 30): Collection
    {
        return $product->expiringBatches()
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->orderBy('expiry_date')
            ->get();
    }

    public function createForProduct(Product $product, array $data): ProductBatch
    {
        return $product->batches()->create($data);
    }

    public function update(ProductBatch $batch, array $data): ProductBatch
    {
        $batch->update($data);

        return $batch;
    }

    public function delete(ProductBatch $batch): void
    {
        $batch->delete();
    }
}
