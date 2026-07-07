<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

/**
 * Staff members can create and view sales (they run the POS till).
 * Only admins can delete or void a sale to prevent fraud.
 */
class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Sale $sale): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /** Voiding/deleting a sale is admin-only to prevent till fraud. */
    public function delete(User $user, Sale $sale): bool
    {
        return $user->isAdmin();
    }

    /** Editing a finalised sale is admin-only. */
    public function update(User $user, Sale $sale): bool
    {
        return $user->isAdmin();
    }
}
