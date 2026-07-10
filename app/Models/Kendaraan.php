<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'kendaraans';

    protected $fillable = ['nik', 'nama_karyawan', 'plat_no', 'jenis_kendaraan', 'jenis_tiket'];

    // 0 = Regular, 1 = VIP, 2 = VVIP
    const TIKET_REGULAR = 0;
    const TIKET_VIP     = 1;
    const TIKET_VVIP    = 2;

    public static function tiketOptions()
    {
        return [
            self::TIKET_REGULAR => 'Regular',
            self::TIKET_VIP     => 'VIP',
            self::TIKET_VVIP    => 'VVIP',
        ];
    }

    public function getJenisTiketLabelAttribute()
    {
        return self::tiketOptions()[$this->jenis_tiket] ?? 'Regular';
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}