<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensaje extends Model
{
    use BelongsToColegio;

    protected $table = 'mensajes';

    protected $fillable = ['colegio_id', 'remitente_id', 'destinatario_id', 'contenido', 'leido_en'];

    protected function casts(): array
    {
        return ['leido_en' => 'datetime'];
    }

    public function remitente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    public function destinatario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }
}
