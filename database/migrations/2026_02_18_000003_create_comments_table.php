<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->string('docUsuario', 20);
                $table->unsignedBigInteger('idPropiedad');
                $table->text('comentario');
                $table->tinyInteger('puntuacion')->default(5)->comment('1-5 estrellas');
                $table->foreign('docUsuario')->references('docUsuario')->on('usuarios')->onDelete('cascade');
                $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
