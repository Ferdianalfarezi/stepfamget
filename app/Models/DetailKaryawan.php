<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKaryawan extends Model
{
    protected $table = 'detail_karyawans';

    protected $fillable = [
        'nik', 'nama_keluarga', 'jenis_kelamin',
        'hubungan', 'tanggal_lahir', 'umur',
        'ukuran_kaos', 'jenis_kaos', 'lengan_kaos',
        'is_scanned', 'is_scanned_baju', 'scanned_baju_at',
    ];

    protected $casts = [
        'tanggal_lahir'   => 'date',
        'is_scanned'      => 'boolean',
        'is_scanned_baju' => 'boolean',
        'scanned_baju_at' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}