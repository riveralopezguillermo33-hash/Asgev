<?php

use App\Models\AnioEscolar;
use App\Models\Area;
use App\Models\Colegio;
use App\Models\Competencia;
use App\Models\EscalaNota;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Nivel;
use App\Models\Nota;
use App\Models\Periodo;
use App\Models\Seccion;
use App\Models\User;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

it('loads the personal notes screen with real data from the database', function () {
    foreach (['familia', 'estudiante'] as $roleName) {
        Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
    }

    $colegio = Colegio::create([
        'nombre' => 'Colegio de pruebas',
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

    $family = User::factory()->create([
        'colegio_id' => $colegio->id,
        'nombres' => 'Ana',
        'apellidos' => 'Pérez',
        'email' => 'ana@asgev.test',
    ]);
    $family->assignRole('familia');

    $studentUser = User::factory()->create([
        'colegio_id' => $colegio->id,
        'nombres' => 'Lucas',
        'apellidos' => 'Santos',
        'email' => 'lucas@asgev.test',
    ]);
    $studentUser->assignRole('estudiante');

    $estudiante = Estudiante::create([
        'colegio_id' => $colegio->id,
        'user_id' => $studentUser->id,
        'codigo' => 'ALU-1001',
    ]);

    $family->hijos()->attach($estudiante->id, [
        'colegio_id' => $colegio->id,
        'parentesco' => 'Madre',
    ]);

    $nivel = Nivel::create(['colegio_id' => $colegio->id, 'nombre' => 'Primaria']);
    $grado = Grado::create(['colegio_id' => $colegio->id, 'nivel_id' => $nivel->id, 'nombre' => 'Quinto']);
    $seccion = Seccion::create(['colegio_id' => $colegio->id, 'grado_id' => $grado->id, 'nombre' => 'A', 'vacantes' => 30]);

    $anio = AnioEscolar::create([
        'colegio_id' => $colegio->id,
        'nombre' => '2026',
        'fecha_inicio' => Carbon::create(2026, 3, 1),
        'fecha_fin' => Carbon::create(2026, 12, 31),
        'vigente' => true,
    ]);

    $periodo = Periodo::create([
        'colegio_id' => $colegio->id,
        'anio_id' => $anio->id,
        'nombre' => 'Bimestre 1',
        'fecha_inicio' => Carbon::create(2026, 3, 1),
        'fecha_fin' => Carbon::create(2026, 5, 31),
    ]);

    $area = Area::create([
        'colegio_id' => $colegio->id,
        'nombre' => 'Comunicación',
        'icono' => 'book-open',
        'peso' => 1,
    ]);

    $competencia = Competencia::create([
        'colegio_id' => $colegio->id,
        'area_id' => $area->id,
        'nombre' => 'Se comunica oralmente',
        'descripcion' => 'Expresa sus ideas con claridad.',
        'icono' => 'mic',
        'peso' => 1,
    ]);

    $matricula = Matricula::create([
        'colegio_id' => $colegio->id,
        'estudiante_id' => $estudiante->id,
        'seccion_id' => $seccion->id,
        'anio_id' => $anio->id,
        'estado' => 'activa',
    ]);

    Nota::create([
        'colegio_id' => $colegio->id,
        'matricula_id' => $matricula->id,
        'competencia_id' => $competencia->id,
        'periodo_id' => $periodo->id,
        'nota' => 17.5,
        'registrado_por' => $family->id,
    ]);

    $this->actingAs($family)
        ->get(route('notas.index'))
        ->assertOk()
        ->assertSee('Notas por competencias')
        ->assertSee('Se comunica oralmente')
        ->assertSee('17.5')
        ->assertSee('Muy bueno');
});
