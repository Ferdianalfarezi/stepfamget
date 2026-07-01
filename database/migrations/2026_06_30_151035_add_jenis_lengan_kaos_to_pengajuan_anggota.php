<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('pengajuan_anggota', function (Blueprint $table) {
        $table->string('jenis_kaos', 20)->nullable()->after('ukuran_kaos');
        $table->string('lengan_kaos', 30)->nullable()->after('jenis_kaos');
    });
}

public function down()
{
    Schema::table('pengajuan_anggota', function (Blueprint $table) {
        $table->dropColumn(['jenis_kaos', 'lengan_kaos']);
    });
}
};
