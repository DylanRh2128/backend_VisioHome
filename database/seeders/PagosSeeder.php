<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class PagosSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'pagos';
        $path = database_path("sql/visiohome/visiohome_{$table}.sql");

        if (!File::exists($path)) {
            Log::error("Seeder Error: SQL file not found for {$table} at {$path}");
            return;
        }

        try {
            $sql = File::get($path);
            preg_match_all('/INSERT INTO `?'.$table.'`?.+?;/is', $sql, $matches);

            if (!empty($matches[0])) {
                DB::unprepared("SET session_replication_role = 'replica'");
                foreach ($matches[0] as $insert) {
                    $insert = str_replace('`', '"', $insert);
                    DB::unprepared($insert);
                }
                DB::unprepared("SET session_replication_role = 'origin'");
            }

        } catch (\Exception $e) {
            Log::error("Seeder Exception: Failed to seed {$table}. " . $e->getMessage());
        }
    }
}
