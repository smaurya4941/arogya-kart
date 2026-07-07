<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

/**
 * Staff can view expenses so they can understand pharmacy overheads.
 * Creating, editing and deleting expense records is admin-only to
 * preserve P&L accuracy.
 */
class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, Expense $expense): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Expense $expense): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->isAdmin();
    }
}
