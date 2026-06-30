<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KaryawanImport;

class ImportKaryawanExcel extends Command
{
    protected $signature = 'import:karyawan {file : Path to Excel file (relative to storage/app)}';

    protected $description = 'Hapus semua data karyawans & detail_karyawans, lalu import ulang dari Excel';

    public function handle()
    {
        $filePath = storage_path('app/' . $this->argument('file'));

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return 1;
        }

        if (!$this->confirm('Ini akan HAPUS SEMUA data karyawans & detail_karyawans lalu import ulang. Lanjut?', false)) {
            $this->info('Dibatalkan.');
            return 0;
        }

        $this->info('Menghapus data lama...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('detail_karyawans')->truncate();
        DB::table('karyawans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('Data lama berhasil dihapus.');

        $this->info('Mengimpor data dari Excel...');
        Excel::import(new KaryawanImport, $filePath);

        $karyawanCount  = DB::table('karyawans')->count();
        $detailCount    = DB::table('detail_karyawans')->count();

        $this->info("Import selesai!");
        $this->table(['Tabel', 'Jumlah Record'], [
            ['karyawans',        $karyawanCount],
            ['detail_karyawans', $detailCount],
        ]);

        return 0;
    }
}