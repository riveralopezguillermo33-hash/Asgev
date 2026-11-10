<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use BelongsToColegio;

    protected $table = 'areas';

    protected $fillable = ['colegio_id', 'nombre', 'icono', 'peso'];

    protected function casts(): array
    {
        return ['peso' => 'decimal:3'];
    }

    public function competencias(): HasMany
    {
        return $this->hasMany(Competencia::class);
    }
}
