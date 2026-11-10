<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nivel extends Model
{
    use BelongsToColegio;

    protected $table = 'niveles';

    protected $fillable = ['colegio_id', 'nombre'];

    public function grados(): HasMany
    {
        return $this->hasMany(Grado::class);
    }
}
