<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Competencia extends Model
{
    use BelongsToColegio;

    protected $table = 'competencias';

    protected $fillable = ['colegio_id', 'area_id', 'nombre', 'descripcion', 'icono', 'peso'];

    protected function casts(): array
    {
        return ['peso' => 'decimal:3'];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
