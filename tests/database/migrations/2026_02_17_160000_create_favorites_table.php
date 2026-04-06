<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->string('docUsuario');
            $table->unsignedBigInteger('idPropiedad');
            $table->timestamps();

            $table->foreign('docUsuario')->references('docUsuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades')->onDelete('cascade');
            $table->unique(['docUsuario', 'idPropiedad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
