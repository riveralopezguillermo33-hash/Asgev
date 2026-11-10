<?php

namespace App\Services;

use App\Models\AnioEscolar;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Pension;
use App\Models\Seccion;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class MatriculaService
{
    public function crear(
        User $actor,
        int $estudianteId,
        int $nivelId,
        int $gradoId,
        int $seccionId,
        int $anioId,
        string $montoMensual,
        int $diaVencimiento,
    ): Matricula {
        Gate::forUser($actor)->authorize('create', Matricula::class);

        try {
            return DB::transaction(function () use (
                $actor,
                $estudianteId,
                $nivelId,
                $gradoId,
                $seccionId,
                $anioId,
                $montoMensual,
                $diaVencimiento,
            ): Matricula {
                $seccion = Seccion::query()->lockForUpdate()->findOrFail($seccionId);
                $estudiante = Estudiante::query()->findOrFail($estudianteId);
                $grado = Grado::query()->findOrFail($gradoId);
                $anio = AnioEscolar::query()->findOrFail($anioId);

                if (
                    $seccion->colegio_id !== $actor->colegio_id
                    || $estudiante->colegio_id !== $actor->colegio_id
                    || $grado->colegio_id !== $actor->colegio_id
                    || $anio->colegio_id !== $actor->colegio_id
                ) {
                    throw ValidationException::withMessages([
                        'estudianteId' => 'Los datos seleccionados no pertenecen a tu colegio.',
                    ]);
                }

                if ($seccion->grado_id !== $grado->id || $grado->nivel_id !== $nivelId) {
                    throw ValidationException::withMessages([
                        'seccionId' => 'La sección no corresponde al nivel y grado seleccionados.',
                    ]);
                }

                if ($anio->fecha_inicio->gt($anio->fecha_fin)) {
                    throw ValidationException::withMessages([
                        'anioId' => 'El año escolar tiene un rango de fechas inválido.',
                    ]);
                }

                if (Matricula::query()
                    ->where('estudiante_id', $estudiante->id)
                    ->where('anio_id', $anio->id)
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'estudianteId' => 'El estudiante ya tiene una matrícula para este año escolar.',
                    ]);
                }

                $matriculasActivas = Matricula::query()
                    ->where('seccion_id', $seccion->id)
                    ->where('anio_id', $anio->id)
                    ->whereIn('estado', ['pendiente', 'activa'])
                    ->count();

                if ($matriculasActivas >= $seccion->vacantes) {
                    throw ValidationException::withMessages([
                        'seccionId' => 'La sección ya no tiene vacantes disponibles.',
                    ]);
                }

                $matricula = Matricula::create([
                    'colegio_id' => $actor->colegio_id,
                    'estudiante_id' => $estudiante->id,
                    'seccion_id' => $seccion->id,
                    'anio_id' => $anio->id,
                    'estado' => 'activa',
                ]);

                $mes = CarbonImmutable::parse($anio->fecha_inicio)->startOfMonth();
                $ultimoMes = CarbonImmutable::parse($anio->fecha_fin)->startOfMonth();

                while ($mes->lessThanOrEqualTo($ultimoMes)) {
                    Pension::create([
                        'colegio_id' => $actor->colegio_id,
                        'matricula_id' => $matricula->id,
                        'mes' => $mes->toDateString(),
                        'monto' => $montoMensual,
                        'vencimiento' => $mes->setDay($diaVencimiento)->toDateString(),
                        'estado' => 'pendiente',
                    ]);

                    $mes = $mes->addMonth();
                }

                return $matricula->load('pensiones');
            });
        } catch (UniqueConstraintViolationException $exception) {
            if (Matricula::query()
                ->where('estudiante_id', $estudianteId)
                ->where('anio_id', $anioId)
                ->exists()) {
                throw ValidationException::withMessages([
                    'estudianteId' => 'El estudiante ya tiene una matrícula para este año escolar.',
                ]);
            }

            throw $exception;
        }
    }
}
