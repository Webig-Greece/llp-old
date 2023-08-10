<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, User $targetUser)
    {
        return $user->hasRole('admin') || $user->id === $targetUser->id;
    }

    public function delete(User $user)
    {
        return $user->hasRole('admin');
    }
}
