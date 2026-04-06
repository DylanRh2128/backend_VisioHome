<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('usuarios');
        Schema::create('usuarios', function (Blueprint $table) {
            $table->string('docUsuario', 36)->primary();
            $table->string('nombre', 120);
            $table->string('correo', 180)->unique();
            $table->string('telefono', 30)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('password', 255);
            $table->unsignedTinyInteger('idRol');
            $table->string('rol', 20)->default('cliente');
            $table->string('especialidad', 120)->nullable();
            $table->text('biografia')->nullable();
            $table->string('carrera', 150)->nullable();
            $table->integer('experiencia_anos')->default(0);
            $table->string('nitInmobiliaria', 50)->nullable();
            $table->dateTime('creado_en')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('actualizado_en')->nullable();
            $table->integer('intentosFallidos')->default(0);
            $table->dateTime('bloqueadoHasta')->nullable();
            $table->string('google_id', 100)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('reset_token', 100)->nullable();
            $table->dateTime('reset_token_expire')->nullable();
            $table->string('genero', 50)->default('prefiero_no_decirlo');
            $table->string('departamento', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('login_count')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('cv_path', 255)->nullable();
            
            // Foreign Keys
            $table->foreign('idRol')->references('idRol')->on('roles')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('usuarios');
    }
};
