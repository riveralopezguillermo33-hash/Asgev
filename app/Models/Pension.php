<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pension extends Model
{
    use BelongsToColegio;

    protected $table = 'pensiones';

    protected $fillable = ['colegio_id', 'matricula_id', 'mes', 'monto', 'vencimiento', 'estado'];

    protected function casts(): array
    {
        return ['mes' => 'date', 'monto' => 'decimal:2', 'vencimiento' => 'date'];
    }

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }
}
