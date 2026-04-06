<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('agentes', function (Blueprint $table) {
            $table->string('docAgente', 36)->primary();
            $table->string('nitInmobiliaria', 20)->nullable();
            $table->string('especialidad', 120)->nullable();
            $table->string('carrera', 100)->nullable();
            $table->integer('experiencia_anos')->default(0);
            $table->decimal('promedio_valoracion', 3, 2)->default(0.00);
            $table->string('estado', 20)->default('activo');
            $table->string('cv_path', 255)->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('docAgente')->references('docUsuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('nitInmobiliaria')->references('nitInmobiliaria')->on('inmobiliarias')->onDelete('set null')->onUpdate('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('agentes');
    }
};