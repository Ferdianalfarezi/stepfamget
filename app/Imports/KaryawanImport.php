<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class KaryawanImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function chunkSize(): int
    {
        return 500;
    }

    public function collection(Collection $rows)
    {
        // State karyawan aktif saat ini
        $currentKaryawan = null;
        $now = now();

        $karyawanBatch  = [];
        $detailBatch    = [];

        foreach ($rows as $row) {
            $nik = trim($row['nik'] ?? '');

            // Baris karyawan = ada NIK-nya
            if (!empty($nik)) {
                // Flush batch karyawan sebelumnya kalau ada
                if (!empty($karyawanBatch)) {
                    DB::table('karyawans')->insert($karyawanBatch);
                    $karyawanBatch = [];
                }

                $currentKaryawan = (string) $nik;

                $karyawanBatch[] = [
                    'nik'              => (string) $nik,
                    'nik_login'        => trim($row['nik_login'] ?? $nik),
                    'nama'             => trim($row['nama_karyawan'] ?? ''),
                    'departemen'       => trim($row['departemen'] ?? ''),
                    'jumlah_keluarga'  => (int) ($row['jumlah_keluarga'] ?? 1),
                    'keterangan'       => trim($row['status'] ?? 'Aktif'),
                    'status_kehadiran' => $this->parseHadir($row['hadir'] ?? 'Tidak'),
                    'last_login_at'    => null,
                    'baju_confirmed_at'  => null,
                    'trans_confirmed_at' => null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            // Baris detail anggota keluarga = ada nama_anggota
            $namaAnggota = trim($row['nama_anggota'] ?? '');
            if (!empty($namaAnggota) && $currentKaryawan !== null) {
                $detailBatch[] = [
                    'nik'             => $currentKaryawan,
                    'nama_keluarga'   => $namaAnggota,
                    'jenis_kelamin'   => trim($row['jenis_kelamin'] ?? ''),
                    'hubungan'        => trim($row['hubungan'] ?? ''),
                    'tanggal_lahir'   => $this->parseDate($row['tanggal_lahir'] ?? null),
                    'umur'            => (int) ($row['umur'] ?? 0),
                    'ukuran_kaos'     => trim($row['ukuran_kaos'] ?? ''),
                    'jenis_kaos'      => trim($row['jenis_kaos'] ?? ''),
                    'lengan_kaos'     => $this->parseLengan($row['lengan_kaos'] ?? ''),
                    'is_scanned'      => false,
                    'is_scanned_baju' => false,
                    'scanned_baju_at' => null,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];

                // Insert detail per batch 200 baris
                if (count($detailBatch) >= 200) {
                    DB::table('detail_karyawans')->insert($detailBatch);
                    $detailBatch = [];
                }
            }
        }

        // Flush sisa
        if (!empty($karyawanBatch)) {
            DB::table('karyawans')->insert($karyawanBatch);
        }
        if (!empty($detailBatch)) {
            DB::table('detail_karyawans')->insert($detailBatch);
        }
    }

    // -------------------------------------------------------------------------

    private function parseDate($value): ?string
    {
        if (empty($value)) return null;

        // Format dari Excel: d/m/Y atau d-m-Y
        try {
            // Cek kalau numeric (Excel serial date)
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                    ->format('Y-m-d');
            }
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }

    private function parseHadir($value): int
    {
        $v = strtolower(trim($value ?? ''));
        return in_array($v, ['ya', 'yes', '1', 'hadir']) ? 1 : 0;
    }

    private function parseLengan($value): ?string
    {
        $v = trim($value ?? '');
        if ($v === '-' || $v === '') return null;
        return $v;
    }
}