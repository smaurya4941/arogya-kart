<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

/**
 * Staff can view and create customers (needed at the POS counter).
 * Editing and deleting customer records is admin-only.
 */
class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->isAdmin();
    }
}
