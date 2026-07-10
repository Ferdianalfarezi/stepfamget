<?php

namespace App\Exports;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KaryawanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;
    protected $type; // 'simple' | 'full'
    protected $no = 0;

    public function __construct(Request $request, string $type = 'full')
    {
        $this->request = $request;
        $this->type    = $type;
    }

    public function query()
    {
        $q = Karyawan::query();

        if ($this->type === 'full') {
            $q->with('details');
        }

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $q->where(function ($q2) use ($search) {
                $q2->where('nama', 'like', "%$search%")
                   ->orWhere('nik', 'like', "%$search%")
                   ->orWhere('departemen', 'like', "%$search%");
            });
        }

        if ($this->request->filled('departemen')) {
            $q->where('departemen', $this->request->departemen);
        }

        if ($this->request->filled('keterangan')) {
            $q->where('keterangan', $this->request->keterangan);
        }

        // ── Filter Baju ──
        if ($this->request->filled('baju')) {
            $year = now()->year;
            if ($this->request->baju === 'confirmed') {
                $q->whereNotNull('baju_confirmed_at')
                  ->whereYear('baju_confirmed_at', $year);
            } elseif ($this->request->baju === 'belum') {
                $q->where(function ($q2) use ($year) {
                    $q2->whereNull('baju_confirmed_at')
                       ->orWhereYear('baju_confirmed_at', '!=', $year);
                });
            }
        }

        // ── Filter Trans ──
        if ($this->request->filled('trans')) {
            $year = now()->year;
            if ($this->request->trans === 'confirmed') {
                $q->whereNotNull('trans_confirmed_at')
                  ->whereYear('trans_confirmed_at', $year);
            } elseif ($this->request->trans === 'belum') {
                $q->where(function ($q2) use ($year) {
                    $q2->whereNull('trans_confirmed_at')
                       ->orWhereYear('trans_confirmed_at', '!=', $year);
                });
            }
        }

        // ── Filter Hubungan (Karyawan / Karyawati) ──
        if ($this->request->filled('hubungan')) {
            $q->whereHas('details', fn ($q2) =>
                $q2->where('hubungan', $this->request->hubungan)
            );
        }

        // ── Filter Hadir ──
        if ($this->request->filled('hadir') || $this->request->hadir === '0') {
            $q->where('status_kehadiran', $this->request->hadir);
        }

        return $q->orderBy('nama');
    }

    public function headings(): array
    {
        $base = [
            'No',
            'NIK',
            'NIK Login',
            'Nama Karyawan',
            'Departemen',
            'Jumlah Keluarga',
            'Usia diatas 1 Tahun',
            'Status',
            'Hadir',
        ];

        if ($this->type === 'simple') {
            return $base;
        }

        // full → tambah kolom detail keluarga
        return array_merge($base, [
            'Nama Anggota',
            'Hubungan',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Umur',
            'Ukuran Kaos',
            'Jenis Kaos',
            'Lengan Kaos',
        ]);
    }

    public function map($k): array
    {
        $this->no++;

        $baseRow = [
            $this->no,
            $k->nik,
            $k->nik_login ?? '-',
            $k->nama,
            $k->departemen,
            $k->jumlah_keluarga,
            $k->jumlah_fasilitas,
            $k->keterangan,
            $k->status_kehadiran ? 'Ya' : 'Tidak',
        ];

        // ── MODE SIMPLE: 1 baris per karyawan, tanpa detail keluarga ──
        if ($this->type === 'simple') {
            return $baseRow;
        }

        // ── MODE FULL: 1 baris per anggota keluarga (baris pertama bawa data karyawan) ──
        $rows    = [];
        $details = $k->details;

        if ($details->isEmpty()) {
            $rows[] = array_merge($baseRow, ['', '', '', '', '', '', '', '']);
        } else {
            foreach ($details as $idx => $d) {
                $rows[] = [
                    $idx === 0 ? $this->no          : '',
                    $idx === 0 ? $k->nik             : '',
                    $idx === 0 ? ($k->nik_login ?? '-') : '',
                    $idx === 0 ? $k->nama            : '',
                    $idx === 0 ? $k->departemen      : '',
                    $idx === 0 ? $k->jumlah_keluarga : '',
                    $idx === 0 ? $k->jumlah_fasilitas : '',
                    $idx === 0 ? $k->keterangan      : '',
                    $idx === 0 ? ($k->status_kehadiran ? 'Ya' : 'Tidak') : '',
                    $d->nama_keluarga,
                    $d->hubungan,
                    $d->jenis_kelamin,
                    $d->tanggal_lahir ? $d->tanggal_lahir->format('d/m/Y') : '-',
                    $d->umur ?? '-',
                    $d->ukuran_kaos ?? '-',
                    $d->jenis_kaos ?? '-',
                    $d->lengan_kaos ?? '-',
                ];
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0B4614']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ]);

        if ($lastRow > 1) {
            $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);
        }

        $sheet->freezePane('A2');

        return [];
    }
}