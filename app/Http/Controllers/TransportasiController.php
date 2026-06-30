<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use App\Exports\BusExport;
use App\Exports\KendaraanExport;
use Maatwebsite\Excel\Facades\Excel;

class TransportasiController extends Controller
{
    public function buses(Request $request)
    {
        $query = Bus::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%$search%")
                ->orWhere('nik', 'like', "%$search%");
            });
        }

        $buses     = $query->orderBy('nama_karyawan')->paginate(15)->withQueryString();
        $total     = Bus::count();
        $jumlahBus = (int) ceil($total / 54); // 1-54 = 1 bus, 55-108 = 2 bus, dst

        return view('buses.index', compact('buses', 'total', 'jumlahBus'));
    }

    public function kendaraans(Request $request)
    {
        $query = Kendaraan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%$search%")
                  ->orWhere('nik', 'like', "%$search%")
                  ->orWhere('plat_no', 'like', "%$search%");
            });
        }

        $kendaraans = $query->orderBy('nama_karyawan')->paginate(15)->withQueryString();
        $total      = Kendaraan::count();

        return view('kendaraans.index', compact('kendaraans', 'total'));
    }

    public function exportBuses(Request $request)
    {
        $filename = 'data-bus-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new BusExport($request), $filename);
    }

    public function exportKendaraans(Request $request)
    {
        $filename = 'data-kendaraan-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new KendaraanExport($request), $filename);
    }

    public function importKursi(Request $request)
{
    $request->validate([
        'file_excel' => 'required|file|mimes:xlsx,xls',
    ]);

    $import = new \App\Imports\BusKursiImport();
    Excel::import($import, $request->file('file_excel'));

    return response()->json([
        'updated' => $import->updated,
        'skipped' => $import->skipped,
        'debug'   => $import->debugLog, // tambah ini
        'message' => "{$import->updated} kursi berhasil diperbarui, {$import->skipped} dilewati.",
    ]);
}

// Taruh sementara di TransportasiController, akses via browser: /buses/debug-nik/1267
public function debugNik($nik)
{
    $buses = \App\Models\Bus::where('nik', $nik)->get(['id','nik','nama_karyawan','kursi']);
    return response()->json($buses);
}
}