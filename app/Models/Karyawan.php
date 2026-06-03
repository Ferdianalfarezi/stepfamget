<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $fillable = [
        'nik_login', 'nik', 'nama', 'departemen',
        'jumlah_keluarga', 'keterangan', 'status_kehadiran','last_login_at'

    ];

    protected $casts = [
        'status_kehadiran' => 'boolean',
        'last_login_at'    => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(DetailKaryawan::class, 'nik', 'nik');
    }
}
