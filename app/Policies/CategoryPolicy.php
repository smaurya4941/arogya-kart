<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDo('view medicines');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->canDo('view medicines');
    }

    public function create(User $user): bool
    {
        return $user->canDo('create medicines');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->canDo('edit medicines');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->canDo('delete medicines');
    }
}
