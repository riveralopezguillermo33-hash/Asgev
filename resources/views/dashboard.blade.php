<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0e8a9a">
    <title>{{ config('app.name') }} | Portal educativo</title>
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F2F9FB] text-[#143747] antialiased">
    @php
        $role = $user->getRoleNames()->first();
        $roleLabels = [
            'familia' => 'Portal para Familias',
            'estudiante' => 'Portal del Estudiante',
            'docente' => 'Portal Docente',
            'administrador' => 'Administración',
            'caja' => 'Administración y Caja',
            'superadmin' => 'Superadministración',
        ];
        $items = [
            ['Inicio', 'home', true],
            ['Mis hijos', 'users', false],
            ['Comunicados', 'message', false],
            ['Calendario', 'calendar', false],
            ['Pagos', 'wallet', false],
        ];
    @endphp

    <header class="relative overflow-hidden rounded-b-[30px] bg-[#0E8A9A] px-5 pb-8 pt-5 text-white shadow-[0_10px_30px_rgba(11,79,108,.12)] sm:px-8 sm:pb-10 md:rounded-b-[38px]">
        <div class="pointer-events-none absolute -right-16 -top-32 size-72 rounded-full border border-white/10"></div>
        <div class="relative mx-auto max-w-6xl">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2.5 text-white no-underline" aria-label="ASGEV, inicio">
                    <span class="grid size-10 place-items-center rounded-[14px] bg-white/15">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-6" aria-hidden="true">
                            <path d="M3 5.5c3.5-.8 6.5 0 9 2.2 2.5-2.2 5.5-3 9-2.2v13c-3.5-.8-6.5 0-9 2.2-2.5-2.2-5.5-3-9-2.2v-13Z"/><path d="M12 7.7v13"/>
                        </svg>
                    </span>
                    <span class="text-[22px] font-extrabold tracking-[-1.2px]">ASGEV</span>
                </a>
                <div class="flex items-center gap-2">
                    <button class="relative grid size-11 place-items-center rounded-2xl bg-white/10 text-white" aria-label="Notificaciones, {{ $unreadNotifications }} sin leer" title="Notificaciones">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 12h4"/>
                        </svg>
                        @if ($unreadNotifications > 0)
                            <span class="absolute right-1 top-1 grid min-h-4 min-w-4 place-items-center rounded-full bg-[#F4AD45] px-1 text-[9px] font-extrabold text-[#143747]">{{ $unreadNotifications }}</span>
                        @endif
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="min-h-11 rounded-2xl bg-white/10 px-4 text-xs font-bold text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/70" type="submit">Salir</button>
                    </form>
                </div>
            </div>
            <div class="mt-8 sm:mt-10">
                <p class="text-sm font-semibold text-white/75">{{ $user->colegio?->nombre ?? 'ASGEV' }}</p>
                <h1 class="mt-1 text-[25px] font-extrabold tracking-[-.7px] sm:text-[30px]">{{ $roleLabels[$role] ?? 'Portal educativo' }}</h1>
                <p class="mt-2 text-sm text-white/80">Hola, {{ $user->nombres }}. Nos alegra verte.</p>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-5 pb-28 pt-6 sm:px-8 sm:pt-8 md:pb-10">
        <section class="rounded-[22px] border border-[#E5EFF1] bg-white p-5 shadow-[0_6px_24px_rgba(20,55,71,.035)] sm:p-7">
            <div class="flex items-start gap-4">
                <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-[#E8F6F7] text-[#0E8A9A]">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-6" aria-hidden="true">
                        <path d="M3 5.5c3.5-.8 6.5 0 9 2.2 2.5-2.2 5.5-3 9-2.2v13c-3.5-.8-6.5 0-9 2.2-2.5-2.2-5.5-3-9-2.2v-13Z"/><path d="M12 7.7v13"/>
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.12em] text-[#0E8A9A]">Aprende hoy, lidera mañana</p>
                    <h2 class="mt-1 text-lg font-extrabold text-[#0B4F6C]">Tu espacio educativo</h2>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-[#68808B]">
                        Consulta la información escolar vinculada a tu cuenta. Los accesos se habilitarán cuando se complete la configuración correspondiente.
                    </p>
                </div>
            </div>
        </section>

        @can('create', \App\Models\Matricula::class)
            <a href="{{ route('matriculas.create') }}" class="mt-5 flex min-h-[76px] items-center gap-4 rounded-[18px] border border-[#CBE7EA] bg-white px-4 py-4 shadow-[0_6px_24px_rgba(20,55,71,.035)] transition hover:border-[#0E8A9A] hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#0E8A9A]/15">
                <span class="grid size-11 place-items-center rounded-[14px] bg-[#E8F6F7] text-[#0E8A9A]">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true">
                        <path d="M12 5v14m-7-7h14"/>
                    </svg>
                </span>
                <span class="flex-1">
                    <span class="block text-sm font-extrabold text-[#0B4F6C]">Crear matrícula</span>
                    <span class="mt-1 block text-xs text-[#68808B]">Registrar estudiante y programar sus pensiones</span>
                </span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-[18px] text-[#0E8A9A]" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        @endcan

        @if ($user->hasAnyRole(['familia', 'estudiante']))
            <a href="{{ route('notas.index') }}" class="mt-5 flex min-h-[76px] items-center gap-4 rounded-[18px] border border-[#CBE7EA] bg-white px-4 py-4 shadow-[0_6px_24px_rgba(20,55,71,.035)] transition hover:border-[#0E8A9A] hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#0E8A9A]/15">
                <span class="grid size-11 place-items-center rounded-[14px] bg-[#E8F6F7] text-[#0E8A9A]">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v15H6.5A2.5 2.5 0 0 1 4 14.5v-10A2.5 2.5 0 0 1 6.5 2Z"/><path d="M8.5 7.5h8M8.5 11h8"/>
                    </svg>
                </span>
                <span class="flex-1">
                    <span class="block text-sm font-extrabold text-[#0B4F6C]">Mis notas</span>
                    <span class="mt-1 block text-xs text-[#68808B]">Consulta notas por competencia y periodo</span>
                </span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-[18px] text-[#0E8A9A]" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        @endif

        @if ($user->hasRole('familia'))
            <section class="mt-8" aria-labelledby="children-heading">
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.12em] text-[#7E969F]">Portal para Familias</p>
                        <h2 id="children-heading" class="mt-1 text-xl font-extrabold tracking-[-.4px] text-[#0B4F6C]">Mis hijos</h2>
                    </div>
                    <span class="rounded-full bg-[#E5F3F4] px-3 py-1.5 text-[11px] font-bold text-[#0E8A9A]">{{ $children->count() }}</span>
                </div>
                @if ($children->isEmpty())
                    <div class="rounded-[20px] border border-dashed border-[#BFD5DB] bg-white px-5 py-9 text-center">
                        <p class="text-sm font-bold text-[#264858]">Aún no hay estudiantes vinculados a tu cuenta.</p>
                        <p class="mt-2 text-sm text-[#68808B]">Cuando el colegio registre el vínculo familiar, la información aparecerá aquí.</p>
                    </div>
                @else
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($children as $child)
                            @php($enrollment = $child->matriculas->first())
                            <article class="rounded-[20px] border border-[#E5EFF1] bg-white p-5 shadow-[0_6px_24px_rgba(20,55,71,.035)]">
                                <div class="flex items-center gap-3">
                                    <span class="grid size-12 shrink-0 place-items-center rounded-full bg-[#E8F6F7] text-sm font-extrabold text-[#0E8A9A]">
                                        {{ mb_substr($child->user->nombres, 0, 1) }}{{ mb_substr($child->user->apellidos, 0, 1) }}
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="truncate text-sm font-extrabold text-[#0B4F6C]">{{ $child->user->nombres }} {{ $child->user->apellidos }}</h3>
                                        <p class="mt-1 text-xs text-[#68808B]">{{ $enrollment?->seccion?->grado?->nivel?->nombre ?? 'Nivel pendiente' }} · {{ $enrollment?->seccion?->grado?->nombre ?? 'Grado pendiente' }}</p>
                                    </div>
                                </div>
                                <p class="mt-4 border-t border-[#EDF2F3] pt-3 text-xs font-semibold text-[#68808B]">
                                    {{ $enrollment?->anioEscolar?->nombre ?? 'Sin matrícula activa' }}
                                </p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif

        <section class="mt-8" aria-labelledby="links-heading">
            <div class="mb-4 flex items-end justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.12em] text-[#7E969F]">ASGEV</p>
                    <h2 id="links-heading" class="mt-1 text-xl font-extrabold tracking-[-.4px] text-[#0B4F6C]">Accesos del portal</h2>
                </div>
                <span class="rounded-full bg-[#E5F3F4] px-3 py-1.5 text-[11px] font-bold text-[#0E8A9A]">En preparación</span>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach (array_slice($items, 1) as [$label, $icon])
                    <div class="flex min-h-[86px] items-center gap-4 rounded-[18px] border border-[#E5EFF1] bg-white px-4 py-4 opacity-75" aria-disabled="true">
                        <span class="grid size-11 place-items-center rounded-[14px] bg-[#F0F8F9] text-[#0E8A9A]">
                            @if ($icon === 'users')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-7.5a4 4 0 0 1 0 7.75M20 21v-2a4 4 0 0 0-3-3.87"/></svg>
                            @elseif ($icon === 'message')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5Z"/></svg>
                            @elseif ($icon === 'calendar')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                            @endif
                        </span>
                        <span class="flex-1 text-sm font-bold text-[#264858]">{{ $label }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-[18px] text-[#A5B6BC]" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    </div>
                @endforeach
            </div>
        </section>
    </main>

    <nav class="fixed inset-x-0 bottom-0 z-20 border-t border-[#E8EFF1] bg-white/95 px-2 pb-[max(env(safe-area-inset-bottom),8px)] pt-2 backdrop-blur md:hidden" aria-label="Navegación principal">
        <ul class="mx-auto flex max-w-lg items-center justify-around">
            @foreach ($items as [$label, $icon, $active])
                <li class="flex-1">
                    <button class="mx-auto flex min-h-[54px] w-full flex-col items-center justify-center gap-1 rounded-xl text-[10px] font-bold {{ $active ? 'text-[#0E8A9A]' : 'text-[#8399A2]' }}" aria-current="{{ $active ? 'page' : 'false' }}" @disabled(! $active)>
                        @if ($icon === 'home')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" class="size-5" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10Z"/></svg>
                        @elseif ($icon === 'users')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" class="size-5" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-7.5a4 4 0 0 1 0 7.75M20 21v-2a4 4 0 0 0-3-3.87"/></svg>
                        @elseif ($icon === 'message')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" class="size-5" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5Z"/></svg>
                        @elseif ($icon === 'calendar')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" class="size-5" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" class="size-5" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                        @endif
                        {{ $label }}
                    </button>
                </li>
            @endforeach
        </ul>
    </nav>
</body>
</html>
