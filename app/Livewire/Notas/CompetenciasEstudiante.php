<?php

namespace App\Livewire\Notas;

use App\Models\AnioEscolar;
use App\Models\Area;
use App\Models\EscalaNota;
use App\Models\Estudiante;
use App\Models\Nota;
use App\Models\Periodo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal')]
class CompetenciasEstudiante extends Component
{
    public ?int $estudianteId = null;

    public ?int $periodoId = null;

    public ?int $areaId = null;

    public bool $mostrarEscala = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->estudianteId = $this->resolverEstudianteId($user);

        if (! $this->estudianteId) {
            abort(403, 'No tienes estudiantes vinculados para consultar notas.');
        }

        $estudiante = Estudiante::query()->findOrFail($this->estudianteId);
        Gate::authorize('view', $estudiante);

        $periodos = $this->periodos();
        if ($periodos->isNotEmpty() && ! $this->periodoId) {
            $this->periodoId = $periodos->first()->id;
        }

        $areas = $this->areas();
        if ($areas->isNotEmpty() && ! $this->areaId) {
            $this->areaId = $areas->first()->id;
        }
    }

    public function updatedEstudianteId(): void
    {
        $this->periodoId = null;
        $this->areaId = null;

        $periodos = $this->periodos();
        if ($periodos->isNotEmpty()) {
            $this->periodoId = $periodos->first()->id;
        }

        $areas = $this->areas();
        if ($areas->isNotEmpty()) {
            $this->areaId = $areas->first()->id;
        }
    }

    public function abrirEscala(): void
    {
        $this->mostrarEscala = true;
    }

    public function cerrarEscala(): void
    {
        $this->mostrarEscala = false;
    }

    public function render()
    {
        $estudiantesDisponibles = $this->estudiantesDisponibles();
        $periodos = $this->periodos();
        $areas = $this->areas();
        $competencias = $this->competencias();
        $promedioArea = $this->promedioArea();
        $promedioGeneral = $this->promedioGeneral();
        $escalas = $this->escalas();

        return view('livewire.notas.competencias-estudiante', [
            'estudiantesDisponibles' => $estudiantesDisponibles,
            'estudiante' => $this->estudiante(),
            'periodos' => $periodos,
            'areas' => $areas,
            'competencias' => $competencias,
            'promedioArea' => $promedioArea,
            'promedioGeneral' => $promedioGeneral,
            'escalas' => $escalas,
        ]);
    }

    public function estudiante(): ?Estudiante
    {
        if (! $this->estudianteId) {
            return null;
        }

        return Estudiante::query()->with('user')->find($this->estudianteId);
    }

    public function estudiantesDisponibles(): Collection
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->hasRole('familia')) {
            return $user->hijos()->with('user')->orderBy('codigo')->get();
        }

        if ($user->hasRole('estudiante')) {
            $student = $user->estudiante()->with('user')->first();

            return $student ? collect([$student]) : collect();
        }

        if ($user->hasRole('docente')) {
            return Estudiante::query()
                ->whereHas('matriculas.seccion.asignaciones', fn ($query) => $query->where('docente_id', $user->id))
                ->with('user')
                ->orderBy('codigo')
                ->get();
        }

        return Estudiante::query()
            ->where('colegio_id', $user->colegio_id)
            ->with('user')
            ->orderBy('codigo')
            ->get();
    }

    public function periodos(): Collection
    {
        $estudiante = $this->estudiante();
        if (! $estudiante) {
            return collect();
        }

        $anioId = $estudiante->matriculas()
            ->where('estado', 'activa')
            ->value('anio_id')
            ?? AnioEscolar::query()
                ->where('colegio_id', auth()->user()->colegio_id)
                ->where('vigente', true)
                ->value('id');

        if (! $anioId) {
            return collect();
        }

        return Periodo::query()
            ->where('colegio_id', auth()->user()->colegio_id)
            ->where('anio_id', $anioId)
            ->orderBy('fecha_inicio')
            ->get();
    }

    public function areas(): Collection
    {
        return Area::query()
            ->where('colegio_id', auth()->user()->colegio_id)
            ->orderBy('nombre')
            ->get();
    }

    public function competencias(): Collection
    {
        if (! $this->estudianteId || ! $this->periodoId) {
            return collect();
        }

        $query = Nota::query()
            ->with(['competencia.area'])
            ->where('colegio_id', auth()->user()->colegio_id)
            ->where('periodo_id', $this->periodoId)
            ->whereHas('matricula', function ($query) {
                $query->where('estudiante_id', $this->estudianteId)
                    ->where('estado', 'activa');
            });

        if ($this->areaId) {
            $query->whereHas('competencia', fn ($subQuery) => $subQuery->where('area_id', $this->areaId));
        }

        return $query
            ->get()
            ->sortBy(fn (Nota $nota) => [$nota->competencia->area->nombre, $nota->competencia->nombre]);
    }

    public function promedioArea(): ?float
    {
        $notas = $this->competencias();

        if ($notas->isEmpty()) {
            return null;
        }

        return round((float) $notas->avg('nota'), 2);
    }

    public function promedioGeneral(): ?float
    {
        $notas = $this->competencias();

        if ($notas->isEmpty()) {
            return null;
        }

        return round((float) $notas->avg('nota'), 2);
    }

    public function escalas(): Collection
    {
        return EscalaNota::query()
            ->where('colegio_id', auth()->user()->colegio_id)
            ->orderBy('nota_desde')
            ->get();
    }

    public function rangoNota(float $nota): array
    {
        $escala = EscalaNota::query()
            ->where('colegio_id', auth()->user()->colegio_id)
            ->where('nota_desde', '<=', $nota)
            ->where('nota_hasta', '>=', $nota)
            ->first();

        if (! $escala) {
            return ['etiqueta' => 'Sin rango', 'color' => '#94A3B8', 'fondo' => '#F1F5F9'];
        }

        return [
            'etiqueta' => $escala->etiqueta,
            'color' => $escala->color,
            'fondo' => $this->fondoPorEtiqueta($escala->etiqueta),
        ];
    }

    private function fondoPorEtiqueta(string $etiqueta): string
    {
        return match ($etiqueta) {
            'Excelente' => '#E7F9EE',
            'Muy bueno' => '#E8F1FF',
            'Regular' => '#FFF4D6',
            default => '#FDE8E8',
        };
    }

    private function resolverEstudianteId(User $user): ?int
    {
        if ($user->hasRole('familia')) {
            return $user->hijos()->orderBy('codigo')->first()?->id;
        }

        if ($user->hasRole('estudiante')) {
            return $user->estudiante()->value('id');
        }

        if ($user->hasRole('docente')) {
            return Estudiante::query()
                ->whereHas('matriculas.seccion.asignaciones', fn ($query) => $query->where('docente_id', $user->id))
                ->orderBy('codigo')
                ->value('id');
        }

        return Estudiante::query()
            ->where('colegio_id', $user->colegio_id)
            ->orderBy('codigo')
            ->value('id');
    }
}
