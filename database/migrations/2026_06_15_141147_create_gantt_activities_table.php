<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gantt_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity');
            $table->string('detail')->nullable();

            // Plan
            $table->unsignedTinyInteger('plan_start_month');   // 1–12
            $table->unsignedTinyInteger('plan_start_week');    // 1–4
            $table->unsignedTinyInteger('plan_end_month');     // 1–12
            $table->unsignedTinyInteger('plan_end_week');      // 1–4

            // Actual (opsional)
            $table->unsignedTinyInteger('actual_start_month')->nullable();
            $table->unsignedTinyInteger('actual_start_week')->nullable();
            $table->unsignedTinyInteger('actual_end_month')->nullable();
            $table->unsignedTinyInteger('actual_end_week')->nullable();

            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gantt_activities');
    }
};