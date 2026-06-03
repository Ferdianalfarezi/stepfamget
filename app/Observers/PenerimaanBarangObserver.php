<?php

namespace App\Observers;

use App\Models\PenerimaanBarang;
use App\Models\PenerimaanHadiah;

class PenerimaanBarangObserver
{
    // Saat barang baru ditambahkan → buat hadiah baru
    public function created(PenerimaanBarang $barang): void
    {
        PenerimaanHadiah::create([
            'barang' => $barang->barang,
            'status' => 'belum_ada_pemenang',
        ]);
    }

    // Saat nama barang diupdate → sync ke hadiah
    public function updated(PenerimaanBarang $barang): void
    {
        if ($barang->wasChanged('barang')) {
            // cari hadiah yang namanya sama dengan nama barang LAMA
            PenerimaanHadiah::where('barang', $barang->getOriginal('barang'))
                ->whereNull('nik_pemenang') // jangan overwrite yang udah ada pemenang
                ->update(['barang' => $barang->barang]);
        }
    }

    // Saat barang dihapus → hapus juga hadiah yang belum ada pemenang
    public function deleted(PenerimaanBarang $barang): void
    {
        PenerimaanHadiah::where('barang', $barang->barang)
            ->whereNull('nik_pemenang') // yang udah ada pemenang dibiarkan
            ->delete();
    }
}