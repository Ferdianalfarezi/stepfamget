<?php

namespace App\Imports;

use App\Models\DetailKaryawan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class DetailKaryawanImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithChunkReading
{
    public int $imported = 0;
    public int $skipped  = 0;

    public function model(array $row): ?DetailKaryawan
    {
        // Kolom: NIK, Nama Karyawan, Departemen, Nama Keluarga, Jenis Kelamin, Hubungan, Tanggal Lahir, Umur, Ukuran Kaos
        $namaKeluarga = trim($row['nama_keluarga'] ?? '');

        if (empty($namaKeluarga)) {
            $this->skipped++;
            return null;
        }

        $tanggalLahir = null;
        $rawTgl = trim($row['tanggal_lahir'] ?? '');
        if (!empty($rawTgl)) {
            try {
                // Handle dd/mm/yyyy format
                if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $rawTgl)) {
                    $tanggalLahir = Carbon::createFromFormat('d/m/Y', $rawTgl)->format('Y-m-d');
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawTgl)) {
                    $tanggalLahir = $rawTgl;
                } else {
                    $tanggalLahir = Carbon::parse($rawTgl)->format('Y-m-d');
                }
            } catch (\Exception $e) {
                $tanggalLahir = null;
            }
        }

        // Handle Excel serial date number
        if (is_numeric($rawTgl) && strlen($rawTgl) < 10) {
            try {
                $tanggalLahir = Carbon::createFromTimestamp(($rawTgl - 25569) * 86400)->format('Y-m-d');
            } catch (\Exception $e) {
                $tanggalLahir = null;
            }
        }

        $umurKey = 'umur_per_30_aug_2025';
        $umur = intval($row[$umurKey] ?? $row['umur'] ?? 0);

        $this->imported++;

        return new DetailKaryawan([
            'nik'           => trim($row['nik'] ?? ''),
            'nama_keluarga' => $namaKeluarga,
            'jenis_kelamin' => trim($row['jenis_kelamin'] ?? ''),
            'hubungan'      => trim($row['hubungan'] ?? ''),
            'tanggal_lahir' => $tanggalLahir,
            'umur'          => $umur,
            'ukuran_kaos'   => trim($row['ukuran_kaos'] ?? ''),
        ]);
    }

    public function chunkSize(): int
    {
        return 200;
    }
}
