<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\DetailKaryawan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class KaryawanTambahanImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $importedKaryawan = 0;
    public int $importedDetail   = 0;
    public int $skipped          = 0;

    /** @var array<int,string> */
    public array $errors = [];

    public function collection(Collection $rows)
    {
        // ── Group baris berdasarkan NIK ──
        $grouped = $rows
            ->filter(fn($row) => !empty(trim((string) ($row['nik'] ?? ''))))
            ->groupBy(fn($row) => trim((string) $row['nik']));

        foreach ($grouped as $nik => $rowsForNik) {
            DB::beginTransaction();
            try {
                $first = $rowsForNik->first();

                $namaKaryawan = trim((string) ($first['nama_karyawan'] ?? ''));
                if ($nik === '' || $namaKaryawan === '') {
                    $this->skipped += $rowsForNik->count();
                    DB::rollBack();
                    continue;
                }

                $karyawan = Karyawan::updateOrCreate(
                    ['nik' => $nik],
                    [
                        'nik_login'        => trim((string) ($first['nik_login'] ?? '')) ?: $nik,
                        'nama'             => $namaKaryawan,
                        'departemen'       => trim((string) ($first['departemen'] ?? '')) ?: '-',
                        'keterangan'       => $this->mapKeterangan($first['status'] ?? null),
                        'status_kehadiran' => $this->mapHadir($first['hadir'] ?? null),
                        'jumlah_keluarga'  => $rowsForNik->count(),
                    ]
                );

                foreach ($rowsForNik as $row) {
                    $namaAnggota  = trim((string) ($row['nama_anggota'] ?? ''));
                    $hubungan     = trim((string) ($row['hubungan'] ?? ''));
                    $jenisKelamin = trim((string) ($row['jenis_kelamin'] ?? ''));

                    if ($namaAnggota === '' || $hubungan === '' || $jenisKelamin === '') {
                        $this->skipped++;
                        continue;
                    }

                    // ── jenis_kaos: NOT NULL di DB, default 'Dewasa' kalau kosong ──
                    $jenisKaos = trim((string) ($row['jenis_kaos'] ?? ''));
                    $jenisKaos = in_array($jenisKaos, ['Dewasa', 'Anak']) ? $jenisKaos : 'Dewasa';

                    // ── ukuran_kaos: nullable, bersihkan placeholder "-" dkk ──
                    $ukuranKaos = $this->cleanValue($row['ukuran_kaos'] ?? null);

                    // ── lengan_kaos: enum nullable, validasi ketat + bersihkan placeholder ──
                    $lenganKaos = $this->cleanValue($row['lengan_kaos'] ?? null);
                    $lenganKaos = in_array($lenganKaos, ['Lengan Pendek', 'Lengan Panjang']) ? $lenganKaos : null;
                    if ($jenisKaos === 'Anak') {
                        $lenganKaos = null;
                    }

                    DetailKaryawan::updateOrCreate(
                        [
                            'nik'           => $nik,
                            'nama_keluarga' => $namaAnggota,
                        ],
                        [
                            'hubungan'      => $this->mapHubungan($hubungan),
                            'jenis_kelamin' => $this->mapJenisKelamin($jenisKelamin),
                            'tanggal_lahir' => $this->parseTanggalLahir($row['tanggal_lahir'] ?? null),
                            'umur'          => is_numeric($row['umur'] ?? null) ? (int) $row['umur'] : 0,
                            'ukuran_kaos'   => $ukuranKaos,
                            'jenis_kaos'    => $jenisKaos,
                            'lengan_kaos'   => $lenganKaos,
                        ]
                    );

                    $this->importedDetail++;
                }

                $this->importedKaryawan++;
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->skipped += $rowsForNik->count();
                $this->errors[] = "NIK {$nik}: " . $e->getMessage();
            }
        }
    }

    // ─────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────
    private function mapKeterangan(?string $status): string
    {
        $status = strtolower(trim((string) $status));
        return str_contains($status, 'non') ? 'Non-Aktif' : 'Aktif';
    }

    private function mapHadir(?string $hadir): int
    {
        $hadir = strtolower(trim((string) $hadir));

        if (str_contains($hadir, 'tidak')) return 1; // Tidak Hadir
        if ($hadir === 'hadir')             return 2; // Hadir
        return 0; // Belum Ditentukan
    }

    private function mapHubungan(string $hubungan): string
    {
        $valid = ['Karyawan', 'Karyawati', 'Istri', 'Suami', 'Anak', 'Saudara'];
        foreach ($valid as $v) {
            if (strcasecmp($v, $hubungan) === 0) return $v;
        }
        return 'Saudara'; // fallback aman
    }

    private function mapJenisKelamin(string $jk): string
    {
        $jk = strtolower(trim($jk));
        return str_starts_with($jk, 'p') ? 'Perempuan' : 'Laki-laki';
    }

    /**
     * Bersihkan value excel dari placeholder kosong seperti "-", "–", "—", "N/A".
     */
    private function cleanValue($value): ?string
    {
        $value   = trim((string) $value);
        $empties = ['-', '–', '—', 'n/a', 'na', '#n/a', ''];

        return in_array(strtolower($value), $empties, true) ? null : $value;
    }

    private function parseTanggalLahir($value): ?string
    {
        if (empty($value)) return null;

        // Excel numeric date serial
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        $value = trim((string) $value);
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }
}