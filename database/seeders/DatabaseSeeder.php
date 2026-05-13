<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user
        DB::table('users')->insertOrIgnore([
            'username' => 'fatah',
            'password' => Hash::make('senaru'),
            'nama'     => 'Ahmad Fatah',
            
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Run SQL seeder for karyawan data
        $this->call(KaryawanSeeder::class);
    }
}
