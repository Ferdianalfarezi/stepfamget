<?php

namespace App\Imports;

use App\Models\Bus;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class BusKursiImport implements ToCollection, WithHeadingRow
{
    public int $updated  = 0;
    public int $skipped  = 0;
    public array $debugLog = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $nik   = str_pad((string)(int)($row['nik'] ?? 0), 4, '0', STR_PAD_LEFT);
            $nama  = strtolower(preg_replace('/\s+/', '', $row['nama'] ?? ''));
            $kursi = trim($row['kursi'] ?? '');

            $log = [
                'nik_raw'    => $row['nik'],
                'nik_clean'  => $nik,
                'nama_raw'   => $row['nama'],
                'nama_clean' => $nama,
                'kursi_raw'  => $row['kursi'],
                'kursi_clean'=> $kursi,
                'db_count'   => Bus::where('nik', $nik)->count(),
                'db_match'   => 'NO MATCH',
            ];

            if (!$nik || $nik === '0000' || !$kursi) {
                $log['status'] = 'SKIPPED_EMPTY';
                $this->debugLog[] = $log;
                $this->skipped++;
                continue;
            }

            $bus = Bus::where('nik', $nik)->get()->first(function ($b) use ($nama) {
                return strtolower(preg_replace('/\s+/', '', $b->nama_karyawan)) === $nama;
            });

            if ($bus) {
                $log['db_match'] = $bus->nama_karyawan;
                $log['status']   = 'UPDATED';
                $bus->update(['kursi' => $kursi]);
                $this->updated++;
            } else {
                $log['status'] = 'SKIPPED_NO_MATCH';
                $this->skipped++;
            }

            $this->debugLog[] = $log;
        }
    }
}