<?php

namespace App\Policies;

use App\Models\Credential;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CredentialPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Credential $credential): bool
    {
        // Permitir si el usuario es el DUEÑO del certificado
        return $user->id === $credential->user_id;
    }
}
