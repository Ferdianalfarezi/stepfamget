<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $fillable = [
        'nik_login', 'nik', 'nama', 'departemen',
        'jumlah_keluarga', 'jumlah_fasilitas', 'keterangan', 'status_kehadiran',
        'last_login_at', 'baju_confirmed_at', 'trans_confirmed_at',
    ];

    protected $casts = [
        'status_kehadiran' => 'integer',
        'last_login_at'      => 'datetime',
        'baju_confirmed_at'  => 'datetime',
        'trans_confirmed_at' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(DetailKaryawan::class, 'nik', 'nik');
    }

    public function isBajuConfirmedThisYear(): bool
    {
        return $this->baju_confirmed_at !== null
            && $this->baju_confirmed_at->year === now()->year;
    }

    public function isTransConfirmedThisYear(): bool
    {
        return $this->trans_confirmed_at !== null
            && $this->trans_confirmed_at->year === now()->year;
    }

    /**
     * Hitung ulang jumlah_fasilitas dari relasi details (umur > 1 tahun)
     * dan simpan ke kolom. Dipanggil dari Observer setiap details berubah,
     * atau manual saat backfill.
     */
    public function recalculateJumlahFasilitas(): void
    {
        $count = $this->details()->where('umur', '>', 1)->count();

        // updateQuietly biar gak trigger event Karyawan lain yang gak perlu
        $this->updateQuietly(['jumlah_fasilitas' => $count]);
    }
}