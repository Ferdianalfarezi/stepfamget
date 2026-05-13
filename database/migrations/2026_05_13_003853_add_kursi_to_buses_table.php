<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // php artisan make:migration add_kursi_to_buses_table
public function up()
{
    Schema::table('buses', function (Blueprint $table) {
        $table->string('kursi', 10)->nullable()->after('nama_karyawan');
    });
}
public function down()
{
    Schema::table('buses', function (Blueprint $table) {
        $table->dropColumn('kursi');
    });
}
};
