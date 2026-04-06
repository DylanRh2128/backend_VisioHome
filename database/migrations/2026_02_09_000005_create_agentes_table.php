<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('agentes');
        Schema::create('agentes', function (Blueprint $table) {
            $table->string('docAgente', 36)->primary();
            $table->string('nombre', 120);
            $table->string('carrera', 100)->nullable();
            $table->string('especialidad', 120)->nullable();
            $table->integer('experiencia_anos')->default(0);
            $table->string('direccion', 200)->nullable();
            $table->string('ciudad', 80)->nullable();
            $table->string('correo', 180)->unique();
            $table->string('telefono', 30)->nullable();
            $table->string('nitInmobiliaria', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->decimal('promedio_valoracion', 3, 2)->default(0.00);
            $table->string('cv_path', 255)->nullable();

            $table->foreign('nitInmobiliaria')->references('nitInmobiliaria')->on('inmobiliarias')->onDelete('set null')->onUpdate('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('agentes');
    }
};
