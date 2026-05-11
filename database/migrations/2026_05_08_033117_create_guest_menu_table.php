<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_menus', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();       // 'kehadiran', 'profil', dst
            $table->string('label', 100);              // 'Kehadiran', 'Profil', dst
            $table->string('icon', 100);               // font-awesome class
            $table->string('color', 20)->default('#3d7a47'); // warna card
            $table->string('bg_color', 20)->default('#e8f5e9');
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Seed default menus
        DB::table('guest_menus')->insert([
            ['key' => 'kehadiran',   'label' => 'Kehadiran',    'icon' => 'fa-calendar-check',  'color' => '#2e7d32', 'bg_color' => '#e8f5e9', 'is_active' => true,  'urutan' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'profil',      'label' => 'Profil Saya',  'icon' => 'fa-user-circle',     'color' => '#1565c0', 'bg_color' => '#e3f2fd', 'is_active' => true,  'urutan' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'keluarga',    'label' => 'Keluarga',     'icon' => 'fa-people-group',    'color' => '#6a1b9a', 'bg_color' => '#f3e5f5', 'is_active' => true,  'urutan' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pengumuman',  'label' => 'Pengumuman',   'icon' => 'fa-bullhorn',        'color' => '#e65100', 'bg_color' => '#fff3e0', 'is_active' => false, 'urutan' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'slip_gaji',   'label' => 'Slip Gaji',    'icon' => 'fa-file-invoice',    'color' => '#00695c', 'bg_color' => '#e0f2f1', 'is_active' => false, 'urutan' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'cuti',        'label' => 'Pengajuan Cuti','icon' => 'fa-umbrella-beach', 'color' => '#c62828', 'bg_color' => '#ffebee', 'is_active' => false, 'urutan' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_menus');
    }
};