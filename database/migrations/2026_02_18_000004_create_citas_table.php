<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->bigIncrements('idCita');
            $table->string('docUsuario', 36);
            $table->unsignedBigInteger('idPropiedad');
            $table->string('docAgente', 36)->nullable();
            $table->unsignedBigInteger('idDisponibilidad')->nullable();
            $table->dateTime('fecha');
            $table->decimal('precio', 15, 2)->nullable();
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'finalizada'])->default('pendiente');
            $table->string('canal', 50)->default('presencial');
            $table->text('notas')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('docUsuario')->references('docUsuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades')->onDelete('cascade');
            $table->foreign('docAgente')->references('docAgente')->on('agentes')->onDelete('set null');
            $table->foreign('idDisponibilidad')->references('idDisponibilidad')->on('disponibilidades')->onDelete('set null');
        });


        // Foreign Key para disponibilidades (se crea en su propia migración o aquí si aseguramos orden)
        // Como 'disponibilidades' tiene timestamp 2026_02_23 y 'citas' 2026_02_18, 
        // no podemos referenciarla aquí si queremos que corra 'fresh'.
        // Solución: Dejar la columna y el usuario decidirá si añade el constraint después o simplemente usar el order.
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};

