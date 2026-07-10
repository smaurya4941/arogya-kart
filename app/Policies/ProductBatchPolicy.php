<?php

namespace App\Policies;

use App\Models\ProductBatch;
use App\Models\User;

/**
 * Batch/stock intake is inventory work — anyone who may edit medicines may manage
 * batches (Pharmacist + owner). Deleting a batch is destructive and stays owner-
 * only so stock history can't be quietly rewritten.
 */
class ProductBatchPolicy
{
    public function create(User $user): bool
    {
        return $user->canDo('edit medicines');
    }

    public function update(User $user, ProductBatch $batch): bool
    {
        return $user->canDo('edit medicines');
    }

    public function delete(User $user, ProductBatch $batch): bool
    {
        return $user->isAdmin();
    }
}
