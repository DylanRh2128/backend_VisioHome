<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            if (!Schema::hasColumn('citas', 'idDisponibilidad')) {
                $table->unsignedBigInteger('idDisponibilidad')->nullable()->after('docAgente');
            }
            if (!Schema::hasColumn('citas', 'precio')) {
                $table->decimal('precio', 15, 2)->nullable()->after('fecha');
            }
        });

        Schema::table('pagos', function (Blueprint $table) {
            if (!Schema::hasColumn('pagos', 'external_reference')) {
                $table->string('external_reference', 80)->unique()->nullable()->after('referencia');
            }
            if (!Schema::hasColumn('pagos', 'mp_preference_id')) {
                $table->string('mp_preference_id', 100)->nullable()->after('external_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn(['idDisponibilidad', 'precio']);
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['external_reference', 'mp_preference_id']);
        });
    }
};
