<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    use BelongsToColegio;

    protected $table = 'horarios';

    protected $fillable = [
        'colegio_id', 'seccion_id', 'area_id', 'docente_id',
        'dia_semana', 'hora_inicio', 'hora_fin', 'aula',
    ];

    protected function casts(): array
    {
        return ['dia_semana' => 'integer'];
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'docente_id');
    }
}
