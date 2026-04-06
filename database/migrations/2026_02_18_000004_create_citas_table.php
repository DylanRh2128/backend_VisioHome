<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('citas')) {
            Schema::create('citas', function (Blueprint $table) {
                $table->id('idCita');
                $table->string('docUsuario', 20);
                $table->unsignedBigInteger('idPropiedad');
                $table->string('docAgente', 20)->nullable();
                $table->dateTime('fecha');
                $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'finalizada'])->default('pendiente');
                $table->string('canal', 50)->default('presencial');
                $table->text('notas')->nullable();
                
                // Foreign keys
                $table->foreign('docUsuario')->references('docUsuario')->on('usuarios')->onDelete('cascade');
                $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades')->onDelete('cascade');
                $table->foreign('docAgente')->references('docAgente')->on('agentes')->onDelete('set null');
                
                $table->timestamp('creado_en')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
