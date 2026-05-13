<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_karyawans', function (Blueprint $table) {
            $table->tinyInteger('is_scanned_baju')->default(0)->after('is_scanned')
                  ->comment('0 = belum terima baju, 1 = sudah terima baju');
        });
    }

    public function down(): void
    {
        Schema::table('detail_karyawans', function (Blueprint $table) {
            $table->dropColumn('is_scanned_baju');
        });
    }
};