<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    use BelongsToColegio;

    protected $table = 'estudiantes';

    protected $fillable = ['colegio_id', 'user_id', 'codigo'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apoderados(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'apoderado_estudiante', 'estudiante_id', 'apoderado_id')
            ->withPivot('parentesco', 'colegio_id')
            ->withTimestamps();
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }
}
