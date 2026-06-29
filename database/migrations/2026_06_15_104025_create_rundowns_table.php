<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rundowns', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan');
            $table->time('mulai');
            $table->time('selesai');
            $table->string('pic')->nullable();
            $table->string('properti')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0); // for drag-sort later
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rundowns');
    }
};