<?php

namespace App\Exports;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
        $q = Karyawan::query();

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
        return ['No', 'NIK', 'NIK Login', 'Nama Karyawan', 'Departemen', 'Jumlah Keluarga', 'Status', 'Hadir'];
    }

    public function map($k): array
    {
        $this->no++;
        return [
            $this->no,
            $k->nik,
            $k->nik_login ?? '-',
            $k->nama,
            $k->departemen,
            $k->jumlah_keluarga,
            $k->keterangan,
            $k->status_kehadiran ? 'Ya' : 'Tidak',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF3B82F6']],
                'alignment' => ['horizontal' => 'center'],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }
}