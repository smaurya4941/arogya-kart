<?php

namespace App\Policies;

use App\Models\SaleReturn;
use App\Models\User;

/**
 * Returns are handled by whoever holds 'return sale' (Pharmacist + owner). Anyone
 * who can view sales can view the resulting credit notes.
 */
class SaleReturnPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDo('view sale');
    }

    public function view(User $user, SaleReturn $saleReturn): bool
    {
        return $user->canDo('view sale');
    }

    public function create(User $user): bool
    {
        return $user->canDo('return sale');
    }
}
