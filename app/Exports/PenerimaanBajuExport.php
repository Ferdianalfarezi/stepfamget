<?php

namespace App\Exports;

use App\Models\DetailKaryawan;
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

class PenerimaanBajuExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;
    protected $no = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = DetailKaryawan::with('karyawan')->orderBy('nik')->orderBy('id');

        if ($this->request->filled('search')) {
            $s = $this->request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_keluarga', 'like', "%$s%")
                  ->orWhere('nik', 'like', "%$s%")
                  ->orWhereHas('karyawan', fn($k) => $k->where('nama', 'like', "%$s%"));
            });
        }

        if ($this->request->filled('hubungan'))    $query->where('hubungan', $this->request->hubungan);
        if ($this->request->filled('ukuran'))      $query->where('ukuran_kaos', $this->request->ukuran);
        if ($this->request->filled('jenis'))       $query->where('jenis_kaos', $this->request->jenis);
        if ($this->request->filled('lengan'))      $query->where('lengan_kaos', $this->request->lengan);
        if ($this->request->filled('status_baju')) {
            if ($this->request->status_baju === 'belum') {
                $query->where('is_scanned_baju', 0);
            } elseif ($this->request->status_baju === 'sudah') {
                $query->where('is_scanned_baju', 1);
            }
        }

        return $query;
    }

    public function headings(): array
    {
        return ['No', 'NIK', 'Nama Karyawan', 'Departemen', 'Nama Anggota', 'Hubungan', 'Ukuran Baju', 'Jenis Kaos', 'Lengan Kaos', 'Status Terima'];
    }

    public function map($d): array
    {
        $this->no++;
        return [
            $this->no,
            $d->nik,
            $d->karyawan->nama ?? '-',
            $d->karyawan->departemen ?? '-',
            $d->nama_keluarga,
            $d->hubungan,
            $d->ukuran_kaos ?? '-',
            $d->jenis_kaos  ?? '-',
            $d->lengan_kaos ?? '-',
            $d->is_scanned_baju ? 'Sudah' : 'Belum',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0369A1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
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