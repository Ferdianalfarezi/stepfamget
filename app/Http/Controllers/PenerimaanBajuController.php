<?php

namespace App\Http\Controllers;

use App\Models\DetailKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PenerimaanBajuController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = DetailKaryawan::with('karyawan')->orderBy('nik')->orderBy('id');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_keluarga', 'like', "%$s%")
                  ->orWhere('nik', 'like', "%$s%")
                  ->orWhereHas('karyawan', fn($k) => $k->where('nama', 'like', "%$s%")
                                                        ->orWhere('departemen', 'like', "%$s%"));
            });
        }

        if ($request->filled('departemen')) {
            $query->whereHas('karyawan', fn($k) => $k->where('departemen', $request->departemen));
        }

        if ($request->filled('hubungan')) {
            $query->where('hubungan', $request->hubungan);
        }

        if ($request->filled('ukuran')) {
            $query->where('ukuran_kaos', $request->ukuran);
        }

        if ($request->filled('status_terima')) {
            if ($request->status_terima === 'sudah') {
                $query->where('is_scanned_baju', 1);
            } elseif ($request->status_terima === 'belum') {
                $query->where('is_scanned_baju', 0);
            }
        }

        $details        = $query->paginate(20)->withQueryString();
        $totalScanned   = DetailKaryawan::where('is_scanned_baju', 1)->count();
        $totalUnscanned = DetailKaryawan::where('is_scanned_baju', 0)->count();
        $departemenList = Karyawan::select('departemen')->distinct()->orderBy('departemen')->pluck('departemen');

        return view('penerimaan_baju.index', compact(
            'details', 'totalScanned', 'totalUnscanned', 'departemenList'
        ));
    }

    // ── SCAN PER NIK ──────────────────────────────────────────────────────────
    // Scan 1 NIK → mark semua anggota keluarga NIK itu
    public function scan(Request $request)
    {
        $request->validate(['nik' => 'required|string']);
        $nik = trim($request->nik);

        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return response()->json(['found' => false, 'message' => 'NIK tidak ditemukan.'], 404);
        }

        $count = DetailKaryawan::where('nik', $nik)
            ->where('is_scanned_baju', 0)
            ->count();

        if ($count === 0) {
            return response()->json([
                'found'        => true,
                'already_done' => true,
                'nik'          => $nik,
                'nama'         => $karyawan->nama,
                'departemen'   => $karyawan->departemen,
                'message'      => 'Karyawan ini sudah mengambil baju.',
            ]);
        }

        DetailKaryawan::where('nik', $nik)
            ->where('is_scanned_baju', 0)
            ->update([
                'is_scanned_baju' => 1,
                'scanned_baju_at' => now(),
            ]);

        $total = DetailKaryawan::where('nik', $nik)->count();

        return response()->json([
            'found'        => true,
            'already_done' => false,
            'nik'          => $nik,
            'nama'         => $karyawan->nama,
            'departemen'   => $karyawan->departemen,
            'count'        => $count,
            'total'        => $total,
            'message'      => "{$count} anggota berhasil ditandai.",
        ]);
    }

    // ── SCAN BULK DEPARTEMEN ──────────────────────────────────────────────────
    // Mark semua karyawan di departemen yang sama
    public function scanDepartemen(Request $request)
    {
        $request->validate(['departemen' => 'required|string']);
        $dept = $request->departemen;

        $niks = Karyawan::where('departemen', $dept)->pluck('nik');

        if ($niks->isEmpty()) {
            return response()->json(['message' => 'Departemen tidak ditemukan.'], 404);
        }

        $count = DetailKaryawan::whereIn('nik', $niks)
            ->where('is_scanned_baju', 0)
            ->count();

        DetailKaryawan::whereIn('nik', $niks)
            ->where('is_scanned_baju', 0)
            ->update([
                'is_scanned_baju' => 1,
                'scanned_baju_at' => now(),
            ]);

        return response()->json([
            'departemen' => $dept,
            'count'      => $count,
            'message'    => "{$count} anggota dari departemen {$dept} ditandai.",
        ]);
    }

    // ── RESET PER NIK ─────────────────────────────────────────────────────────
    public function resetNik(Request $request)
    {
        $request->validate(['nik' => 'required|string']);
        $nik = trim($request->nik);

        $count = DetailKaryawan::where('nik', $nik)
            ->where('is_scanned_baju', 1)
            ->count();

        DetailKaryawan::where('nik', $nik)->update([
            'is_scanned_baju' => 0,
            'scanned_baju_at' => null,
        ]);

        $karyawan = Karyawan::where('nik', $nik)->first();

        return response()->json([
            'message' => "{$count} anggota NIK {$nik} ({$karyawan->nama}) direset.",
            'count'   => $count,
        ]);
    }

    // ── RESET SEMUA ───────────────────────────────────────────────────────────
    public function resetScan()
    {
        DetailKaryawan::query()->update([
            'is_scanned_baju' => 0,
            'scanned_baju_at' => null,
        ]);
        return response()->json(['message' => 'Semua penerimaan direset.']);
    }

    // ── PRINT PER DEPARTEMEN ──────────────────────────────────────────────────
    public function print(Request $request)
    {
        $request->validate(['departemen' => 'required|string']);
        $dept = $request->departemen;

        // Ambil semua karyawan di departemen (hanya karyawan/karyawati — bukan anggota keluarga)
        $karyawans = Karyawan::where('departemen', $dept)
            ->where('keterangan', 'Aktif')
            ->orderBy('nama')
            ->get();

        // Sudah ambil baju
        $sudah = $karyawans->filter(function ($k) {
            return DetailKaryawan::where('nik', $k->nik)
                ->whereIn('hubungan', ['Karyawan', 'Karyawati'])
                ->where('is_scanned_baju', 1)
                ->exists();
        })->values();

        // Belum ambil baju
        $belum = $karyawans->filter(function ($k) {
            return !DetailKaryawan::where('nik', $k->nik)
                ->whereIn('hubungan', ['Karyawan', 'Karyawati'])
                ->where('is_scanned_baju', 1)
                ->exists();
        })->values();

        // Ambil scanned_at per karyawan
        $scannedAt = DetailKaryawan::whereIn('nik', $karyawans->pluck('nik'))
            ->whereIn('hubungan', ['Karyawan', 'Karyawati'])
            ->where('is_scanned_baju', 1)
            ->get()
            ->keyBy('nik');

        return view('penerimaan_baju.print', compact('dept', 'sudah', 'belum', 'scannedAt'));
    }

    // ── EXPORT ────────────────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $filename = 'penerimaan-baju-' . now()->format('Ymd-His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PenerimaanBajuExport($request), $filename
        );
    }
}