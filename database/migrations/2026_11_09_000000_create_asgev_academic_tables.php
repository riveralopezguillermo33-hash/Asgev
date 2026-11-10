<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escala_notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->decimal('nota_desde', 4, 2);
            $table->decimal('nota_hasta', 4, 2);
            $table->string('etiqueta');
            $table->string('color', 16);
            $table->unique(['colegio_id', 'nota_desde']);
        });

        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('codigo');
            $table->unique(['colegio_id', 'codigo']);
            $table->timestamps();
        });

        Schema::create('apoderado_estudiante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('apoderado_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->string('parentesco');
            $table->timestamps();
            $table->unique(['apoderado_id', 'estudiante_id']);
        });

        Schema::create('anios_escolares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->string('nombre');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('vigente')->default(false);
            $table->timestamps();
            $table->unique(['id', 'colegio_id']);
        });

        Schema::create('niveles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->string('nombre');
            $table->timestamps();
            $table->unique(['colegio_id', 'nombre']);
            $table->unique(['id', 'colegio_id']);
        });

        Schema::create('grados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('nivel_id')->constrained('niveles')->cascadeOnDelete();
            $table->string('nombre');
            $table->timestamps();
            $table->unique(['nivel_id', 'nombre']);
            $table->unique(['id', 'colegio_id']);
        });

        Schema::create('secciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('grado_id')->constrained('grados')->cascadeOnDelete();
            $table->string('nombre');
            $table->unsignedSmallInteger('vacantes');
            $table->timestamps();
            $table->unique(['grado_id', 'nombre']);
            $table->unique(['id', 'colegio_id']);
        });

        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->foreignId('seccion_id')->constrained('secciones')->restrictOnDelete();
            $table->foreignId('anio_id')->constrained('anios_escolares')->restrictOnDelete();
            $table->string('estado')->default('pendiente');
            $table->timestamps();
            $table->unique(['estudiante_id', 'anio_id']);
            $table->unique(['id', 'colegio_id']);
        });

        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('anio_id')->constrained('anios_escolares')->cascadeOnDelete();
            $table->string('nombre');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamps();
            $table->unique(['anio_id', 'nombre']);
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('icono')->default('book-open');
            $table->decimal('peso', 7, 3)->default(1);
            $table->timestamps();
            $table->unique(['colegio_id', 'nombre']);
        });

        Schema::create('competencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('icono')->default('target');
            $table->decimal('peso', 7, 3)->default(1);
            $table->timestamps();
            $table->unique(['area_id', 'nombre']);
        });

        Schema::create('asignaciones_docentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('docente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seccion_id')->constrained('secciones')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['docente_id', 'seccion_id', 'area_id']);
        });

        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('competencia_id')->constrained('competencias')->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
            $table->decimal('nota', 4, 2);
            $table->foreignId('registrado_por')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['matricula_id', 'competencia_id', 'periodo_id']);
        });

        Schema::create('notas_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('nota_id')->constrained('notas')->cascadeOnDelete();
            $table->decimal('nota_anterior', 4, 2)->nullable();
            $table->decimal('nota_nueva', 4, 2);
            $table->foreignId('cambiado_por')->constrained('users')->restrictOnDelete();
            $table->timestamp('fecha')->useCurrent();
        });

        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'pgsql'], true)) {
            DB::statement('ALTER TABLE colegios ADD CONSTRAINT colegios_nota_minima_check CHECK (nota_minima_aprobatoria >= 0 AND nota_minima_aprobatoria <= 20)');
            DB::statement('ALTER TABLE escala_notas ADD CONSTRAINT escala_notas_rango_check CHECK (nota_desde >= 0 AND nota_hasta <= 20 AND nota_desde <= nota_hasta)');
            DB::statement('ALTER TABLE notas ADD CONSTRAINT notas_rango_check CHECK (nota >= 0 AND nota <= 20)');
            DB::statement('ALTER TABLE notas_historial ADD CONSTRAINT notas_historial_rango_check CHECK (nota_nueva >= 0 AND nota_nueva <= 20)');
        }

        if ($driver === 'sqlite') {
            DB::unprepared("CREATE TRIGGER notas_rango_insert BEFORE INSERT ON notas WHEN NEW.nota < 0 OR NEW.nota > 20 BEGIN SELECT RAISE(ABORT, 'La nota debe estar entre 0 y 20'); END");
            DB::unprepared("CREATE TRIGGER notas_rango_update BEFORE UPDATE OF nota ON notas WHEN NEW.nota < 0 OR NEW.nota > 20 BEGIN SELECT RAISE(ABORT, 'La nota debe estar entre 0 y 20'); END");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::unprepared('DROP TRIGGER IF EXISTS notas_rango_update');
            DB::unprepared('DROP TRIGGER IF EXISTS notas_rango_insert');
        }

        Schema::dropIfExists('notas_historial');
        Schema::dropIfExists('notas');
        Schema::dropIfExists('asignaciones_docentes');
        Schema::dropIfExists('competencias');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('periodos');
        Schema::dropIfExists('matriculas');
        Schema::dropIfExists('secciones');
        Schema::dropIfExists('grados');
        Schema::dropIfExists('niveles');
        Schema::dropIfExists('anios_escolares');
        Schema::dropIfExists('apoderado_estudiante');
        Schema::dropIfExists('estudiantes');
        Schema::dropIfExists('escala_notas');
    }
};
