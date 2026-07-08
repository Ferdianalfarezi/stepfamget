<?php

namespace App\Exports;

use App\Models\Bus;
use App\Models\DetailKaryawan;
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
        $buses = Bus::orderBy('nama_karyawan')->get();

        $keluargaGrouped = DetailKaryawan::whereIn('nik', $buses->pluck('nik'))
            ->whereNotIn('hubungan', ['Karyawan', 'Karyawati'])
            ->orderBy('id')
            ->get()
            ->groupBy('nik');

        $result = collect();
        $no     = 0;

        foreach ($buses as $b) {
            $no++;
            $result->push([
                'No'             => $no,
                'NIK'            => $b->nik,
                'Nama'           => $b->nama_karyawan,
                'Hubungan'       => 'Karyawan',
                'Kursi'          => $b->kursi ?? '-',
                'Terdaftar Pada' => $b->created_at->format('d/m/Y H:i'),
            ]);

            foreach ($keluargaGrouped->get($b->nik, collect()) as $k) {
                $no++;
                $result->push([
                    'No'             => $no,
                    'NIK'            => $b->nik,
                    'Nama'           => $k->nama_keluarga,
                    'Hubungan'       => $k->hubungan,
                    'Kursi'          => $k->kursi_bus ?? '-',
                    'Terdaftar Pada' => '-',
                ]);
            }
        }

        return $result;
    }

    public function headings(): array
    {
        return ['No', 'NIK', 'Nama', 'Hubungan', 'Kursi', 'Terdaftar Pada'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}