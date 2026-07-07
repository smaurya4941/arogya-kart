<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Repositories\Interfaces\BatchRepositoryInterface;

class BatchService
{
    public function __construct(
        private readonly BatchRepositoryInterface $batches,
        private readonly AuditLogService $audit
    ) {}

    public function createBatch(Product $product, array $data): ProductBatch
    {
        $batch = $this->batches->createForProduct($product, $data);
        $this->audit->log(auth()->user(), 'batch_created', $batch, [
            'product_id' => $product->id,
            'batch_number' => $batch->batch_number,
        ]);

        return $batch;
    }

    public function updateBatch(ProductBatch $batch, array $data): ProductBatch
    {
        $batch = $this->batches->update($batch, $data);
        $this->audit->log(auth()->user(), 'batch_updated', $batch, [
            'product_id' => $batch->product_id,
            'batch_number' => $batch->batch_number,
        ]);

        return $batch;
    }

    public function deleteBatch(ProductBatch $batch): void
    {
        $this->batches->delete($batch);
        $this->audit->log(auth()->user(), 'batch_deleted', $batch, [
            'product_id' => $batch->product_id,
            'batch_number' => $batch->batch_number,
        ]);
    }
}
