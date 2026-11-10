<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recurso extends Model
{
    use BelongsToColegio;

    protected $table = 'recursos';

    protected $fillable = ['colegio_id', 'sesion_id', 'tipo', 'path'];

    public function sesion(): BelongsTo
    {
        return $this->belongsTo(Sesion::class);
    }
}
