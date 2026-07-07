<?php

namespace App\Policies;

use App\Models\ProductBatch;
use App\Models\User;

class ProductBatchPolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ProductBatch $batch): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ProductBatch $batch): bool
    {
        return $user->isAdmin();
    }
}
