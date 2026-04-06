<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disponibilidades', function (Blueprint $table) {
            $table->id('idDisponibilidad');
            $table->string('docAgente', 36);
            $table->unsignedTinyInteger('dia_semana'); // 1=Lunes … 7=Domingo
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('estado', 20)->default('disponible');
            $table->timestamps();

            $table->foreign('docAgente')
                  ->references('docAgente')
                  ->on('agentes')
                  ->onDelete('cascade');

            // Un agente no puede tener dos bloques para el mismo día
            $table->unique(['docAgente', 'dia_semana']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disponibilidades');
    }
};

