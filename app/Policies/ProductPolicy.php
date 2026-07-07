<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

/**
 * Staff can view and search products (needed for POS autocomplete).
 * Only admins can add, edit or remove a product from the catalogue.
 */
class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    /** Issuing stock manually is an admin operation. */
    public function issueStock(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }
}
