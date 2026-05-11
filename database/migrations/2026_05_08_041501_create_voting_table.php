<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel kandidat tempat
        Schema::create('voting_tempat', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('lokasi', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('foto', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel votes
        Schema::create('voting_votes', function (Blueprint $table) {
            $table->id();
            $table->string('karyawan_nik', 20);
            $table->foreignId('voting_tempat_id')->constrained('voting_tempat')->cascadeOnDelete();
            $table->timestamps();

            // 1 karyawan hanya bisa vote 1x
            $table->unique('karyawan_nik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_votes');
        Schema::dropIfExists('voting_tempat');
    }
};