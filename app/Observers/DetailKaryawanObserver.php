<?php

namespace App\Observers;

use App\Models\DetailKaryawan;
use App\Models\Karyawan;

class DetailKaryawanObserver
{
    public function saved(DetailKaryawan $detail): void
    {
        $this->recalc($detail->nik);
    }

    public function deleted(DetailKaryawan $detail): void
    {
        $this->recalc($detail->nik);
    }

    private function recalc(string $nik): void
    {
        $karyawan = Karyawan::where('nik', $nik)->first();
        $karyawan?->recalculateJumlahFasilitas();
    }
}