<?php

use App\Livewire\Matriculas\CrearMatricula;
use App\Models\AnioEscolar;
use App\Models\Colegio;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Nivel;
use App\Models\Pension;
use App\Models\Seccion;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function crearDatosMatricula(): array
{
    $colegio = Colegio::create(['nombre' => 'Colegio de prueba']);
    $adminRole = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
    $cajaRole = Role::firstOrCreate(['name' => 'caja', 'guard_name' => 'web']);
    $familyRole = Role::firstOrCreate(['name' => 'familia', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => 'web']);

    $admin = User::factory()->create(['colegio_id' => $colegio->id]);
    $admin->assignRole($adminRole);

    $family = User::factory()->create(['colegio_id' => $colegio->id]);
    $family->assignRole($familyRole);

    $studentUser = User::factory()->create(['colegio_id' => $colegio->id]);
    $studentUser->assignRole('estudiante');
    $student = Estudiante::create([
        'colegio_id' => $colegio->id,
        'user_id' => $studentUser->id,
        'codigo' => 'ALU-PRUEBA',
    ]);

    $nivel = Nivel::create(['colegio_id' => $colegio->id, 'nombre' => 'Primaria']);
    $grado = Grado::create(['colegio_id' => $colegio->id, 'nivel_id' => $nivel->id, 'nombre' => 'Segundo']);
    $seccion = Seccion::create([
        'colegio_id' => $colegio->id,
        'grado_id' => $grado->id,
        'nombre' => 'B',
        'vacantes' => 1,
    ]);
    $anio = AnioEscolar::create([
        'colegio_id' => $colegio->id,
        'nombre' => 'Próximo ciclo',
        'fecha_inicio' => Carbon::parse('2027-03-01'),
        'fecha_fin' => Carbon::parse('2027-12-31'),
        'vigente' => true,
    ]);

    return compact('admin', 'family', 'student', 'nivel', 'grado', 'seccion', 'anio');
}

it('allows administration and caja to open the enrollment form, but not families', function () {
    $data = crearDatosMatricula();

    $this->actingAs($data['admin'])
        ->get(route('matriculas.create'))
        ->assertOk()
        ->assertSee('Completa la información para continuar')
        ->assertSee('Monto mensual (S/)');

    $caja = User::factory()->create(['colegio_id' => $data['admin']->colegio_id]);
    $caja->assignRole('caja');
    $this->actingAs($caja)->get(route('matriculas.create'))->assertOk();

    $this->actingAs($data['family'])
        ->get(route('matriculas.create'))
        ->assertForbidden();
});

it('creates an enrollment and all monthly pensions in one operation', function () {
    $data = crearDatosMatricula();

    Livewire::actingAs($data['admin'])
        ->test(CrearMatricula::class)
        ->set('estudianteId', $data['student']->id)
        ->set('nivelId', $data['nivel']->id)
        ->set('gradoId', $data['grado']->id)
        ->set('seccionId', $data['seccion']->id)
        ->set('anioId', $data['anio']->id)
        ->set('montoMensual', '345.50')
        ->set('diaVencimiento', 10)
        ->call('confirmar')
        ->assertHasNoErrors()
        ->assertRedirect(route('matriculas.create'));

    $this->assertDatabaseHas('matriculas', [
        'estudiante_id' => $data['student']->id,
        'anio_id' => $data['anio']->id,
        'seccion_id' => $data['seccion']->id,
        'estado' => 'activa',
    ]);

    $this->assertDatabaseCount('pensiones', 10);
    expect(Pension::query()
        ->where('monto', 345.50)
        ->whereDate('vencimiento', '2027-03-10')
        ->where('estado', 'pendiente')
        ->exists())->toBeTrue();
});

it('prevents a second enrollment for the same student and year without leaving partial pensions', function () {
    $data = crearDatosMatricula();
    $create = fn () => Livewire::actingAs($data['admin'])
        ->test(CrearMatricula::class)
        ->set('estudianteId', $data['student']->id)
        ->set('nivelId', $data['nivel']->id)
        ->set('gradoId', $data['grado']->id)
        ->set('seccionId', $data['seccion']->id)
        ->set('anioId', $data['anio']->id)
        ->set('montoMensual', '345.50')
        ->set('diaVencimiento', 10)
        ->call('confirmar');

    $create()->assertHasNoErrors();
    $create()->assertHasErrors('estudianteId');

    $this->assertDatabaseCount('matriculas', 1);
    $this->assertDatabaseCount('pensiones', 10);
});

it('does not enroll a second student when a section has no remaining seats', function () {
    $data = crearDatosMatricula();

    Livewire::actingAs($data['admin'])
        ->test(CrearMatricula::class)
        ->set('estudianteId', $data['student']->id)
        ->set('nivelId', $data['nivel']->id)
        ->set('gradoId', $data['grado']->id)
        ->set('seccionId', $data['seccion']->id)
        ->set('anioId', $data['anio']->id)
        ->set('montoMensual', '345.50')
        ->set('diaVencimiento', 10)
        ->call('confirmar')
        ->assertHasNoErrors();

    $secondStudentUser = User::factory()->create(['colegio_id' => $data['admin']->colegio_id]);
    $secondStudent = Estudiante::create([
        'colegio_id' => $data['admin']->colegio_id,
        'user_id' => $secondStudentUser->id,
        'codigo' => 'ALU-SEGUNDO',
    ]);

    Livewire::actingAs($data['admin'])
        ->test(CrearMatricula::class)
        ->set('estudianteId', $secondStudent->id)
        ->set('nivelId', $data['nivel']->id)
        ->set('gradoId', $data['grado']->id)
        ->set('seccionId', $data['seccion']->id)
        ->set('anioId', $data['anio']->id)
        ->set('montoMensual', '345.50')
        ->set('diaVencimiento', 10)
        ->call('confirmar')
        ->assertHasErrors('seccionId');

    $this->assertDatabaseCount('matriculas', 1);
    $this->assertDatabaseCount('pensiones', 10);
});
