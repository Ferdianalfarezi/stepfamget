<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $fillable = [
        'nik_login', 'nik', 'nama', 'departemen',
        'jumlah_keluarga', 'keterangan', 'status_kehadiran',
        'last_login_at', 'baju_confirmed_at', 'trans_confirmed_at',
    ];

    protected $casts = [
        'status_kehadiran' => 'integer', // was: 'boolean'
        'last_login_at'      => 'datetime',
        'baju_confirmed_at'  => 'datetime',
        'trans_confirmed_at' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(DetailKaryawan::class, 'nik', 'nik');
    }

    /**
     * Sudah konfirmasi baju di tahun berjalan?
     */
    public function isBajuConfirmedThisYear(): bool
    {
        return $this->baju_confirmed_at !== null
            && $this->baju_confirmed_at->year === now()->year;
    }

    /**
     * Sudah konfirmasi transportasi di tahun berjalan?
     */
    public function isTransConfirmedThisYear(): bool
    {
        return $this->trans_confirmed_at !== null
            && $this->trans_confirmed_at->year === now()->year;
    }
}