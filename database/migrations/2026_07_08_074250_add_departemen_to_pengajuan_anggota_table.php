<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_anggota', function (Blueprint $table) {
            $table->string('departemen', 100)->nullable()->after('nik');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_anggota', function (Blueprint $table) {
            $table->dropColumn('departemen');
        });
    }
};