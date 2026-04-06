<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('roles', function (Blueprint $table) {
            $table->tinyIncrements('idRol');
            $table->string('nombre', 30)->unique();
        });
        DB::table('roles')->insert([
            ['nombre' => 'cliente'],
            ['nombre' => 'agente'],
            ['nombre' => 'admin'],
        ]);
    }
    public function down(): void {
        Schema::dropIfExists('roles');
    }
};