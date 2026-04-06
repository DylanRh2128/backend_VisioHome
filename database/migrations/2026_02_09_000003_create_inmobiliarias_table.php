<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inmobiliarias', function (Blueprint $table) {
            $table->string('nitInmobiliaria', 20)->primary();
            $table->string('nombre', 150);
            $table->string('correo', 180)->unique();
            $table->string('telefono', 30)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->text('objetivo')->nullable();
            $table->string('logo_light', 255)->nullable();
            $table->string('logo_dark', 255)->nullable();
        });
    }
    public function down(): void {
        Schema::dropIfExists('inmobiliarias');
    }
};