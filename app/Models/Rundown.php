<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rundown extends Model
{
    protected $fillable = [
        'kegiatan',
        'mulai',
        'selesai',
        'pic',
        'properti',
        'keterangan',
        'urutan',
    ];

    /**
     * Hitung durasi dalam format HH:MM
     */
    public function getDurasiAttribute(): string
    {
        $start  = \Carbon\Carbon::createFromFormat('H:i:s', $this->mulai);
        $end    = \Carbon\Carbon::createFromFormat('H:i:s', $this->selesai);
        $diff   = $start->diffInMinutes($end);
        $h      = intdiv($diff, 60);
        $m      = $diff % 60;
        return sprintf('%02d:%02d', $h, $m);
    }
}