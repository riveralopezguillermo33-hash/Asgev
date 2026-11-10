<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaHistorial extends Model
{
    use BelongsToColegio;

    public $timestamps = false;

    protected $table = 'notas_historial';

    protected $fillable = ['colegio_id', 'nota_id', 'nota_anterior', 'nota_nueva', 'cambiado_por', 'fecha'];

    protected function casts(): array
    {
        return [
            'nota_anterior' => 'decimal:2',
            'nota_nueva' => 'decimal:2',
            'fecha' => 'datetime',
        ];
    }

    public function nota(): BelongsTo
    {
        return $this->belongsTo(Nota::class);
    }
}
