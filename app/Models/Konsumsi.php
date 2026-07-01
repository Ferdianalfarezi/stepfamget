<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Konsumsi extends Model
{
    protected $table = 'konsumsis';

    protected $fillable = [
        'nama',
        'satuan',
        'spare',
    ];

    protected $casts = [
        'spare' => 'integer',
    ];

    /**
     * Hitung qty realtime:
     * Jumlah detail_karyawans yang karyawannya hadir (status_kehadiran = 1)
     */
    public static function getQtyHadir(): int
    {
        return (int) DB::table('karyawans')
            ->where('status_kehadiran', 2)
            ->sum('jumlah_keluarga');
    }

    /**
     * total = qty (hadir) + spare
     */
    public function getTotalAttribute(): int
    {
        return self::getQtyHadir() + $this->spare;
    }
}