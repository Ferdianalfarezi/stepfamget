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
    protected $no = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $q = Karyawan::with('details');

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

        return $q->orderBy('nama');
    }

    public function headings(): array
    {
        return [
            'No',
            'NIK',
            'NIK Login',
            'Nama Karyawan',
            'Departemen',
            'Jumlah Keluarga',
            'Status',
            'Hadir',
            // ── detail keluarga ──
            'Nama Anggota',
            'Hubungan',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Umur',
            'Ukuran Kaos',
            'Jenis Kaos',
            'Lengan Kaos',
        ];
    }

    public function map($k): array
    {
        $this->no++;
        $rows = [];

        $details = $k->details;

        if ($details->isEmpty()) {
            // Karyawan tanpa anggota keluarga → 1 baris, kolom detail kosong
            $rows[] = [
                $this->no,
                $k->nik,
                $k->nik_login ?? '-',
                $k->nama,
                $k->departemen,
                $k->jumlah_keluarga,
                $k->keterangan,
                $k->status_kehadiran ? 'Ya' : 'Tidak',
                '', '', '', '', '', '', '', '',
            ];
        } else {
            foreach ($details as $idx => $d) {
                $rows[] = [
                    // Kolom karyawan hanya di baris pertama
                    $idx === 0 ? $this->no          : '',
                    $idx === 0 ? $k->nik             : '',
                    $idx === 0 ? ($k->nik_login ?? '-') : '',
                    $idx === 0 ? $k->nama            : '',
                    $idx === 0 ? $k->departemen      : '',
                    $idx === 0 ? $k->jumlah_keluarga : '',
                    $idx === 0 ? $k->keterangan      : '',
                    $idx === 0 ? ($k->status_kehadiran ? 'Ya' : 'Tidak') : '',
                    // Kolom detail
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

        // Header row
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0B4614']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
        ]);

        // Data rows — border tipis
        if ($lastRow > 1) {
            $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);
        }

        // Freeze header
        $sheet->freezePane('A2');

        return [];
    }
}