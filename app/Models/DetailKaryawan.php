<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKaryawan extends Model
{
    protected $table = 'detail_karyawans';

    protected $fillable = [
        'nik', 'nama_keluarga', 'jenis_kelamin',
        'hubungan', 'tanggal_lahir', 'umur', 'ukuran_kaos'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}
