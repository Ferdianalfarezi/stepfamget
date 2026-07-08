<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use App\Exports\BusExport;
use App\Models\Karyawan;
use App\Models\DetailKaryawan;
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

        // ── Filter status kursi (sudah / belum terisi) ──
        if ($request->filled('kursi_status')) {
            if ($request->kursi_status === 'filled') {
                $query->whereNotNull('kursi')->where('kursi', '!=', '');
            } elseif ($request->kursi_status === 'empty') {
                $query->where(function ($q) {
                    $q->whereNull('kursi')->orWhere('kursi', '');
                });
            }
        }

        $buses = $query->orderBy('nama_karyawan')->paginate(15)->withQueryString();

        // Ambil keluarga (DetailKaryawan) untuk nik yang tampil di halaman ini
        // exclude hubungan Karyawan/Karyawati karena itu record diri sendiri, bukan keluarga
        $niksHalaman = $buses->pluck('nik');
        $keluargaGrouped = \App\Models\DetailKaryawan::whereIn('nik', $niksHalaman)
            ->whereNotIn('hubungan', ['Karyawan', 'Karyawati'])
            ->orderBy('id')
            ->get()
            ->groupBy('nik');

        foreach ($buses as $b) {
            $b->keluarga = $keluargaGrouped->get($b->nik, collect());
        }

        // Total keseluruhan (bukan cuma yang di halaman ini) — dari data YANG UDAH DAFTAR naik bus
        $totalKaryawan = Bus::count();
        $nikSemua      = Bus::pluck('nik');
        $totalKeluarga = \App\Models\DetailKaryawan::whereIn('nik', $nikSemua)
            ->whereNotIn('hubungan', ['Karyawan', 'Karyawati'])
            ->count();

        $total     = $totalKaryawan + $totalKeluarga;
        $jumlahBus = (int) ceil($total / 54);

        // ── Estimasi bus jika FULL ──
        // Semua karyawan + semua keluarga mereka (bukan cuma yg daftar naik bus),
        // dikurangi karyawan (+ keluarganya) yang bawa kendaraan pribadi.
        $nikKendaraanPribadi = Kendaraan::distinct()->pluck('nik');

        $totalSemuaOrang = Karyawan::count()
            + \App\Models\DetailKaryawan::whereNotIn('hubungan', ['Karyawan', 'Karyawati'])->count();

        $totalOrangBawaPribadi = Karyawan::whereIn('nik', $nikKendaraanPribadi)->count()
            + \App\Models\DetailKaryawan::whereIn('nik', $nikKendaraanPribadi)
                ->whereNotIn('hubungan', ['Karyawan', 'Karyawati'])
                ->count();

        $estimasiNaikBusFull    = max($totalSemuaOrang - $totalOrangBawaPribadi, 0);
        $estimasiBusFull        = (int) ceil($estimasiNaikBusFull / 54);
        // Versi desimal (misal 32.4) buat ditampilin di summary card
        $estimasiBusFullDesimal = number_format($estimasiNaikBusFull / 54, 1);

        return view('buses.index', compact(
            'buses', 'total', 'jumlahBus', 'totalKaryawan', 'totalKeluarga',
            'estimasiNaikBusFull', 'estimasiBusFull', 'estimasiBusFullDesimal'
        ));
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

        // ── Filter jenis kendaraan (yang tadinya belum ada) ──
        if ($request->filled('jenis')) {
            $query->where('jenis_kendaraan', $request->jenis);
        }

        $kendaraans = $query->orderBy('nama_karyawan')->paginate(15)->withQueryString();

        // Total keseluruhan (tanpa filter jenis, biar summary tetap nunjukin semua)
        $total = Kendaraan::count();

        // ── Summary per jenis kendaraan ──
        $totalPerJenis = Kendaraan::selectRaw('jenis_kendaraan, count(*) as total')
            ->groupBy('jenis_kendaraan')
            ->pluck('total', 'jenis_kendaraan');

        return view('kendaraans.index', compact('kendaraans', 'total', 'totalPerJenis'));
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
            'debug'   => $import->debugLog,
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