<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaanHadiah extends Model
{
    protected $table = 'penerimaan_hadiah';

    protected $fillable = [
        'barang',
        'nik_pemenang',
        'nama_pemenang',
        'qr_code',
        'status',
        'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    // ── Relasi ke Karyawan ──
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik_pemenang', 'nik');
    }

    // ── Label status ──
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'belum_ada_pemenang' => 'Belum Ada Pemenang',
            'siap_diambil'       => 'Siap Diambil',
            'sudah_diambil'      => 'Sudah Diambil',
            default              => '-',
        };
    }

    // ── Badge color ──
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'belum_ada_pemenang' => '#94a3b8',
            'siap_diambil'       => '#f59e0b',
            'sudah_diambil'      => '#22c55e',
            default              => '#94a3b8',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match ($this->status) {
            'belum_ada_pemenang' => '#f1f5f9',
            'siap_diambil'       => '#fffbeb',
            'sudah_diambil'      => '#f0fdf4',
            default              => '#f1f5f9',
        };
    }
}