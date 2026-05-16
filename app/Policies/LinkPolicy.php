<?php

namespace App\Policies;

use App\Models\Link;
use App\Models\User;

class LinkPolicy
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
        if (! $user->can('links.update')) {
            return false;
        }

        return $user->hasRole('admin') || $link->created_by === $user->id;
    }

    public function delete(User $user, Link $link): bool
    {
        if (! $user->can('links.delete')) {
            return false;
        }

        return $user->hasRole('admin') || $link->created_by === $user->id;
    }

    public function restore(User $user, Link $link): bool
    {
        return $user->can('links.delete');
    }

    public function forceDelete(User $user, Link $link): bool
    {
        return false;
    }

    public function archive(User $user, Link $link): bool
    {
        return $user->can('links.archive');
    }

    public function open(User $user, Link $link): bool
    {
        return $user->can('links.open') && $this->view($user, $link);
    }

    public function favorite(User $user, Link $link): bool
    {
        return $user->can('favorites.manage') && $this->view($user, $link);
    }
}
