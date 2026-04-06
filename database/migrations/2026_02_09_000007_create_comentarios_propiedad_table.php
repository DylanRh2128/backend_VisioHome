<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comentarios_propiedad', function (Blueprint $table) {
            $table->bigIncrements('idComentario');
            $table->unsignedBigInteger('idPropiedad');
            $table->string('docUsuario', 36);
            $table->text('comentario');
            $table->tinyInteger('puntuacion')->default(5);
            $table->timestamps();

            // Foreign Keys
            $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades')->onDelete('cascade');
            $table->foreign('docUsuario')->references('docUsuario')->on('usuarios')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('comentarios_propiedad');
    }
};