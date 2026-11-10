<?php

namespace App\Policies;

use App\Models\Matricula;
use App\Models\User;

class MatriculaPolicy
{
    public function view(User $user, Matricula $matricula): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->colegio_id !== $matricula->colegio_id) {
            return false;
        }

        return $user->hasAnyRole(['administrador', 'caja'])
            || $matricula->estudiante?->user_id === $user->id
            || $user->hijos()->whereKey($matricula->estudiante_id)->exists()
            || ($user->hasRole('docente') && $matricula->seccion->asignaciones()
                ->where('docente_id', $user->id)->exists());
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrador', 'caja']);
    }

    public function update(User $user, Matricula $matricula): bool
    {
        return $this->create($user)
            && ($user->hasRole('superadmin') || $user->colegio_id === $matricula->colegio_id);
    }
}
