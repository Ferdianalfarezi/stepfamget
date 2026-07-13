<?php

namespace App\Http\Controllers;

use App\Models\Konsumsi;
use App\Models\Karyawan;
use App\Models\DetailKaryawan;
use App\Models\Kendaraan;
use Illuminate\Http\Request;

class KonsumsiController extends Controller
{
    // Sumber qty yang valid — dipake buat validasi & dropdown
    const QTY_SOURCES = ['semua_orang', 'karyawan_saja', 'anak', 'pasangan', 'vip', 'vvip'];

    public function index(Request $request)
    {
        $search = $request->search;

        $query = Konsumsi::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('satuan', 'like', "%$search%");
            });
        }
        $konsumsis = $query->orderBy('nama')->paginate(15)->withQueryString();

        // ── Semua Orang (Karyawan + Keluarga) ──
        $qtyHadir = Karyawan::whereNotNull('trans_confirmed_at')->count()
            + DetailKaryawan::whereIn('nik', Karyawan::whereNotNull('trans_confirmed_at')->pluck('nik'))
                ->whereNotIn('hubungan', ['Karyawan', 'Karyawati'])
                ->count();

        $qtySemua = Karyawan::count()
            + DetailKaryawan::whereNotIn('hubungan', ['Karyawan', 'Karyawati'])->count();

        // ── Karyawan Saja (tanpa keluarga) ──
        $qtyKaryawanSemua = Karyawan::count();
        $qtyKaryawanHadir = Karyawan::whereNotNull('trans_confirmed_at')->count();

        // ── Anak ──
        $qtyAnakSemua = DetailKaryawan::where('hubungan', 'Anak')->count();
        $qtyAnakHadir = DetailKaryawan::where('hubungan', 'Anak')
            ->whereIn('nik', Karyawan::whereNotNull('trans_confirmed_at')->pluck('nik'))
            ->count();

        // ── Pasangan (Suami/Istri) ──
        $qtyPasanganSemua = DetailKaryawan::whereIn('hubungan', ['Suami', 'Istri'])->count();
        $qtyPasanganHadir = DetailKaryawan::whereIn('hubungan', ['Suami', 'Istri'])
            ->whereIn('nik', Karyawan::whereNotNull('trans_confirmed_at')->pluck('nik'))
            ->count();

        // ── VIP & VVIP (dari Kendaraan, fixed count, dipisah) ──
        $qtyVip  = Kendaraan::where('jenis_tiket', Kendaraan::TIKET_VIP)->count();
        $qtyVvip = Kendaraan::where('jenis_tiket', Kendaraan::TIKET_VVIP)->count();

        return view('konsumsis.index', compact(
            'konsumsis', 'search',
            'qtyHadir', 'qtySemua',
            'qtyKaryawanHadir', 'qtyKaryawanSemua',
            'qtyAnakHadir', 'qtyAnakSemua',
            'qtyPasanganHadir', 'qtyPasanganSemua',
            'qtyVip', 'qtyVvip'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'satuan'     => 'required|string|max:50',
            'spare'      => 'required|integer',
            'qty_source' => 'required|in:' . implode(',', self::QTY_SOURCES),
        ]);

        Konsumsi::create($validated);

        return response()->json([
            'message' => "Konsumsi \"{$validated['nama']}\" berhasil ditambahkan.",
        ]);
    }

    public function show(Konsumsi $konsumsi)
    {
        return response()->json($konsumsi);
    }

    public function update(Request $request, Konsumsi $konsumsi)
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'satuan'     => 'required|string|max:50',
            'spare'      => 'required|integer',
            'qty_source' => 'required|in:' . implode(',', self::QTY_SOURCES),
        ]);

        $konsumsi->update($validated);

        return response()->json([
            'message' => "Konsumsi \"{$validated['nama']}\" berhasil diperbarui.",
        ]);
    }

    public function destroy(Konsumsi $konsumsi)
    {
        $nama = $konsumsi->nama;
        $konsumsi->delete();

        return response()->json([
            'message' => "Konsumsi \"{$nama}\" berhasil dihapus.",
        ]);
    }
}