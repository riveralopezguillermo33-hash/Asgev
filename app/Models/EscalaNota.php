<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;

class EscalaNota extends Model
{
    use BelongsToColegio;

    protected $table = 'escala_notas';

    public $timestamps = false;

    protected $fillable = ['colegio_id', 'nota_desde', 'nota_hasta', 'etiqueta', 'color'];

    protected function casts(): array
    {
        return ['nota_desde' => 'decimal:2', 'nota_hasta' => 'decimal:2'];
    }
}
