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
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'nama'     => 'Administrator',
            'role'     => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Run SQL seeder for karyawan data
        $this->call(KaryawanSeeder::class);
    }
}
