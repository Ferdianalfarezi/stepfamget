<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_karyawans', function (Blueprint $table) {
            $table->tinyInteger('is_scanned')->default(0)->after('lengan_kaos')
                  ->comment('0 = belum discan, 1 = sudah discan');
        });
    }

    public function down(): void
    {
        Schema::table('detail_karyawans', function (Blueprint $table) {
            $table->dropColumn('is_scanned');
        });
    }
};