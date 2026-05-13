<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_karyawans', function (Blueprint $table) {
            // Timestamp kapan baju diambil (null = belum diambil)
            $table->timestamp('scanned_baju_at')->nullable()->after('is_scanned_baju')
                  ->comment('Waktu pengambilan baju');
        });
    }

    public function down(): void
    {
        Schema::table('detail_karyawans', function (Blueprint $table) {
            $table->dropColumn('scanned_baju_at');
        });
    }
};