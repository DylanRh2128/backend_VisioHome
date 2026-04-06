<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement("ALTER TABLE propiedades ADD COLUMN test_col INT");
    echo "Success";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
