<main class="min-h-screen bg-[#F2F9FB] pb-24 text-[#143747]">
    <header class="relative overflow-hidden rounded-b-[34px] bg-[#0E8A9A] px-5 pb-8 pt-5 text-white shadow-[0_10px_30px_rgba(11,79,108,.12)] sm:px-8 sm:pb-10">
        <div class="pointer-events-none absolute -right-14 -top-32 size-72 rounded-full border border-white/10"></div>
        <div class="relative mx-auto max-w-4xl">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('dashboard') }}" class="grid size-11 place-items-center rounded-2xl bg-white/10 text-white hover:bg-white/20" aria-label="Volver al portal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5" aria-hidden="true">
                        <path d="m15 18-6-6 6-6M9 12h12"/>
                    </svg>
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-white no-underline" aria-label="ASGEV">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-6" aria-hidden="true">
                        <path d="M3 5.5c3.5-.8 6.5 0 9 2.2 2.5-2.2 5.5-3 9-2.2v13c-3.5-.8-6.5 0-9 2.2-2.5-2.2-5.5-3-9-2.2v-13Z"/><path d="M12 7.7v13"/>
                    </svg>
                    <span class="text-xl font-extrabold tracking-[-1px]">ASGEV</span>
                </a>
                <div class="size-11"></div>
            </div>

            <div class="mt-8">
                <p class="text-sm font-semibold text-white/75">Aprende hoy, lidera mañana</p>
                <h1 class="mt-1 text-[28px] font-extrabold tracking-[-.7px] sm:text-[32px]">Notas por competencias</h1>
                <p class="mt-2 text-sm text-white/85">Conoce tu progreso por cada competencia</p>
            </div>
        </div>
    </header>

    <section class="mx-auto max-w-4xl px-4 pb-8 pt-6 sm:px-8 sm:pt-8">
        <div class="rounded-[22px] border border-[#E5EFF1] bg-white p-4 shadow-[0_6px_24px_rgba(20,55,71,.035)] sm:p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
                @if ($estudiantesDisponibles->count() > 1)
                    <label class="flex-1">
                        <span class="mb-2 block text-sm font-bold text-[#264858]">Estudiante</span>
                        <select wire:model.live="estudianteId" class="min-h-12 w-full rounded-xl border-[#DDE8EB] bg-white text-sm text-[#264858] focus:border-[#0E8A9A] focus:ring-[#0E8A9A]">
                            @foreach ($estudiantesDisponibles as $estudianteDisponible)
                                <option value="{{ $estudianteDisponible->id }}">{{ $estudianteDisponible->user->nombres }} {{ $estudianteDisponible->user->apellidos }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                <label class="flex-1">
                    <span class="mb-2 block text-sm font-bold text-[#264858]">Periodo</span>
                    <select wire:model.live="periodoId" class="min-h-12 w-full rounded-xl border-[#DDE8EB] bg-white text-sm text-[#264858] focus:border-[#0E8A9A] focus:ring-[#0E8A9A]">
                        @foreach ($periodos as $periodo)
                            <option value="{{ $periodo->id }}">{{ $periodo->nombre }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="flex-1">
                    <span class="mb-2 block text-sm font-bold text-[#264858]">Área</span>
                    <select wire:model.live="areaId" class="min-h-12 w-full rounded-xl border-[#DDE8EB] bg-white text-sm text-[#264858] focus:border-[#0E8A9A] focus:ring-[#0E8A9A]">
                        <option value="">Todas</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>

        @if ($estudiante)
            <div class="mt-5 flex items-center justify-between rounded-[20px] border border-[#E5EFF1] bg-white p-4 shadow-[0_6px_24px_rgba(20,55,71,.035)]">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.12em] text-[#7E969F]">Estudiante</p>
                    <h2 class="mt-1 text-lg font-extrabold text-[#0B4F6C]">{{ $estudiante->user->nombres }} {{ $estudiante->user->apellidos }}</h2>
                </div>
                @if ($promedioGeneral !== null)
                    @php($general = $this->rangoNota((float) $promedioGeneral))
                    <div class="rounded-full px-3 py-2 text-right" style="background-color: {{ $general['fondo'] }}; color: {{ $general['color'] }};">
                        <div class="text-[10px] font-bold uppercase tracking-[.1em]">Promedio</div>
                        <div class="text-base font-extrabold">{{ number_format($promedioGeneral, 1) }}</div>
                    </div>
                @endif
            </div>
        @endif

        @if ($competencias->isEmpty())
            <div class="mt-5 rounded-[20px] border border-dashed border-[#BFD5DB] bg-white px-5 py-9 text-center">
                <p class="text-sm font-bold text-[#264858]">No hay notas registradas para esta selección.</p>
                <p class="mt-2 text-sm text-[#68808B]">Cambia el periodo o el área para revisar otro conjunto de competencias.</p>
            </div>
        @else
            <div class="mt-5 space-y-4">
                @foreach ($competencias as $nota)
                    @php($rango = $this->rangoNota((float) $nota->nota))
                    <article class="rounded-[22px] border border-[#E5EFF1] bg-white p-4 shadow-[0_6px_24px_rgba(20,55,71,.035)] sm:p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <span class="grid size-12 place-items-center rounded-2xl bg-[#E8F6F7] text-[#0E8A9A]">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="size-5" aria-hidden="true">
                                        <path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5v-9Z"/>
                                        <path d="M8 9h8M8 13h8M8 17h5"/>
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-base font-extrabold text-[#0B4F6C]">{{ $nota->competencia->nombre }}</h3>
                                    <p class="mt-1 text-sm text-[#68808B]">{{ $nota->competencia->descripcion ?: 'Competencia curricular del colegio.' }}</p>
                                </div>
                            </div>
                            <span class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-[.08em]" style="background-color: {{ $rango['fondo'] }}; color: {{ $rango['color'] }};">{{ $rango['etiqueta'] }}</span>
                        </div>

                        <div class="mt-5 flex items-end justify-between gap-3">
                            <div class="flex items-end gap-2">
                                <span class="text-4xl font-extrabold tracking-[-.8px] text-[#0B4F6C]">{{ number_format((float) $nota->nota, 1) }}</span>
                                <span class="pb-1 text-[11px] font-bold uppercase tracking-[.12em] text-[#7E969F]">/20</span>
                            </div>
                            <span class="text-xs font-bold text-[#68808B]">{{ $nota->competencia->area->nombre }}</span>
                        </div>

                        <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-[#EAF1F4]">
                            <span class="block h-full rounded-full" style="width: {{ min(max(((float) $nota->nota / 20) * 100, 0), 100) }}%; background-color: {{ $rango['color'] }};"></span>
                        </div>
                    </article>
                @endforeach

                @if ($promedioArea !== null)
                    @php($promedio = $this->rangoNota((float) $promedioArea))
                    <div class="rounded-[20px] border border-[#DDEEF0] bg-[#F8FCFD] p-4">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-bold text-[#264858]">Promedio del área</span>
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-extrabold text-[#0B4F6C]">{{ number_format($promedioArea, 1) }}</span>
                                <span class="rounded-full px-2 py-1 text-[10px] font-bold uppercase tracking-[.08em]" style="background-color: {{ $promedio['fondo'] }}; color: {{ $promedio['color'] }};">{{ $promedio['etiqueta'] }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="mt-6 flex justify-center">
            <button type="button" wire:click="abrirEscala" class="text-sm font-bold text-[#0E8A9A] hover:text-[#0B4F6C]">¿Qué significan las notas?</button>
        </div>
    </section>

    @if ($mostrarEscala)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-[#143747]/45 p-4" wire:click.self="cerrarEscala">
            <div class="w-full max-w-lg rounded-[26px] border border-[#E5EFF1] bg-white p-5 shadow-[0_20px_60px_rgba(11,79,108,.18)]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.12em] text-[#0E8A9A]">Escala de notas</p>
                        <h2 class="mt-1 text-xl font-extrabold text-[#0B4F6C]">Rangos de evaluación</h2>
                    </div>
                    <button type="button" wire:click="cerrarEscala" class="grid size-10 place-items-center rounded-full bg-[#EEF4F6] text-[#264858]" aria-label="Cerrar diálogo">×</button>
                </div>

                <div class="mt-5 overflow-hidden rounded-2xl border border-[#E5EFF1]">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#F5FAFB] text-[#264858]">
                            <tr>
                                <th class="px-4 py-3 font-bold">Rango</th>
                                <th class="px-4 py-3 font-bold">Etiqueta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($escalas as $escala)
                                <tr class="border-t border-[#E5EFF1]">
                                    <td class="px-4 py-3 font-medium text-[#143747]">{{ number_format((float) $escala->nota_desde, 1) }}–{{ number_format((float) $escala->nota_hasta, 1) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-[.08em]" style="background-color: {{ $this->fondoPorEtiqueta($escala->etiqueta) }}; color: {{ $escala->color }};">{{ $escala->etiqueta }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <nav class="fixed inset-x-0 bottom-0 z-20 border-t border-[#E8EFF1] bg-white/95 px-2 pb-[max(env(safe-area-inset-bottom),8px)] pt-2 backdrop-blur md:hidden" aria-label="Navegación principal">
        <ul class="mx-auto flex max-w-lg items-center justify-around">
            <li class="flex-1">
                <a href="{{ route('dashboard') }}" class="mx-auto flex min-h-[54px] w-full flex-col items-center justify-center gap-1 rounded-xl text-[10px] font-bold text-[#8399A2]">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-8H9v8H4a1 1 0 0 1-1-1v-9.5Z"/></svg>
                    Inicio
                </a>
            </li>
            <li class="flex-1">
                <button type="button" class="mx-auto flex min-h-[54px] w-full flex-col items-center justify-center gap-1 rounded-xl text-[10px] font-bold text-[#8399A2]" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true"><path d="M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1"/><path d="M9.5 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M22 19v-1a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Cursos
                </button>
            </li>
            <li class="flex-1">
                <button type="button" class="mx-auto flex min-h-[54px] w-full flex-col items-center justify-center gap-1 rounded-xl text-[10px] font-bold text-[#0E8A9A]" aria-current="page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v15H6.5A2.5 2.5 0 0 1 4 14.5v-10A2.5 2.5 0 0 1 6.5 2Z"/><path d="M8.5 7.5h8M8.5 11h8"/></svg>
                    Notas
                </button>
            </li>
            <li class="flex-1">
                <button type="button" class="mx-auto flex min-h-[54px] w-full flex-col items-center justify-center gap-1 rounded-xl text-[10px] font-bold text-[#8399A2]" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true"><path d="M3 12h18"/><path d="M3 6h18"/><path d="M3 18h12"/></svg>
                    Horario
                </button>
            </li>
            <li class="flex-1">
                <button type="button" class="mx-auto flex min-h-[54px] w-full flex-col items-center justify-center gap-1 rounded-xl text-[10px] font-bold text-[#8399A2]" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10Z"/></svg>
                    Mensajes
                </button>
            </li>
        </ul>
    </nav>
</main>
