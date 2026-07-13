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
        'qty_source', 
    ];

    protected $casts = [
        'spare' => 'integer',
    ];

    /**
     * Hitung qty realtime:
     * Jumlah anggota (termasuk keluarga) dari karyawan yang statusnya HADIR (status_kehadiran = 2)
     */
    public static function getQtyHadir(): int
    {
        return (int) DB::table('karyawans')
            ->where('status_kehadiran', 2)
            ->sum('jumlah_keluarga');
    }

    /**
     * Hitung qty jika SEMUA karyawan (apapun status_kehadiran-nya) dianggap hadir semua.
     * Dipakai untuk estimasi worst-case / persiapan konsumsi maksimal.
     */
    public static function getQtySemua(): int
    {
        return (int) DB::table('karyawans')->sum('jumlah_keluarga');
    }

    /**
     * total = qty (hadir) + spare
     */
    public function getTotalAttribute(): int
    {
        return self::getQtyHadir() + $this->spare;
    }

    /**
     * total jika semua hadir = qty (semua) + spare
     */
    public function getTotalSemuaAttribute(): int
    {
        return self::getQtySemua() + $this->spare;
    }
}