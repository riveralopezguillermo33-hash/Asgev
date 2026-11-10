<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    use BelongsToColegio;

    protected $table = 'cursos';

    protected $fillable = ['colegio_id', 'titulo', 'descripcion', 'docente_id', 'duracion_semanas', 'nivel'];

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class)->orderBy('orden');
    }
}
