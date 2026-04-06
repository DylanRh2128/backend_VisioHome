<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('propiedades', function (Blueprint $table) {
            $table->unsignedBigInteger('idPropiedad')->primary();
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->string('ubicacion', 255);
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->decimal('tamano_m2', 10, 2)->nullable();
            $table->decimal('precio', 15, 2);
            $table->string('estado', 20);
            $table->string('tipo', 20);
            $table->string('nitInmobiliaria', 20);
            $table->string('modelo_3d_path', 255)->nullable();
            $table->string('imagen', 255)->nullable();
            $table->string('categoria_ciudad', 100)->nullable();
            $table->dateTime('creado_en')->useCurrent();
            $table->dateTime('actualizado_en')->nullable();
            
            // Foreign Keys
            $table->foreign('nitInmobiliaria')->references('nitInmobiliaria')->on('inmobiliarias')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('propiedades');
    }
};