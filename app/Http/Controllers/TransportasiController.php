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

        $buses = $query->orderBy('nama_karyawan')->paginate(15)->withQueryString();
        $total = Bus::count();

        return view('buses.index', compact('buses', 'total'));
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
}