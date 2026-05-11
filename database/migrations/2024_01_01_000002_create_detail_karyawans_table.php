<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->nullable();
            $table->string('nama_keluarga');
            $table->string('jenis_kelamin')->nullable();
            $table->string('hubungan')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('umur')->default(0);
            $table->string('ukuran_kaos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_karyawans');
    }
};
