<?php

namespace App\Imports;

use App\Models\Bus;
use App\Models\DetailKaryawan;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class BusKursiImport implements ToCollection, WithHeadingRow
{
    public int $updated  = 0;
    public int $skipped  = 0;
    public int $conflict = 0;
    public array $debugLog = [];

    protected array $seatMap = [];

    public function collection(Collection $rows)
    {
        $this->buildSeatMap();

        foreach ($rows as $row) {
            $nik      = str_pad((string)(int)($row['nik'] ?? 0), 4, '0', STR_PAD_LEFT);
            $nama     = strtolower(preg_replace('/\s+/', '', $row['nama'] ?? ''));
            $hubungan = strtolower(trim($row['hubungan'] ?? ''));
            $kursi    = trim($row['kursi'] ?? '');

            $log = [
                'nik'      => $nik,
                'nama'     => $row['nama'] ?? null,
                'hubungan' => $row['hubungan'] ?? '-',
                'kursi'    => $kursi,
                'status'   => null,
            ];

            if (!$nik || $nik === '0000' || !$kursi || $kursi === '-') {
                $log['status'] = 'SKIPPED_EMPTY';
                $this->debugLog[] = $log;
                $this->skipped++;
                continue;
            }

            $isKaryawan = $hubungan === '' || $hubungan === 'karyawan';

            if ($isKaryawan) {
                $type   = 'bus';
                $target = Bus::where('nik', $nik)->get()
                    ->first(fn($b) => strtolower(preg_replace('/\s+/', '', $b->nama_karyawan)) === $nama);
            } else {
                $type   = 'keluarga';
                $target = DetailKaryawan::where('nik', $nik)->get()
                    ->first(fn($k) => strtolower(preg_replace('/\s+/', '', $k->nama_keluarga)) === $nama);
            }

            if (!$target) {
                $log['status'] = 'SKIPPED_NO_MATCH';
                $this->debugLog[] = $log;
                $this->skipped++;
                continue;
            }

            $currentKursi = $type === 'bus' ? $target->kursi : $target->kursi_bus;

            if ($currentKursi === $kursi) {
                $log['status'] = 'NO_CHANGE';
                $this->debugLog[] = $log;
                continue;
            }

            if (isset($this->seatMap[$kursi])) {
                $owner  = $this->seatMap[$kursi];
                $isSelf = $owner['type'] === $type && $owner['id'] === $target->id;

                if (!$isSelf) {
                    $log['status'] = 'SKIPPED_CONFLICT';
                    $log['note']   = "Kursi {$kursi} sudah dipakai ({$owner['type']} #{$owner['id']}), kursi lama ({$currentKursi}) dipertahankan.";
                    $this->debugLog[] = $log;
                    $this->conflict++;
                    $this->skipped++;
                    continue;
                }
            }

            if ($type === 'bus') {
                $target->update(['kursi' => $kursi]);
            } else {
                $target->update(['kursi_bus' => $kursi]);
            }

            if ($currentKursi && ($this->seatMap[$currentKursi]['id'] ?? null) === $target->id
                && ($this->seatMap[$currentKursi]['type'] ?? null) === $type) {
                unset($this->seatMap[$currentKursi]);
            }
            $this->seatMap[$kursi] = ['type' => $type, 'id' => $target->id];

            $log['status'] = $isKaryawan ? 'UPDATED_KARYAWAN' : 'UPDATED_KELUARGA';
            $this->debugLog[] = $log;
            $this->updated++;
        }
    }

    protected function buildSeatMap(): void
    {
        Bus::whereNotNull('kursi')->where('kursi', '!=', '')
            ->get(['id', 'kursi'])
            ->each(function ($b) {
                $this->seatMap[$b->kursi] = ['type' => 'bus', 'id' => $b->id];
            });

        DetailKaryawan::whereNotNull('kursi_bus')->where('kursi_bus', '!=', '')
            ->get(['id', 'kursi_bus'])
            ->each(function ($k) {
                $this->seatMap[$k->kursi_bus] = ['type' => 'keluarga', 'id' => $k->id];
            });
    }
}