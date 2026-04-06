<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('roles');
        Schema::create('roles', function (Blueprint $table) {
            $table->tinyIncrements('idRol');
            $table->string('nombre', 30)->unique();
        });

        // Dumping data from SQL as initial seeds
        DB::table('roles')->insert([
            ['idRol' => 1, 'nombre' => 'admin'],
            ['idRol' => 2, 'nombre' => 'cliente'],
            ['idRol' => 3, 'nombre' => 'agente'],
        ]);
    }
    public function down(): void {
        Schema::dropIfExists('roles');
    }
};