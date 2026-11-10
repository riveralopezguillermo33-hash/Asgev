<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Voucher extends Model
{
    use BelongsToColegio;

    protected $table = 'vouchers';

    protected $fillable = [
        'colegio_id', 'pension_id', 'imagen_path', 'nro_operacion', 'fecha_pago',
        'nombre', 'dni', 'concepto', 'monto', 'medio_pago', 'sucursal', 'estado',
        'revisado_por', 'motivo_rechazo',
    ];

    protected function casts(): array
    {
        return ['fecha_pago' => 'datetime', 'monto' => 'decimal:2'];
    }

    public function pension(): BelongsTo
    {
        return $this->belongsTo(Pension::class);
    }

    public function boleta(): HasOne
    {
        return $this->hasOne(Boleta::class);
    }
}
