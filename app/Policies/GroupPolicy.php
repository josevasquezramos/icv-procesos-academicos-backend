<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede completar el grupo.
     */
    public function complete(User $user, Group $group): bool
    {
        // Permitir si el usuario es un profesor asignado a ese grupo
        return $group->teachers()->where('user_id', $user->id)->exists();
    }
}
