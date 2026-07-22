<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('seccion')->nullable();
            $table->string('categoria')->nullable();
            $table->string('calle')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('telefono', 100)->nullable();
            $table->text('enfermedad_cronica')->nullable();
            $table->boolean('requiere_medicamentos')->default(false);
            $table->enum('status', ['efectivo', 'rechazo', 'no_encontrado'])->default('efectivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
