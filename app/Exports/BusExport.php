<?php

namespace App\Exports;

use App\Models\Bus;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BusExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return Bus::orderBy('nama_karyawan')
            ->get()
            ->map(fn($b, $i) => [
                'No'            => $i + 1,
                'NIK'           => $b->nik,
                'Nama Karyawan' => $b->nama_karyawan,
                'Terdaftar'     => $b->created_at->format('d/m/Y H:i'),
            ]);
    }

    public function headings(): array
    {
        return ['No', 'NIK', 'Nama Karyawan', 'Terdaftar Pada'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}