<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->string('docUsuario', 36)->primary();
            $table->string('nombre', 120);
            $table->string('correo', 180)->unique();
            $table->string('password', 255);
            $table->unsignedTinyInteger('idRol'); // References roles.idRol
            $table->string('rol', 20)->default('cliente');
            
            // Profile fields
            $table->string('telefono', 30)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('genero', 50)->default('prefiero_no_decirlo');
            
            // Agent-specific fields (within user table)
            $table->string('especialidad', 120)->nullable();
            $table->text('biografia')->nullable();
            $table->string('carrera', 150)->nullable();
            $table->integer('experiencia_anos')->default(0);
            $table->string('nitInmobiliaria', 50)->nullable();
            $table->string('cv_path', 255)->nullable();
            
            // Security & Auth
            $table->integer('intentosFallidos')->default(0);
            $table->dateTime('bloqueadoHasta')->nullable();
            $table->string('google_id', 100)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('reset_token', 100)->nullable();
            $table->dateTime('reset_token_expire')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            
            // Stats & Control
            $table->boolean('activo')->default(true);
            $table->integer('login_count')->default(0);
            
            // Custom timestamps (avoiding built-in to keep business names)
            $table->dateTime('creado_en')->useCurrent();
            $table->dateTime('actualizado_en')->nullable();

            // Foreign Keys
            $table->foreign('idRol')->references('idRol')->on('roles')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('usuarios');
    }
};