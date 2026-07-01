<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konsumsis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');          // e.g. "Makan Siang", "Gorengan"
            $table->string('satuan');        // e.g. "porsi", "pcs", "kotak"
            // qty tidak disimpan — dihitung realtime dari detail_karyawans
            $table->integer('spare')->default(0);
            // total = qty + spare → computed saat tampil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konsumsis');
    }
};