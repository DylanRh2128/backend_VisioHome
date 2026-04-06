<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('pagos');
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('idPago');
            $table->string('docUsuario', 36)->nullable();
            $table->unsignedBigInteger('idPropiedad');
            $table->unsignedBigInteger('idCita')->nullable();
            $table->decimal('monto', 15, 2);
            $table->string('metodoPago', 20);
            $table->string('estado', 20);
            $table->string('referencia', 80)->unique()->nullable();
            $table->dateTime('fecha')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('mp_payment_id', 100)->nullable();
            $table->string('mp_status', 50)->nullable();
            $table->string('external_reference', 100)->nullable();
            $table->string('mp_preference_id', 100)->nullable();

            // Foreign Keys
            $table->foreign('idCita')->references('idCita')->on('citas');
            $table->foreign('idPropiedad')->references('idPropiedad')->on('propiedades');
            $table->foreign('docUsuario')->references('docUsuario')->on('usuarios');
        });

        // Add check constraints exactly as in SQL dump
        DB::statement("ALTER TABLE \"pagos\" ADD CONSTRAINT \"pagos_chk_1\" CHECK (\"metodoPago\" IN ('tarjeta', 'transferencia', 'efectivo', 'paypal', 'otro', 'mercadopago'))");
        DB::statement("ALTER TABLE \"pagos\" ADD CONSTRAINT \"pagos_chk_2\" CHECK (\"estado\" IN ('pendiente', 'aprobado', 'rechazado', 'reembolsado'))");

    }
    public function down(): void {
        Schema::dropIfExists('pagos');
    }
};
