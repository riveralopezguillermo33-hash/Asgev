<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nota extends Model
{
    use BelongsToColegio;

    protected $table = 'notas';

    protected $fillable = ['colegio_id', 'matricula_id', 'competencia_id', 'periodo_id', 'nota', 'registrado_por'];

    protected function casts(): array
    {
        return ['nota' => 'decimal:2'];
    }

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    public function competencia(): BelongsTo
    {
        return $this->belongsTo(Competencia::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }

    public function historial(): HasMany
    {
        return $this->hasMany(NotaHistorial::class);
    }
}
