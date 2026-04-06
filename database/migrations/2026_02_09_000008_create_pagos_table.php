<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('idPago');
            $table->string('docUsuario', 36)->nullable();
            $table->bigInteger('idPropiedad');
            $table->bigInteger('idCita')->nullable();
            $table->decimal('monto', 15, 2);
            $table->string('metodoPago', 20);
            $table->string('estado', 20);
            $table->string('referencia', 80)->nullable()->unique();
            $table->dateTime('fecha')->useCurrent();
            $table->string('mp_payment_id', 100)->nullable();
            $table->string('mp_status', 50)->nullable();
            $table->string('external_reference', 100)->nullable();
            $table->string('mp_preference_id', 100)->nullable();
            $table->foreign('docUsuario')->references('docUsuario')->on('usuarios');
            $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades');
        });
    }
    public function down(): void {
        Schema::dropIfExists('pagos');
    }
};