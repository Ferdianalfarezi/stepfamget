<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan_hadiah', function (Blueprint $table) {
            $table->id();
            $table->string('barang', 200);
            $table->string('nik_pemenang', 50)->nullable();   // FK ke karyawan.nik
            $table->string('nama_pemenang', 150)->nullable();
            $table->string('qr_code', 100)->nullable()->unique(); // generate saat pilih pemenang
            $table->enum('status', ['belum_ada_pemenang', 'siap_diambil', 'sudah_diambil'])
                  ->default('belum_ada_pemenang');
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan_hadiah');
    }
};