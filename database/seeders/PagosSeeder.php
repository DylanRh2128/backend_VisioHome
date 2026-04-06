<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PagosSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'pagos';
        $path = database_path("sql/visiohome/visiohome_{$table}.sql");

        if (!File::exists($path)) return;

        try {
            $sql = File::get($path);
            $sql = str_replace('`', '"', $sql);

            preg_match_all('/INSERT INTO "pagos" VALUES \((.+?)\);/is', $sql, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $values) {
                    // Insertamos directamente. El orden del DatabaseSeeder garantiza que las FK existan.
                    DB::unprepared("INSERT INTO \"pagos\" VALUES ($values)");
                }
            }
        } catch (\Exception $e) {
            Log::error("Error en PagosSeeder: " . $e->getMessage());
        }
    }
}