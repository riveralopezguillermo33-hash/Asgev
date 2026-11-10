<?php

namespace Database\Seeders;

use App\Models\AnioEscolar;
use App\Models\Area;
use App\Models\Colegio;
use App\Models\Competencia;
use App\Models\Curso;
use App\Models\EscalaNota;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Nivel;
use App\Models\Nota;
use App\Models\Periodo;
use App\Models\Pension;
use App\Models\Seccion;
use App\Models\Sesion;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $demoPassword = env('ASGEV_DEMO_PASSWORD');
        if (! is_string($demoPassword) || strlen($demoPassword) < 12) {
            throw new \RuntimeException('Define ASGEV_DEMO_PASSWORD con al menos 12 caracteres en .env para ejecutar la semilla de demostración.');
        }

        DB::transaction(function () use ($demoPassword): void {
            foreach (['familia', 'estudiante', 'docente', 'administrador', 'caja', 'superadmin'] as $roleName) {
                Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            }

            $colegio = Colegio::create([
                'nombre' => 'Colegio de demostración',
                'nota_minima_aprobatoria' => 11,
                'moneda' => 'PEN',
                'periodo_tipo' => 'bimestre',
            ]);

            foreach ([
                [18, 20, 'Excelente', 'verde'],
                [14, 17.99, 'Muy bueno', 'azul'],
                [11, 13.99, 'Regular', 'ambar'],
                [0, 10.99, 'Desaprobado', 'rojo'],
            ] as [$desde, $hasta, $etiqueta, $color]) {
                EscalaNota::create([
                    'colegio_id' => $colegio->id,
                    'nota_desde' => $desde,
                    'nota_hasta' => $hasta,
                    'etiqueta' => $etiqueta,
                    'color' => $color,
                ]);
            }

            $year = (int) now()->format('Y');
            $start = CarbonImmutable::create($year, 3, 1);
            $end = CarbonImmutable::create($year, 12, 31);

            $anio = AnioEscolar::create([
                'colegio_id' => $colegio->id,
                'nombre' => (string) $year,
                'fecha_inicio' => $start,
                'fecha_fin' => $end,
                'vigente' => true,
            ]);

            $nivel = Nivel::create(['colegio_id' => $colegio->id, 'nombre' => 'Primaria']);
            $grado = Grado::create(['colegio_id' => $colegio->id, 'nivel_id' => $nivel->id, 'nombre' => 'Quinto']);
            $seccion = Seccion::create([
                'colegio_id' => $colegio->id,
                'grado_id' => $grado->id,
                'nombre' => 'A',
                'vacantes' => 30,
            ]);

            $periodo = Periodo::create([
                'colegio_id' => $colegio->id,
                'anio_id' => $anio->id,
                'nombre' => 'Bimestre 1',
                'fecha_inicio' => $start,
                'fecha_fin' => $start->addMonths(3),
            ]);

            $area = Area::create([
                'colegio_id' => $colegio->id,
                'nombre' => 'Comunicación',
                'icono' => 'book-open',
                'peso' => 1,
            ]);

            $competencias = collect([
                'Se comunica oralmente',
                'Lee diversos tipos de textos',
                'Escribe diversos tipos de textos',
                'Gestiona su aprendizaje',
            ])->map(fn (string $nombre) => Competencia::create([
                'colegio_id' => $colegio->id,
                'area_id' => $area->id,
                'nombre' => $nombre,
                'descripcion' => 'Competencia curricular configurada por el colegio.',
                'icono' => 'target',
                'peso' => 1,
            ]));

            $familia = $this->user($colegio, $demoPassword, 'familia', 'familia');
            $docente = $this->user($colegio, $demoPassword, 'docente', 'docente');
            $this->user($colegio, $demoPassword, 'administrador', 'administracion');
            $this->user($colegio, $demoPassword, 'caja', 'caja');
            $this->user(null, $demoPassword, 'superadmin', 'superadmin');

            $seccion->asignaciones()->create([
                'colegio_id' => $colegio->id,
                'docente_id' => $docente->id,
                'area_id' => $area->id,
            ]);

            $notasEjemplo = [0, 11, 15.5, 20];
            foreach (range(1, 3) as $studentNumber) {
                $studentUser = $this->user(
                    $colegio,
                    $demoPassword,
                    'estudiante',
                    "estudiante{$studentNumber}",
                );
                $estudiante = Estudiante::create([
                    'colegio_id' => $colegio->id,
                    'user_id' => $studentUser->id,
                    'codigo' => sprintf('ALU-%04d', $studentNumber),
                ]);
                $familia->hijos()->attach($estudiante->id, [
                    'colegio_id' => $colegio->id,
                    'parentesco' => 'Madre/Padre',
                ]);

                $matricula = Matricula::create([
                    'colegio_id' => $colegio->id,
                    'estudiante_id' => $estudiante->id,
                    'seccion_id' => $seccion->id,
                    'anio_id' => $anio->id,
                    'estado' => 'activa',
                ]);

                foreach ($competencias as $index => $competencia) {
                    Nota::create([
                        'colegio_id' => $colegio->id,
                        'matricula_id' => $matricula->id,
                        'competencia_id' => $competencia->id,
                        'periodo_id' => $periodo->id,
                        'nota' => $notasEjemplo[($index + $studentNumber - 1) % count($notasEjemplo)],
                        'registrado_por' => $docente->id,
                    ]);
                }

                foreach (range(0, 2) as $monthOffset) {
                    $month = $start->addMonths($monthOffset);
                    Pension::create([
                        'colegio_id' => $colegio->id,
                        'matricula_id' => $matricula->id,
                        'mes' => $month->startOfMonth(),
                        'monto' => 350,
                        'vencimiento' => $month->day(10),
                        'estado' => 'pendiente',
                    ]);
                }
            }

            $curso = Curso::create([
                'colegio_id' => $colegio->id,
                'titulo' => 'Estrategias para el aula',
                'descripcion' => 'Material de formación docente.',
                'docente_id' => $docente->id,
                'duracion_semanas' => 6,
                'nivel' => 'Todos los niveles',
            ]);

            foreach (range(1, 6) as $order) {
                Sesion::create([
                    'colegio_id' => $colegio->id,
                    'curso_id' => $curso->id,
                    'orden' => $order,
                    'titulo' => "Sesión {$order}",
                    'descripcion' => 'Contenido de formación para docentes.',
                    'duracion_min' => 30,
                ]);
            }
        });

        $this->command?->info('Datos de demostración creados. Las cuentas comparten ASGEV_DEMO_PASSWORD.');
        $this->command?->line('Correos de acceso: familia@asgev.test, estudiante1@asgev.test, estudiante2@asgev.test, estudiante3@asgev.test, docente@asgev.test, administracion@asgev.test, caja@asgev.test, superadmin@asgev.test');
    }

    private function user(?Colegio $colegio, string $password, string $role, string $emailPrefix): User
    {
        $user = User::create([
            'colegio_id' => $colegio?->id,
            'nombres' => Str::headline($emailPrefix),
            'apellidos' => 'Demostración',
            'email' => "{$emailPrefix}@asgev.test",
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);
        $user->assignRole($role);

        return $user;
    }
}
