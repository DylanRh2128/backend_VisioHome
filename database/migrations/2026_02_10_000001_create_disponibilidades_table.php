<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('disponibilidades');
        Schema::create('disponibilidades', function (Blueprint $table) {
            $table->bigIncrements('idDisponibilidad');
            $table->string('docAgente', 36);
            $table->string('dia_semana', 20);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->enum('estado', ['disponible', 'reservada', 'comprada'])->default('disponible');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('disponibilidades');
    }
};


