<?php

namespace App\Services;

use App\Repositories\ProductBatchRepository;
use Carbon\Carbon;

class ProductBatchService
{
    protected $batchRepository;

    public function __construct(ProductBatchRepository $batchRepository)
    {
        $this->batchRepository = $batchRepository;
    }

    public function createBatch(array $data)
    {
        // Business rule: expiry must be future date
        if (Carbon::parse($data['expiry_date'])->isPast()) {
            throw new \Exception("Cannot add expired batch.");
        }

        return $this->batchRepository->create($data);
    }

    public function updateBatch($id, array $data)
    {
        return $this->batchRepository->update($id, $data);
    }

    public function deleteBatch($id)
    {
        return $this->batchRepository->delete($id);
    }
}