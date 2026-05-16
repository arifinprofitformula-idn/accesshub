<?php

namespace App\Policies;

use App\Models\Link;
use App\Models\User;

class LinkPolicy
{
    public function before(User $user, string $_ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('links.view');
    }

    public function view(User $user, Link $link): bool
    {
        return $user->can('links.view') && $link->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $user->can('links.create');
    }

    public function update(User $user, Link $link): bool
    {
        return $user->can('links.update') && $link->isOwnedBy($user);
    }

    public function delete(User $user, Link $link): bool
    {
        return $user->can('links.delete') && $link->isOwnedBy($user);
    }

    public function restore(User $user, Link $link): bool
    {
        return $user->can('links.delete') && $link->isOwnedBy($user);
    }

    public function forceDelete(User $_user, Link $_link): bool
    {
        return false;
    }

    public function archive(User $user, Link $link): bool
    {
        return $user->can('links.archive') && $link->isOwnedBy($user);
    }

    public function open(User $user, Link $link): bool
    {
        return $user->can('links.open') && $link->isVisibleTo($user);
    }

    public function favorite(User $user, Link $link): bool
    {
        return $user->can('favorites.manage') && $link->isVisibleTo($user);
    }
}
