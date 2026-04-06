<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    Schema::table('propiedades', function (Blueprint $table) {
        if (!Schema::hasColumn('propiedades', 'latitud')) {
            $table->decimal('latitud', 10, 8)->nullable();
        }
        if (!Schema::hasColumn('propiedades', 'longitud')) {
            $table->decimal('longitud', 11, 8)->nullable();
        }
        if (!Schema::hasColumn('propiedades', 'habitaciones')) {
            $table->integer('habitaciones')->default(1);
        }
        if (!Schema::hasColumn('propiedades', 'banos')) {
            $table->integer('banos')->default(1);
        }
        if (!Schema::hasColumn('propiedades', 'imagen_principal')) {
            $table->string('imagen_principal')->nullable();
        }
    });
    echo "Success migrating columns";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
