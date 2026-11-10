<?php

namespace App\Policies;

use App\Models\Pension;
use App\Models\User;

class PensionPolicy
{
    public function view(User $user, Pension $pension): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->colegio_id !== $pension->colegio_id) {
            return false;
        }

        return $user->hasAnyRole(['administrador', 'caja'])
            || $pension->matricula->estudiante?->user_id === $user->id
            || $user->hijos()->whereKey($pension->matricula->estudiante_id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrador', 'superadmin']);
    }
}
