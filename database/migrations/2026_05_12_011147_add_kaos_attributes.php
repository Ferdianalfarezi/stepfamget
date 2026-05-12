<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_karyawans', function (Blueprint $table) {
            $table->enum('jenis_kaos', ['Dewasa', 'Anak'])
                  ->default('Dewasa')
                  ->after('ukuran_kaos')
                  ->comment('Kategori ukuran kaos: dewasa atau anak');

            $table->enum('lengan_kaos', ['Lengan Pendek', 'Lengan Panjang'])
                  ->nullable()
                  ->after('jenis_kaos')
                  ->comment('Tipe lengan kaos (umumnya diisi untuk kaos dewasa)');
        });
    }

    public function down(): void
    {
        Schema::table('detail_karyawans', function (Blueprint $table) {
            $table->dropColumn(['jenis_kaos', 'lengan_kaos']);
        });
    }
};