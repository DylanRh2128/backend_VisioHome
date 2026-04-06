<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('propiedades');
        Schema::create('propiedades', function (Blueprint $table) {
            $table->bigIncrements('idPropiedad');
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->string('ubicacion', 255);
            $table->string('ciudad', 100)->nullable();
            $table->decimal('tamano_m2', 10, 2)->nullable();
            $table->decimal('precio', 15, 2);
            $table->string('estado', 20);
            $table->string('tipo', 20);
            $table->string('nitInmobiliaria', 20);
            $table->dateTime('creado_en')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('actualizado_en')->nullable();
            $table->string('modelo_3d_path', 255)->nullable();
            $table->string('imagen', 255)->nullable();
            $table->string('categoria_ciudad', 100)->nullable();

            // Foreign Keys
            $table->foreign('nitInmobiliaria')->references('nitInmobiliaria')->on('inmobiliarias');
        });

        // Add check constraints exactly as in SQL dump
        DB::statement("ALTER TABLE \"propiedades\" ADD CONSTRAINT \"propiedades_chk_1\" CHECK (\"estado\" IN ('disponible', 'reservada', 'vendida', 'arrendada'))");
        DB::statement("ALTER TABLE \"propiedades\" ADD CONSTRAINT \"propiedades_chk_2\" CHECK (\"tipo\" IN ('casa', 'apartamento', 'lote', 'oficina', 'local', 'bodega', 'finca', 'otro'))");

    }
    public function down(): void {
        Schema::dropIfExists('propiedades');
    }
};
