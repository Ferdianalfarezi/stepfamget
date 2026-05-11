<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_anggota', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->index();                 // index biasa, tanpa FK (nik di karyawans bukan unique key)
            $table->string('nama_keluarga', 100);
            $table->enum('hubungan', ['Istri', 'Suami', 'Anak']);
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->date('tanggal_lahir')->nullable();
            $table->unsignedSmallInteger('umur')->nullable();
            $table->string('ukuran_kaos', 10)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('alasan_tolak')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable(); // FK ke users.id
            $table->timestamps();

            // FK ke users tetap aman karena users.id adalah primary key
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_anggota');
    }
};