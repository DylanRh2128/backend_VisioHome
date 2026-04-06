<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comentarios_propiedad', function (Blueprint $table) {
            $table->bigIncrements('idComentario');
            $table->bigInteger('idPropiedad');
            $table->string('docUsuario', 36)->nullable();
            $table->text('comentario');
            $table->tinyInteger('puntuacion');
            $table->dateTime('fecha')->useCurrent();
            $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades')->onDelete('cascade');
            $table->foreign('docUsuario')->references('docUsuario')->on('usuarios');
        });
    }
    public function down(): void {
        Schema::dropIfExists('comentarios_propiedad');
    }
};