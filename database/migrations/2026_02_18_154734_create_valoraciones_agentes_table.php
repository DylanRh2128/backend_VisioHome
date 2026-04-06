<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valoraciones_agentes', function (Blueprint $table) {
            $table->id('idValoracion');
            $table->string('docAgente', 36);
            $table->string('docUsuario', 36);
            $table->integer('puntuacion'); // 1-5
            $table->text('comentario')->nullable();
            $table->timestamps();

            $table->foreign('docAgente')->references('docAgente')->on('agentes')->onDelete('cascade');
            $table->foreign('docUsuario')->references('docUsuario')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valoraciones_agentes');
    }
};

