<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AplicarColegio
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user, 401);

        $request->attributes->set('asgev_tenant_required', true);

        if ($user->hasRole('superadmin')) {
            $request->attributes->set('asgev_superadmin', true);
        } else {
            abort_unless($user->colegio_id, 403, 'La cuenta no tiene un colegio asignado.');
            $request->attributes->set('asgev_colegio_id', $user->colegio_id);
        }

        return $next($request);
    }
}
