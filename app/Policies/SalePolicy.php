<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

/**
 * Till operations:
 *   - 'view sale'   → everyone on the floor (Cashier, Pharmacist, Staff).
 *   - 'create sale' → Cashier, Pharmacist (Staff can look but not ring up).
 * Voiding/editing a finalised sale stays owner-only to prevent till fraud.
 */
class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDo('view sale');
    }

    public function view(User $user, Sale $sale): bool
    {
        return $user->canDo('view sale');
    }

    public function create(User $user): bool
    {
        return $user->canDo('create sale');
    }

    /** Voiding/deleting a sale is owner-only to prevent till fraud. */
    public function delete(User $user, Sale $sale): bool
    {
        return $user->isAdmin();
    }

    /** Editing a finalised sale is owner-only. */
    public function update(User $user, Sale $sale): bool
    {
        return $user->isAdmin();
    }
}
