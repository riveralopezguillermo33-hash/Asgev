<?php

namespace App\Policies;

use App\Models\Estudiante;
use App\Models\User;

class EstudiantePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['familia', 'estudiante', 'docente', 'administrador', 'caja', 'superadmin']);
    }

    public function view(User $user, Estudiante $estudiante): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->colegio_id !== $estudiante->colegio_id) {
            return false;
        }

        return $estudiante->user_id === $user->id
            || $user->hijos()->whereKey($estudiante->id)->exists()
            || $user->hasAnyRole(['administrador', 'caja'])
            || ($user->hasRole('docente') && $estudiante->matriculas()
                ->whereHas('seccion.asignaciones', fn ($query) => $query->where('docente_id', $user->id))
                ->exists());
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrador', 'superadmin']);
    }

    public function update(User $user, Estudiante $estudiante): bool
    {
        return $user->hasAnyRole(['administrador', 'superadmin'])
            && ($user->hasRole('superadmin') || $user->colegio_id === $estudiante->colegio_id);
    }

    public function delete(User $user, Estudiante $estudiante): bool
    {
        return $this->update($user, $estudiante);
    }
}
