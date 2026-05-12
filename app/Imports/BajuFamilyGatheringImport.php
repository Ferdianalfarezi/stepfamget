<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\DetailKaryawan;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BajuFamilyGatheringImport implements ToCollection, WithHeadingRow
{
    public int   $imported = 0;
    public int   $skipped  = 0;

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {

                $nik         = trim($row['nik'] ?? '');
                $namaAnggota = trim($row['nama_anggota'] ?? '');
                $hubungan    = trim($row['statushubungan'] ?? $row['status_hubungan'] ?? '');
                $ukuran      = strtoupper(trim($row['ukuran_baju'] ?? ''));
                $keterangan  = trim($row['keterangan'] ?? '');

                if (empty($nik) || empty($namaAnggota)) {
                    $this->skipped++;
                    continue;
                }

                // Upsert karyawan kalau belum ada
                Karyawan::firstOrCreate(
                    ['nik' => $nik],
                    [
                        'nik_login'        => $nik,
                        'nama'             => trim($row['nama_karyawan'] ?? ''),
                        'departemen'       => trim($row['departemen'] ?? ''),
                        'keterangan'       => 'Aktif',
                        'status_kehadiran' => false,
                        'jumlah_keluarga'  => 0,
                    ]
                );

                [$jenisKaos, $lenganKaos] = $this->parseKeterangan($keterangan);

                $existing = DetailKaryawan::where('nik', $nik)
                    ->where('nama_keluarga', $namaAnggota)
                    ->first();

                $payload = [
                    'nik'           => $nik,
                    'nama_keluarga' => $namaAnggota,
                    'hubungan'      => $this->normalizeHubungan($hubungan),
                    'jenis_kelamin' => $this->guessJenisKelamin($hubungan),
                    'tanggal_lahir' => null,
                    'umur'          => 0,
                    'ukuran_kaos'   => $ukuran ?: null,
                    'jenis_kaos'    => $jenisKaos,
                    'lengan_kaos'   => $lenganKaos,
                ];

                if ($existing) {
                    $existing->update([
                        'ukuran_kaos' => $payload['ukuran_kaos'],
                        'jenis_kaos'  => $payload['jenis_kaos'],
                        'lengan_kaos' => $payload['lengan_kaos'],
                    ]);
                } else {
                    DetailKaryawan::create($payload);
                }

                $this->imported++;
            }
        });

        // Sync ulang jumlah_keluarga
        DB::statement('
            UPDATE karyawans k
            SET k.jumlah_keluarga = (
                SELECT COUNT(*) FROM detail_karyawans d WHERE d.nik = k.nik
            )
        ');
    }

    // "Lengan Pendek"  → [Dewasa, Lengan Pendek]
    // "Lengan Panjang" → [Dewasa, Lengan Panjang]
    // "Dewasa"         → [Dewasa, null]
    // "Anak"           → [Anak,   null]
    private function parseKeterangan(string $ket): array
    {
        if (stripos($ket, 'Lengan Pendek') !== false)  return ['Dewasa', 'Lengan Pendek'];
        if (stripos($ket, 'Lengan Panjang') !== false) return ['Dewasa', 'Lengan Panjang'];
        if (stripos($ket, 'Anak') !== false)           return ['Anak', null];
        return ['Dewasa', null];
    }

    private function normalizeHubungan(string $h): string
    {
        return match (strtolower($h)) {
            'karyawati' => 'Karyawati',
            'istri'     => 'Istri',
            'suami'     => 'Suami',
            'anak'      => 'Anak',
            'saudara'   => 'Saudara',
            default     => 'Karyawan',
        };
    }

    private function guessJenisKelamin(string $h): string
    {
        return in_array(strtolower($h), ['karyawati', 'istri']) ? 'Perempuan' : 'Laki-laki';
    }
}