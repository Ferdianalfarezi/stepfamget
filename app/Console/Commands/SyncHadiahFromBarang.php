<?php

namespace App\Console\Commands;

use App\Models\PenerimaanBarang;
use App\Models\PenerimaanHadiah;
use Illuminate\Console\Command;

class SyncHadiahFromBarang extends Command
{
    protected $signature   = 'hadiah:sync';
    protected $description = 'Sync data penerimaan barang ke penerimaan hadiah';

    public function handle(): void
    {
        $barangs = PenerimaanBarang::all();
        $count   = 0;

        foreach ($barangs as $barang) {
            // Cek apakah sudah ada hadiah dengan nama barang yang sama
            $exists = PenerimaanHadiah::where('barang', $barang->barang)->exists();

            if (!$exists) {
                PenerimaanHadiah::create([
                    'barang' => $barang->barang,
                    'status' => 'belum_ada_pemenang',
                ]);
                $count++;
            }
        }

        $this->info("Sync selesai. {$count} hadiah berhasil ditambahkan.");
    }
}