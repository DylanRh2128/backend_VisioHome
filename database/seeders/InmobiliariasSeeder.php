<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class InmobiliariasSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'inmobiliarias';
        $path = database_path("sql/visiohome/visiohome_{$table}.sql");

        if (!File::exists($path)) {
            Log::error("Seeder Error: SQL file not found for {$table} at {$path}");
            return;
        }

        try {
            $sql = File::get($path);
            preg_match_all('/INSERT INTO `?'.$table.'`?.+?;/is', $sql, $matches);

            if (!empty($matches[0])) {
                foreach ($matches[0] as $insert) {
                    DB::unprepared($insert);
                }
            }
        } catch (\Exception $e) {
            Log::error("Seeder Exception: Failed to seed {$table}. " . $e->getMessage());
        }
    }
}
