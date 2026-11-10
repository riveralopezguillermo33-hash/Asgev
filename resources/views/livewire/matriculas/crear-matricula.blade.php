<main class="min-h-screen bg-[#F2F9FB] pb-10 text-[#143747]">
    <header class="relative overflow-hidden rounded-b-[34px] bg-[#0E8A9A] px-5 pb-8 pt-5 text-white shadow-[0_10px_30px_rgba(11,79,108,.12)] sm:px-8 sm:pb-10">
        <div class="pointer-events-none absolute -right-14 -top-32 size-72 rounded-full border border-white/10"></div>
        <div class="relative mx-auto max-w-3xl">
            <div class="flex items-center justify-between">
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
                <span class="size-11"></span>
            </div>
            <div class="mt-8">
                <p class="text-sm font-semibold text-white/75">Aprende hoy, lidera mañana</p>
                <h1 class="mt-1 text-[28px] font-extrabold tracking-[-.7px] sm:text-[32px]">Matrícula</h1>
                <p class="mt-2 text-sm text-white/85">Completa la información para continuar</p>
            </div>
        </div>
    </header>

    <section class="mx-auto max-w-3xl px-4 pb-8 pt-6 sm:px-8 sm:pt-8">
        @if (session()->has('matricula-creada'))
            <div class="mb-5 rounded-2xl border border-[#BDE5D2] bg-[#ECF8F1] p-4 text-sm text-[#17694F]" role="status">
                <p class="font-bold">Matrícula confirmada correctamente.</p>
                <p class="mt-1">Se generaron {{ session('pensiones-generadas') }} pensiones según el calendario escolar.</p>
            </div>
        @endif

        <form wire:submit="confirmar" class="space-y-5">
            <section class="rounded-[22px] border border-[#E5EFF1] bg-white p-5 shadow-[0_6px_24px_rgba(20,55,71,.035)] sm:p-7">
                <div class="mb-6">
                    <p class="text-xs font-bold uppercase tracking-[.12em] text-[#0E8A9A]">Datos del estudiante</p>
                    <h2 class="mt-1 text-lg font-extrabold text-[#0B4F6C]">Información académica</h2>
                </div>

                <div class="space-y-4">
                    @if ($this->estudiantes->isEmpty())
                        <p class="rounded-xl bg-[#FFF6E5] px-4 py-3 text-sm text-[#805B18]" role="status">No hay estudiantes registrados para este colegio.</p>
                    @endif
                    @if ($this->niveles->isEmpty())
                        <p class="rounded-xl bg-[#FFF6E5] px-4 py-3 text-sm text-[#805B18]" role="status">No hay niveles académicos configurados todavía.</p>
                    @endif
                    @if ($this->aniosEscolares->isEmpty())
                        <p class="rounded-xl bg-[#FFF6E5] px-4 py-3 text-sm text-[#805B18]" role="status">No hay años escolares configurados todavía.</p>
                    @endif

                    <label class="block">
                        <span class="mb-2 block text-sm font-bold text-[#264858]">Estudiante <span class="text-[#C84036]">*</span></span>
                        <select wire:model="estudianteId" class="min-h-12 w-full rounded-xl border-[#DDE8EB] bg-white text-sm text-[#264858] focus:border-[#0E8A9A] focus:ring-[#0E8A9A]">
                            <option value="">Selecciona un estudiante</option>
                            @foreach ($this->estudiantes as $estudiante)
                                <option value="{{ $estudiante->id }}">{{ $estudiante->user->nombres }} {{ $estudiante->user->apellidos }} · {{ $estudiante->codigo }}</option>
                            @endforeach
                        </select>
                        @error('estudianteId') <span class="mt-1 block text-xs font-medium text-[#B42318]">{{ $message }}</span> @enderror
                    </label>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-sm font-bold text-[#264858]">Nivel <span class="text-[#C84036]">*</span></span>
                            <select wire:model.live="nivelId" class="min-h-12 w-full rounded-xl border-[#DDE8EB] bg-white text-sm text-[#264858] focus:border-[#0E8A9A] focus:ring-[#0E8A9A]">
                                <option value="">Selecciona un nivel</option>
                                @foreach ($this->niveles as $nivel)
                                    <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                                @endforeach
                            </select>
                            @error('nivelId') <span class="mt-1 block text-xs font-medium text-[#B42318]">{{ $message }}</span> @enderror
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-bold text-[#264858]">Grado <span class="text-[#C84036]">*</span></span>
                            <select wire:model.live="gradoId" @disabled(! $nivelId) class="min-h-12 w-full rounded-xl border-[#DDE8EB] bg-white text-sm text-[#264858] focus:border-[#0E8A9A] focus:ring-[#0E8A9A] disabled:cursor-not-allowed disabled:bg-[#F4F7F8] disabled:text-[#9AAAB0]">
                                <option value="">Selecciona un grado</option>
                                @foreach ($this->grados as $grado)
                                    <option value="{{ $grado->id }}">{{ $grado->nombre }}</option>
                                @endforeach
                            </select>
                            @error('gradoId') <span class="mt-1 block text-xs font-medium text-[#B42318]">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-sm font-bold text-[#264858]">Sección <span class="text-[#C84036]">*</span></span>
                            <select wire:model="seccionId" @disabled(! $gradoId) class="min-h-12 w-full rounded-xl border-[#DDE8EB] bg-white text-sm text-[#264858] focus:border-[#0E8A9A] focus:ring-[#0E8A9A] disabled:cursor-not-allowed disabled:bg-[#F4F7F8] disabled:text-[#9AAAB0]">
                                <option value="">Selecciona una sección</option>
                                @foreach ($this->secciones as $seccion)
                                    <option value="{{ $seccion->id }}">{{ $seccion->nombre }} · {{ $seccion->vacantes_disponibles }} vacantes</option>
                                @endforeach
                            </select>
                            @if ($gradoId && $this->secciones->isEmpty())
                                <span class="mt-1 block text-xs text-[#68808B]">No hay secciones disponibles para este grado.</span>
                            @endif
                            @error('seccionId') <span class="mt-1 block text-xs font-medium text-[#B42318]">{{ $message }}</span> @enderror
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-bold text-[#264858]">Año escolar <span class="text-[#C84036]">*</span></span>
                            <select wire:model.live="anioId" class="min-h-12 w-full rounded-xl border-[#DDE8EB] bg-white text-sm text-[#264858] focus:border-[#0E8A9A] focus:ring-[#0E8A9A]">
                                <option value="">Selecciona un año</option>
                                @foreach ($this->aniosEscolares as $anio)
                                    <option value="{{ $anio->id }}">{{ $anio->nombre }}{{ $anio->vigente ? ' · Vigente' : '' }}</option>
                                @endforeach
                            </select>
                            @error('anioId') <span class="mt-1 block text-xs font-medium text-[#B42318]">{{ $message }}</span> @enderror
                        </label>
                    </div>
                </div>
            </section>

            <section class="rounded-[22px] border border-[#E5EFF1] bg-white p-5 shadow-[0_6px_24px_rgba(20,55,71,.035)] sm:p-7">
                <div class="mb-5">
                    <p class="text-xs font-bold uppercase tracking-[.12em] text-[#0E8A9A]">Pensiones del año</p>
                    <h2 class="mt-1 text-lg font-extrabold text-[#0B4F6C]">Define el pago mensual</h2>
                    <p class="mt-2 text-sm leading-6 text-[#68808B]">El monto y día de vencimiento se aplicarán a cada mes comprendido en las fechas del año escolar.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="mb-2 block text-sm font-bold text-[#264858]">Monto mensual (S/) <span class="text-[#C84036]">*</span></span>
                        <span class="flex min-h-12 items-center rounded-xl border border-[#DDE8EB] px-3 focus-within:border-[#0E8A9A] focus-within:ring-2 focus-within:ring-[#0E8A9A]/15">
                            <span class="mr-2 text-sm font-bold text-[#68808B]">S/</span>
                            <input wire:model="montoMensual" type="number" min="0.01" max="99999999.99" step="0.01" inputmode="decimal" placeholder="0.00" class="h-10 w-full border-0 p-0 text-sm text-[#264858] focus:ring-0">
                        </span>
                        @error('montoMensual') <span class="mt-1 block text-xs font-medium text-[#B42318]">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="mb-2 block text-sm font-bold text-[#264858]">Día de vencimiento <span class="text-[#C84036]">*</span></span>
                        <select wire:model="diaVencimiento" class="min-h-12 w-full rounded-xl border-[#DDE8EB] bg-white text-sm text-[#264858] focus:border-[#0E8A9A] focus:ring-[#0E8A9A]">
                            @foreach (range(1, 28) as $dia)
                                <option value="{{ $dia }}">Día {{ $dia }}</option>
                            @endforeach
                        </select>
                        @error('diaVencimiento') <span class="mt-1 block text-xs font-medium text-[#B42318]">{{ $message }}</span> @enderror
                    </label>
                </div>
            </section>

            <aside class="flex gap-3 rounded-2xl border border-[#D8EBEF] bg-[#EAF6F8] p-4 text-sm leading-6 text-[#416673]">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="mt-0.5 size-5 shrink-0 text-[#0E8A9A]" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/><path d="M12 11v5m0-8h.01"/>
                </svg>
                <p>Verifica que los datos seleccionados sean correctos antes de continuar.</p>
            </aside>

            <button type="submit" wire:loading.attr="disabled" class="flex min-h-[52px] w-full items-center justify-center gap-2 rounded-2xl bg-[#0E8A9A] px-5 text-sm font-extrabold text-white shadow-[0_8px_18px_rgba(14,138,154,.2)] transition hover:bg-[#0B7583] focus:outline-none focus:ring-4 focus:ring-[#0E8A9A]/25 disabled:cursor-wait disabled:opacity-60">
                <span wire:loading.remove wire:target="confirmar">Confirmar matrícula</span>
                <span wire:loading wire:target="confirmar">Guardando matrícula...</span>
                <svg wire:loading.remove wire:target="confirmar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[18px]" aria-hidden="true">
                    <path d="M5 12h14m-6-6 6 6-6 6"/>
                </svg>
            </button>
            <p class="flex items-center justify-center gap-1.5 text-center text-xs text-[#8399A2]">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4" aria-hidden="true">
                    <rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3"/>
                </svg>
                Tu información está protegida.
            </p>
        </form>

        <div wire:loading.delay class="fixed inset-0 z-30 grid place-items-center bg-[#0B4F6C]/20 backdrop-blur-[2px]" aria-live="polite">
            <span class="rounded-2xl bg-white px-5 py-3 text-sm font-bold text-[#0B4F6C] shadow-lg">Actualizando información…</span>
        </div>
    </section>
</main>
