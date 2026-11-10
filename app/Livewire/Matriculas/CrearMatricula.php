<?php

namespace App\Livewire\Matriculas;

use App\Models\AnioEscolar;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Nivel;
use App\Models\Seccion;
use App\Services\MatriculaService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal')]
class CrearMatricula extends Component
{
    public ?int $estudianteId = null;

    public ?int $nivelId = null;

    public ?int $gradoId = null;

    public ?int $seccionId = null;

    public ?int $anioId = null;

    public string $montoMensual = '';

    public int $diaVencimiento = 10;

    public function mount(): void
    {
        Gate::authorize('create', Matricula::class);
    }

    public function updatedNivelId(): void
    {
        $this->gradoId = null;
        $this->seccionId = null;
    }

    public function updatedGradoId(): void
    {
        $this->seccionId = null;
    }

    #[Computed]
    public function estudiantes(): Collection
    {
        return Estudiante::query()
            ->with('user')
            ->orderBy('codigo')
            ->get();
    }

    #[Computed]
    public function niveles(): Collection
    {
        return Nivel::query()->orderBy('nombre')->get();
    }

    #[Computed]
    public function grados(): Collection
    {
        if (! $this->nivelId) {
            return new Collection;
        }

        return Grado::query()
            ->where('nivel_id', $this->nivelId)
            ->orderBy('nombre')
            ->get();
    }

    #[Computed]
    public function secciones(): Collection
    {
        if (! $this->gradoId) {
            return new Collection;
        }

        return Seccion::query()
            ->where('grado_id', $this->gradoId)
            ->orderBy('nombre')
            ->get()
            ->map(function (Seccion $seccion): Seccion {
                $ocupadas = Matricula::query()
                    ->where('seccion_id', $seccion->id)
                    ->where('anio_id', $this->anioId)
                    ->whereIn('estado', ['pendiente', 'activa'])
                    ->count();

                $seccion->setAttribute('vacantes_disponibles', max(0, $seccion->vacantes - $ocupadas));

                return $seccion;
            });
    }

    #[Computed]
    public function aniosEscolares(): Collection
    {
        return AnioEscolar::query()
            ->orderByDesc('fecha_inicio')
            ->get();
    }

    public function confirmar(MatriculaService $matriculaService): void
    {
        $colegioId = auth()->user()->colegio_id;
        $idsDelColegio = fn (string $tabla) => Rule::exists($tabla, 'id')
            ->where('colegio_id', $colegioId);

        $validated = $this->validate([
            'estudianteId' => ['required', 'integer', $idsDelColegio('estudiantes')],
            'nivelId' => ['required', 'integer', $idsDelColegio('niveles')],
            'gradoId' => ['required', 'integer', $idsDelColegio('grados')],
            'seccionId' => ['required', 'integer', $idsDelColegio('secciones')],
            'anioId' => ['required', 'integer', $idsDelColegio('anios_escolares')],
            'montoMensual' => ['required', 'numeric', 'min:0.01', 'max:99999999.99', 'regex:/^\\d{1,8}(\\.\\d{1,2})?$/'],
            'diaVencimiento' => ['required', 'integer', 'between:1,28'],
        ], [
            'estudianteId.required' => 'Selecciona un estudiante.',
            'nivelId.required' => 'Selecciona un nivel.',
            'gradoId.required' => 'Selecciona un grado.',
            'seccionId.required' => 'Selecciona una sección.',
            'anioId.required' => 'Selecciona un año escolar.',
            'montoMensual.required' => 'Indica el monto mensual de la pensión.',
            'montoMensual.regex' => 'Ingresa un monto válido con hasta dos decimales.',
            'diaVencimiento.between' => 'El día de vencimiento debe estar entre 1 y 28.',
        ]);

        $matricula = $matriculaService->crear(
            auth()->user(),
            (int) $validated['estudianteId'],
            (int) $validated['nivelId'],
            (int) $validated['gradoId'],
            (int) $validated['seccionId'],
            (int) $validated['anioId'],
            $validated['montoMensual'],
            (int) $validated['diaVencimiento'],
        );

        session()->flash('matricula-creada', true);
        session()->flash('pensiones-generadas', $matricula->pensiones->count());

        $this->redirectRoute('matriculas.create', navigate: true);
    }

    public function render()
    {
        return view('livewire.matriculas.crear-matricula');
    }
}
