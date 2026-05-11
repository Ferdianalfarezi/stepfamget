<?php

namespace App\Exports;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KendaraanExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return Kendaraan::orderBy('nama_karyawan')
            ->get()
            ->map(fn($k, $i) => [
                'No'             => $i + 1,
                'NIK'            => $k->nik,
                'Nama Karyawan'  => $k->nama_karyawan,
                'Plat Nomor'     => $k->plat_no,
                'Terdaftar'      => $k->created_at->format('d/m/Y H:i'),
            ]);
    }

    public function headings(): array
    {
        return ['No', 'NIK', 'Nama Karyawan', 'Plat Nomor', 'Terdaftar Pada'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}