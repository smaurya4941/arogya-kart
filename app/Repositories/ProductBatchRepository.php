<?php

namespace App\Repositories;

use App\Models\ProductBatch;

class ProductBatchRepository
{
    public function create(array $data)
    {
        return ProductBatch::create($data);
    }

    public function update($id, array $data)
    {
        $batch = ProductBatch::findOrFail($id);
        $batch->update($data);
        return $batch;
    }

    public function delete($id)
    {
        return ProductBatch::destroy($id);
    }

    public function batchesOfProduct($productId)
    {
        return ProductBatch::where('product_id', $productId)->get();
    }
}