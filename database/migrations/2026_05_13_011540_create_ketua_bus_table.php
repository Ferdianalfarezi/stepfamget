<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('ketua_bus', function (Blueprint $table) {
        $table->id();
        $table->string('kode_bus', 10)->unique();
        $table->string('nik', 20);
        $table->string('no_telp', 20)->nullable();
        $table->timestamps();
        // hapus foreign key, pakai index biasa
        $table->index('nik');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ketua_bus');
    }
};
