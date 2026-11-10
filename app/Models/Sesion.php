<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sesion extends Model
{
    use BelongsToColegio;

    protected $table = 'sesiones';

    protected $fillable = ['colegio_id', 'curso_id', 'orden', 'titulo', 'descripcion', 'duracion_min'];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function recursos(): HasMany
    {
        return $this->hasMany(Recurso::class);
    }
}
