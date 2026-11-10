<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoCalendario extends Model
{
    use BelongsToColegio;

    protected $table = 'eventos_calendario';

    protected $fillable = [
        'colegio_id', 'nivel_id', 'grado_id', 'seccion_id',
        'titulo', 'descripcion', 'inicia_en', 'termina_en',
    ];

    protected function casts(): array
    {
        return ['inicia_en' => 'datetime', 'termina_en' => 'datetime'];
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class);
    }

    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class);
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class);
    }
}
