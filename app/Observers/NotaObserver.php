<?php

namespace App\Observers;

use App\Models\Nota;
use App\Models\NotaHistorial;
use Illuminate\Support\Facades\Auth;

class NotaObserver
{
    public function created(Nota $nota): void
    {
        $this->guardarHistorial($nota, null);
    }

    public function updated(Nota $nota): void
    {
        if ($nota->wasChanged('nota')) {
            $this->guardarHistorial($nota, (float) $nota->getOriginal('nota'));
        }
    }

    private function guardarHistorial(Nota $nota, ?float $anterior): void
    {
        NotaHistorial::create([
            'colegio_id' => $nota->colegio_id,
            'nota_id' => $nota->id,
            'nota_anterior' => $anterior,
            'nota_nueva' => $nota->nota,
            'cambiado_por' => Auth::id() ?? $nota->registrado_por,
            'fecha' => now(),
        ]);
    }
}
