<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

/**
 * Catalogue access is capability-driven:
 *   - 'view medicines'   → Pharmacist, Staff (Cashiers only bill, so they can't
 *                          browse the catalogue).
 *   - 'create/edit/delete medicines' → Pharmacist (and the owner, implicitly).
 * The pharmacy owner passes every check via User::canDo().
 */
class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDo('view medicines');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->canDo('view medicines');
    }

    public function create(User $user): bool
    {
        return $user->canDo('create medicines');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->canDo('edit medicines');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->canDo('delete medicines');
    }

    /** Manual stock issue is part of inventory upkeep — same as editing stock. */
    public function issueStock(User $user, Product $product): bool
    {
        return $user->canDo('edit medicines');
    }
}
