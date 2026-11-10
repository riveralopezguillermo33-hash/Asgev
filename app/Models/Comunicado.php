<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comunicado extends Model
{
    use BelongsToColegio;

    protected $table = 'comunicados';

    protected $fillable = ['colegio_id', 'nivel_id', 'grado_id', 'seccion_id', 'titulo', 'contenido', 'creado_por', 'publicado_en'];

    protected function casts(): array
    {
        return ['publicado_en' => 'datetime'];
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
