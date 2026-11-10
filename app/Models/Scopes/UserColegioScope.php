<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Request;

class UserColegioScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $request = Request::instance();

        if ($request->attributes->get('asgev_superadmin') === true) {
            return;
        }

        $colegioId = $request->attributes->get('asgev_colegio_id');
        if ($colegioId !== null) {
            $builder->where($model->qualifyColumn('colegio_id'), $colegioId);
        } elseif ($request->attributes->get('asgev_tenant_required') === true) {
            $builder->whereRaw('1 = 0');
        }
    }
}
