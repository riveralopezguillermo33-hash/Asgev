<?php

namespace App\Policies;

use App\Models\Nota;
use App\Models\User;

class NotaPolicy
{
    public function view(User $user, Nota $nota): bool
    {
        return $user->can('view', $nota->matricula->estudiante);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['docente', 'superadmin']);
    }

    public function update(User $user, Nota $nota): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if (! $user->hasRole('docente') || $user->colegio_id !== $nota->colegio_id) {
            return false;
        }

        return $nota->matricula->seccion->asignaciones()
            ->where('docente_id', $user->id)
            ->where('area_id', $nota->competencia->area_id)
            ->exists();
    }
}
