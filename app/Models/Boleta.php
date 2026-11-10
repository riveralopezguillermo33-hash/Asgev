<?php

namespace App\Models;

use App\Models\Concerns\BelongsToColegio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Boleta extends Model
{
    use BelongsToColegio;

    protected $table = 'boletas';

    protected $fillable = ['colegio_id', 'voucher_id', 'numero', 'pdf_path', 'emitida_en'];

    protected function casts(): array
    {
        return ['emitida_en' => 'datetime'];
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }
}
