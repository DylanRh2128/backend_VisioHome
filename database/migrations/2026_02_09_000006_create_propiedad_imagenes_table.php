<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('propiedad_imagenes');
        Schema::create('propiedad_imagenes', function (Blueprint $table) {
            $table->bigIncrements('idImagen');
            $table->unsignedBigInteger('idPropiedad');
            $table->string('urlImagen', 500);
            $table->integer('orden')->default(1);
            $table->dateTime('creado_en')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('tipo', 50)->nullable();

            // Foreign Keys
            $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('propiedad_imagenes');
    }
};