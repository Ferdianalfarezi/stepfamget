<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nik_login')->nullable();
            $table->string('nik')->nullable();
            $table->string('nama');
            $table->string('departemen')->nullable();
            $table->integer('jumlah_keluarga')->default(0);
            $table->string('keterangan')->nullable();
            $table->boolean('status_kehadiran')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
