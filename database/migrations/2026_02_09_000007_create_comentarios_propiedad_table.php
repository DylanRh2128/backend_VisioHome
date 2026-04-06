<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('comentarios_propiedad');
        Schema::create('comentarios_propiedad', function (Blueprint $table) {
            $table->bigIncrements('idComentario');
            $table->unsignedBigInteger('idPropiedad');
            $table->string('docUsuario', 36)->nullable();
            $table->text('comentario');
            $table->tinyInteger('puntuacion');
            $table->dateTime('fecha')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));

            // Foreign Keys
            $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades')->onDelete('cascade');
            $table->foreign('docUsuario')->references('docUsuario')->on('usuarios');
        });

        // Add check constraint
        DB::statement("ALTER TABLE comentarios_propiedad ADD CONSTRAINT comentarios_propiedad_chk_1 CHECK (puntuacion BETWEEN 1 AND 5)");
    }
    public function down(): void {
        Schema::dropIfExists('comentarios_propiedad');
    }
};
