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
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'genero')) {
                $table->enum('genero', ['masculino', 'femenino', 'prefiero_no_decirlo'])->nullable()->after('password');
            }
            if (!Schema::hasColumn('usuarios', 'departamento')) {
                $table->string('departamento', 100)->nullable()->after('genero');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['genero', 'departamento']);
        });
    }
};
