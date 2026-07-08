<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('detail_karyawans', function (Blueprint $table) {
        $table->string('kursi_bus')->nullable()->after('lengan_kaos');
    });
}

public function down()
{
    Schema::table('detail_karyawans', function (Blueprint $table) {
        $table->dropColumn('kursi_bus');
    });
}
};
