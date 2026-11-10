<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnioEscolar extends Model
{
    use BelongsToColegio;

    protected $table = 'anios_escolares';

    protected $fillable = ['colegio_id', 'nombre', 'fecha_inicio', 'fecha_fin', 'vigente'];

    protected function casts(): array
    {
        return ['fecha_inicio' => 'date', 'fecha_fin' => 'date', 'vigente' => 'boolean'];
    }

    public function periodos(): HasMany
    {
        return $this->hasMany(Periodo::class, 'anio_id');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'anio_id');
    }
}
