<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('citas');
        Schema::create('citas', function (Blueprint $table) {
            $table->bigIncrements('idCita');
            $table->unsignedBigInteger('idPropiedad');
            $table->string('docUsuario', 36)->nullable();
            $table->string('docAgente', 36)->nullable();
            $table->dateTime('fecha');
            $table->string('estado', 20);
            $table->string('canal', 20);
            $table->text('notas')->nullable();
            $table->dateTime('creado_en')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('idDisponibilidad')->nullable();
            $table->decimal('precio', 15, 2)->nullable();

            // Foreign Keys
            $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades');
            $table->foreign('docUsuario')->references('docUsuario')->on('usuarios');
        });

        // Add check constraints exactly as in SQL dump
        DB::statement("ALTER TABLE citas ADD CONSTRAINT citas_chk_1 CHECK (estado IN ('pendiente', 'confirmada', 'realizada', 'cancelada', 'no_asistio'))");
        DB::statement("ALTER TABLE citas ADD CONSTRAINT citas_chk_2 CHECK (canal IN ('presencial', 'virtual'))");
    }
    public function down(): void {
        Schema::dropIfExists('citas');
    }
};


