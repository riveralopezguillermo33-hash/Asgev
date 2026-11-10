<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->foreignId('docente_id')->constrained('users')->restrictOnDelete();
            $table->unsignedSmallInteger('duracion_semanas');
            $table->string('nivel');
            $table->timestamps();
        });

        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->unsignedSmallInteger('orden');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->unsignedSmallInteger('duracion_min');
            $table->timestamps();
            $table->unique(['curso_id', 'orden']);
        });

        Schema::create('recursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('sesion_id')->constrained('sesiones')->cascadeOnDelete();
            $table->string('tipo');
            $table->string('path');
            $table->timestamps();
        });

        Schema::create('progreso_sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sesion_id')->constrained('sesiones')->cascadeOnDelete();
            $table->boolean('completada')->default(false);
            $table->timestamps();
            $table->unique(['usuario_id', 'sesion_id']);
        });

        Schema::create('pensiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->date('mes');
            $table->decimal('monto', 10, 2);
            $table->date('vencimiento');
            $table->string('estado')->default('pendiente');
            $table->timestamps();
            $table->unique(['matricula_id', 'mes']);
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('pension_id')->constrained('pensiones')->cascadeOnDelete();
            $table->string('imagen_path');
            $table->string('nro_operacion')->unique();
            $table->timestamp('fecha_pago');
            $table->string('nombre')->nullable();
            $table->string('dni', 8)->nullable();
            $table->string('concepto')->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('medio_pago');
            $table->string('sucursal')->nullable();
            $table->string('estado')->default('en_revision');
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo_rechazo')->nullable();
            $table->timestamps();
        });

        Schema::create('boletas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('voucher_id')->unique()->constrained('vouchers')->cascadeOnDelete();
            $table->string('numero');
            $table->string('pdf_path');
            $table->timestamp('emitida_en');
            $table->timestamps();
            $table->unique(['colegio_id', 'numero']);
        });

        Schema::create('comunicados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('nivel_id')->nullable()->constrained('niveles')->cascadeOnDelete();
            $table->foreignId('grado_id')->nullable()->constrained('grados')->cascadeOnDelete();
            $table->foreignId('seccion_id')->nullable()->constrained('secciones')->cascadeOnDelete();
            $table->string('titulo');
            $table->longText('contenido');
            $table->foreignId('creado_por')->constrained('users')->restrictOnDelete();
            $table->timestamp('publicado_en')->nullable();
            $table->timestamps();
        });

        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('remitente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('destinatario_id')->constrained('users')->cascadeOnDelete();
            $table->text('contenido');
            $table->timestamp('leido_en')->nullable();
            $table->timestamps();
            $table->index(['remitente_id', 'destinatario_id', 'created_at']);
        });

        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('seccion_id')->constrained('secciones')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('docente_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('aula')->nullable();
            $table->timestamps();
        });

        Schema::create('eventos_calendario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colegio_id')->constrained('colegios')->cascadeOnDelete();
            $table->foreignId('nivel_id')->nullable()->constrained('niveles')->cascadeOnDelete();
            $table->foreignId('grado_id')->nullable()->constrained('grados')->cascadeOnDelete();
            $table->foreignId('seccion_id')->nullable()->constrained('secciones')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->dateTime('inicia_en');
            $table->dateTime('termina_en');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_calendario');
        Schema::dropIfExists('horarios');
        Schema::dropIfExists('mensajes');
        Schema::dropIfExists('comunicados');
        Schema::dropIfExists('boletas');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('pensiones');
        Schema::dropIfExists('progreso_sesiones');
        Schema::dropIfExists('recursos');
        Schema::dropIfExists('sesiones');
        Schema::dropIfExists('cursos');
    }
};
