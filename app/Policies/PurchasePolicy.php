<?php

namespace App\Policies;

use App\Models\PurchaseInvoice;
use App\Models\User;

/**
 * Staff members can view purchase invoices (to check stock received).
 * Creating a purchase order and deleting are admin-only operations.
 */
class PurchasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, PurchaseInvoice $purchase): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /** Creating a purchase (which auto-increments stock) is admin-only. */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, PurchaseInvoice $purchase): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, PurchaseInvoice $purchase): bool
    {
        return $user->isAdmin();
    }
}
