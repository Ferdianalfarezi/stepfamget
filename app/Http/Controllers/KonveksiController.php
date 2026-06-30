<?php

namespace App\Http\Controllers;

use App\Models\DetailKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KonveksiController extends Controller
{
    public function index(Request $request)
    {
        $year = now()->year;

        $confirmedNiks = Karyawan::whereNotNull('baju_confirmed_at')
            ->whereYear('baju_confirmed_at', $year)
            ->pluck('nik');

        $query = DetailKaryawan::with('karyawan')
            ->whereIn('nik', $confirmedNiks)
            ->orderBy('id');

        // Filter by NIK / nama karyawan / nama anggota
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_keluarga', 'like', "%$s%")
                    ->orWhere('nik', 'like', "%$s%")
                    ->orWhereHas('karyawan', fn($k) => $k->where('nama', 'like', "%$s%"));
            });
        }

        // Filter by hubungan
        if ($request->filled('hubungan')) {
            $query->where('hubungan', $request->hubungan);
        }

        // Filter by ukuran kaos
        if ($request->filled('ukuran')) {
            $query->where('ukuran_kaos', $request->ukuran);
        }

        // Filter by jenis kaos
        if ($request->filled('jenis')) {
            $query->where('jenis_kaos', $request->jenis);
        }

        // Filter by lengan
        if ($request->filled('lengan')) {
            $query->where('lengan_kaos', $request->lengan);
        }

        // Filter belum/sudah isi baju
        if ($request->filled('status_baju')) {
            if ($request->status_baju === 'belum') {
                $query->where(fn($q) => $q->whereNull('ukuran_kaos')->orWhere('ukuran_kaos', ''));
            } elseif ($request->status_baju === 'sudah') {
                $query->whereNotNull('ukuran_kaos')->where('ukuran_kaos', '!=', '');
            }
        }

        // Filter sudah/belum konfirmasi baju
        if ($request->filled('status_konfirmasi')) {
            if ($request->status_konfirmasi === 'sudah') {
                $query->whereHas('karyawan', fn($k) => $k
                    ->whereNotNull('baju_confirmed_at')
                    ->whereYear('baju_confirmed_at', $year)
                );
            } elseif ($request->status_konfirmasi === 'belum') {
                $query->whereHas('karyawan', fn($k) => $k->where(function ($q) use ($year) {
                    $q->whereNull('baju_confirmed_at')
                        ->orWhereYear('baju_confirmed_at', '!=', $year);
                }));
            }
        }

        $details = $query->paginate(20)->withQueryString();

        // Summary rekap ukuran — hanya dari karyawan yg sudah konfirmasi
        $rekap = DetailKaryawan::whereIn('nik', $confirmedNiks)
            ->selectRaw('ukuran_kaos, jenis_kaos, lengan_kaos, COUNT(*) as total')
            ->whereNotNull('ukuran_kaos')
            ->where('ukuran_kaos', '!=', '')
            ->groupBy('ukuran_kaos', 'jenis_kaos', 'lengan_kaos')
            ->orderByRaw("FIELD(ukuran_kaos,'XS','S','M','L','XL','XXL','XXXL')")
            ->get();

        $totalKonfirmasi = Karyawan::whereNotNull('baju_confirmed_at')
            ->whereYear('baju_confirmed_at', $year)
            ->count();

        $totalBelumKonfirmasi = Karyawan::where(function ($q) use ($year) {
            $q->whereNull('baju_confirmed_at')
                ->orWhereYear('baju_confirmed_at', '!=', $year);
        })->count();

        $totalBelum = DetailKaryawan::whereIn('nik', $confirmedNiks)
            ->where(fn($q) => $q->whereNull('ukuran_kaos')->orWhere('ukuran_kaos', ''))
            ->count();

        $totalSudah = DetailKaryawan::whereIn('nik', $confirmedNiks)
            ->whereNotNull('ukuran_kaos')
            ->where('ukuran_kaos', '!=', '')
            ->count();

        $totalScanned   = DetailKaryawan::whereIn('nik', $confirmedNiks)->where('is_scanned', 1)->count();
        $totalUnscanned = DetailKaryawan::whereIn('nik', $confirmedNiks)->where('is_scanned', 0)->count();

        return view('konveksis.index', compact(
            'details', 'rekap', 'totalBelum', 'totalSudah',
            'totalScanned', 'totalUnscanned',
            'totalKonfirmasi', 'totalBelumKonfirmasi'
        ));
    }

    // Scan NIK
    public function scan(Request $request)
    {
        $request->validate(['nik' => 'required|string']);
        $nik = trim($request->nik);

        $year = now()->year;
        $karyawan = Karyawan::where('nik', $nik)
            ->whereNotNull('baju_confirmed_at')
            ->whereYear('baju_confirmed_at', $year)
            ->first();

        if (!$karyawan) {
            return response()->json([
                'found'   => false,
                'message' => 'NIK tidak ditemukan atau belum konfirmasi baju.',
            ], 404);
        }

        $count = DetailKaryawan::where('nik', $nik)->count();

        if ($count === 0) {
            return response()->json(['found' => false, 'message' => 'NIK tidak ditemukan.'], 404);
        }

        DetailKaryawan::where('nik', $nik)->update(['is_scanned' => 1]);

        return response()->json([
            'found'   => true,
            'nik'     => $nik,
            'nama'    => $karyawan->nama ?? '-',
            'count'   => $count,
            'message' => "{$count} anggota berhasil ditandai.",
        ]);
    }

    // Reset semua scan
    public function resetScan()
    {
        DetailKaryawan::query()->update(['is_scanned' => 0]);
        return response()->json(['message' => 'Semua scan direset.']);
    }

    // Export
    public function export(Request $request)
    {
        $filename = 'data-konveksi-' . now()->format('Ymd-His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\KonveksiExport($request), $filename
        );
    }

    // Print slip baju — single karyawan by NIK
    public function print(Request $request)
    {
        $request->validate(['nik' => 'required|string']);

        $karyawan = Karyawan::with(['details' => fn($q) => $q->orderBy('id')])
            ->where('nik', $request->nik)
            ->firstOrFail();

        return view('konveksis.print', compact('karyawan'));
    }
}