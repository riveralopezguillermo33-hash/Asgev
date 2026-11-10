<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Request;

class Colegio extends Model
{
    protected $table = 'colegios';

    protected static function booted(): void
    {
        static::addGlobalScope('colegio_actual', function ($query): void {
            $request = Request::instance();

            if ($request->attributes->get('asgev_superadmin') === true) {
                return;
            }

            $colegioId = $request->attributes->get('asgev_colegio_id');
            if ($colegioId !== null) {
                $query->whereKey($colegioId);
            } elseif ($request->attributes->get('asgev_tenant_required') === true) {
                $query->whereRaw('1 = 0');
            }
        });
    }

    protected $fillable = [
        'nombre',
        'logo_path',
        'nota_minima_aprobatoria',
        'moneda',
        'periodo_tipo',
    ];

    protected function casts(): array
    {
        return ['nota_minima_aprobatoria' => 'decimal:2'];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function escalaNotas(): HasMany
    {
        return $this->hasMany(EscalaNota::class);
    }

    public function niveles(): HasMany
    {
        return $this->hasMany(Nivel::class);
    }

    public function aniosEscolares(): HasMany
    {
        return $this->hasMany(AnioEscolar::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }
}
