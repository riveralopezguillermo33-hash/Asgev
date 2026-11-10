<?php

namespace App\Policies;

use App\Models\Voucher;
use App\Models\User;

class VoucherPolicy
{
    public function view(User $user, Voucher $voucher): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->colegio_id === $voucher->colegio_id
            && ($user->hasAnyRole(['administrador', 'caja'])
                || $user->can('view', $voucher->pension));
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['familia', 'estudiante']);
    }

    public function review(User $user, Voucher $voucher): bool
    {
        return $user->hasAnyRole(['administrador', 'caja', 'superadmin'])
            && ($user->hasRole('superadmin') || $user->colegio_id === $voucher->colegio_id);
    }
}
