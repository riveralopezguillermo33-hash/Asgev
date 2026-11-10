<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgresoSesion extends Model
{
    use BelongsToColegio;

    protected $table = 'progreso_sesiones';

    protected $fillable = ['colegio_id', 'usuario_id', 'sesion_id', 'completada'];

    protected function casts(): array
    {
        return ['completada' => 'boolean'];
    }

    public function sesion(): BelongsTo
    {
        return $this->belongsTo(Sesion::class);
    }
}
