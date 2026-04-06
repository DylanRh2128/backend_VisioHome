<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('propiedad_imagenes', function (Blueprint $table) {
            $table->bigIncrements('idImagen');
            $table->unsignedBigInteger('idPropiedad');
            $table->foreign('idPropiedad')
                ->references('idPropiedad')
                ->on('propiedades')
                ->onDelete('cascade');
            $table->string('urlImagen', 500);
            $table->integer('orden')->default(1);
            $table->dateTime('creado_en')->useCurrent();
            $table->string('tipo', 50)->nullable();
        });
    }
    public function down(): void {
        Schema::dropIfExists('propiedad_imagenes');
    }
};