<?php

namespace App\Imports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class KaryawanImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithChunkReading
{
    public int $imported = 0;
    public int $skipped  = 0;

    public function model(array $row): ?Karyawan
    {
        // Kolom header dari file: NIK_Login, NIK, Nama, Departemen, Jumlah Keluarga, Keterangan, Status Kehadiran
        $nik = trim($row['nik'] ?? $row['nik_login'] ?? '');
        $nama = trim($row['nama'] ?? '');

        if (empty($nama)) {
            $this->skipped++;
            return null;
        }

        $nikLogin = trim($row['nik_login'] ?? '');
        $status   = strtolower(trim($row['status_kehadiran'] ?? 'ya'));

        $this->imported++;

        return new Karyawan([
            'nik_login'        => $nikLogin ?: null,
            'nik'              => $nik ?: null,
            'nama'             => $nama,
            'departemen'       => trim($row['departemen'] ?? ''),
            'jumlah_keluarga'  => intval($row['jumlah_keluarga'] ?? 0),
            'keterangan'       => trim($row['keterangan'] ?? 'Aktif'),
            'status_kehadiran' => in_array($status, ['ya', 'yes', '1', 'true']) ? 1 : 0,
        ]);
    }

    public function chunkSize(): int
    {
        return 200;
    }
}
