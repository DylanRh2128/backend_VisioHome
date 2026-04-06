<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\DB::statement("
        CREATE TABLE IF NOT EXISTS favorites (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            docUsuario VARCHAR(255) NOT NULL,
            idPropiedad BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (docUsuario) REFERENCES usuarios(docUsuario) ON DELETE CASCADE,
            FOREIGN KEY (idPropiedad) REFERENCES propiedades(idPropiedad) ON DELETE CASCADE,
            UNIQUE KEY user_property_unique (docUsuario, idPropiedad)
        )
    ");
    
    echo "Table favorites created or already exists.\n";

    \Illuminate\Support\Facades\DB::table('migrations')->insert([
        'migration' => '2026_02_17_160000_create_favorites_table',
        'batch' => (\Illuminate\Support\Facades\DB::table('migrations')->max('batch') ?? 0) + 1
    ]);
    
    echo "Migration record inserted.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
