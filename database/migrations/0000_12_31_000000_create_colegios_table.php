<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colegios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('logo_path')->nullable();
            $table->decimal('nota_minima_aprobatoria', 4, 2)->default(11);
            $table->char('moneda', 3)->default('PEN');
            $table->string('periodo_tipo')->default('bimestre');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colegios');
    }
};
