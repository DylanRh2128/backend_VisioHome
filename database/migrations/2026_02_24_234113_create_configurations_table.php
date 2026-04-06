<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('configurations');
        Schema::create('configurations', function (Blueprint $table) {
            $table->string('key', 255)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default configurations from SQL dump
        DB::table('configurations')->insert([
            ['key' => 'cita_precio_base', 'value' => '50000', 'created_at' => null, 'updated_at' => null],
            ['key' => 'precio_cita_principal', 'value' => '80000', 'created_at' => null, 'updated_at' => null],
            ['key' => 'precio_cita_secundaria', 'value' => '50000', 'created_at' => null, 'updated_at' => null],
            ['key' => 'precio_cita_terciaria', 'value' => '20000', 'created_at' => null, 'updated_at' => '2026-04-06 06:03:54'],
        ]);
    }
    public function down(): void {
        Schema::dropIfExists('configurations');
    }
};

