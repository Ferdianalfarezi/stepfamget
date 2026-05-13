<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KetuaBus extends Model
{
    protected $table    = 'ketua_bus';
    protected $fillable = ['kode_bus', 'nik', 'no_telp'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function penumpang()
    {
        // Semua Bus record yang kursinya diawali kode ini
        return $this->hasMany(Bus::class, 'kursi', 'kode_bus')
                    ->whereRaw("kursi LIKE CONCAT(kode_bus, '-%')");
    }
}