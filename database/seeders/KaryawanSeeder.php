<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $sql = file_get_contents(database_path('seeders/karyawan_data.sql'));
        // Split by ; and run each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                DB::statement($statement);
            }
        }
    }
}
