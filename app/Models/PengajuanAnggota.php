<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanAnggota extends Model
{
    protected $table = 'pengajuan_anggota';

    protected $fillable = [
        'nik',
        'nama_keluarga',
        'hubungan',
        'jenis_kelamin',
        'tanggal_lahir',
        'umur',
        'ukuran_kaos',
        'status',
        'alasan_tolak',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'reviewed_at'   => 'datetime',
    ];

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            'pending'  => 'Sedang Ditinjau',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => '-',
        };
    }
}