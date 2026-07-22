<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->dateTime('fecha_hora')->nullable();
            $table->date('proxima_visita')->nullable();
            $table->text('notas')->nullable();
            $table->enum('status', ['pendiente', 'en_curso', 'completado', 'pospuesto', 'cancelado'])
                  ->default('pendiente');
            $table->enum('source', ['manual', 'excel'])->default('manual');
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
