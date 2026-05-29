<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/xxxx_add_berlaku_hingga_to_guest_menus_table.php
public function up(): void
{
    Schema::table('guest_menus', function (Blueprint $table) {
        $table->dateTime('berlaku_hingga')->nullable()->after('is_active');
    });
}

public function down(): void
{
    Schema::table('guest_menus', function (Blueprint $table) {
        $table->dropColumn('berlaku_hingga');
    });
}
};
