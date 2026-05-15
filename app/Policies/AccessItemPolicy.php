<?php

namespace App\Policies;

use App\Models\AccessItem;
use App\Models\User;

class AccessItemPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('access_items.view');
    }

    public function view(User $user, AccessItem $accessItem): bool
    {
        return $user->can('access_items.view') && $accessItem->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $user->can('access_items.create');
    }

    public function update(User $user, AccessItem $accessItem): bool
    {
        return $user->can('access_items.update');
    }

    public function delete(User $user, AccessItem $accessItem): bool
    {
        return $user->can('access_items.delete');
    }

    public function restore(User $user, AccessItem $accessItem): bool
    {
        return $user->can('access_items.delete');
    }

    public function forceDelete(User $user, AccessItem $accessItem): bool
    {
        return false;
    }

    public function archive(User $user, AccessItem $accessItem): bool
    {
        return $user->can('access_items.archive');
    }

    public function open(User $user, AccessItem $accessItem): bool
    {
        return $user->can('access_items.open') && $this->view($user, $accessItem);
    }
}
