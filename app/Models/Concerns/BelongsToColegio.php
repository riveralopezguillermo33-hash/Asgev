<?php

namespace App\Models\Concerns;

use App\Models\Scopes\ColegioScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToColegio
{
    protected static function bootBelongsToColegio(): void
    {
        static::addGlobalScope(new ColegioScope);

        static::creating(function ($model): void {
            $request = request();
            $colegioId = $request->attributes->get('asgev_colegio_id');

            if ($colegioId !== null) {
                $model->setAttribute('colegio_id', $colegioId);
            } elseif ($request->attributes->get('asgev_tenant_required') === true
                && $request->attributes->get('asgev_superadmin') !== true) {
                throw new \Illuminate\Auth\Access\AuthorizationException('No se pudo determinar el colegio de la cuenta.');
            }
        });
    }

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function scopeEnColegio(Builder $query, int $colegioId): Builder
    {
        return $query->where($query->getModel()->qualifyColumn('colegio_id'), $colegioId);
    }
}
