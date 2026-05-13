<?php

namespace App\Http\Controllers;

use App\Models\KetuaBus;
use App\Models\Karyawan;
use App\Models\Bus;
use Illuminate\Http\Request;

class KetuaBusController extends Controller
{
    public function card()
    {
        $ketuaList = KetuaBus::with('karyawan')->orderBy('kode_bus')->get();

        $cards = $ketuaList->map(function ($ketua) {
            $kode       = $ketua->kode_bus;
            $terisi     = Bus::where('kursi', 'like', $kode . '-%')->count();
            $totalKursi = 54;
            $kosong     = $totalKursi - $terisi;
            $snack      = $terisi + 2;
            $persen     = $totalKursi > 0 ? round($terisi / $totalKursi * 100) : 0;
            $dept       = $ketua->karyawan?->departemen ?? '-';

            return (object) compact(
                'ketua', 'kode', 'terisi', 'totalKursi', 'kosong', 'snack', 'persen', 'dept'
            );
        });

        return view('buses.card', compact('cards'));
    }

    public function index()
    {
        $ketuaList  = KetuaBus::with('karyawan')->orderBy('kode_bus')->paginate(15);
        $karyawans  = Karyawan::where('keterangan', 'Aktif')->orderBy('nama')->get(['nik', 'nama', 'departemen']);
        return view('buses.ketua', compact('ketuaList', 'karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_bus' => 'required|string|max:10|unique:ketua_bus,kode_bus',
            'nik'      => 'required|exists:karyawans,nik',
            'no_telp'  => 'nullable|string|max:20',
        ]);

        KetuaBus::create($request->only('kode_bus', 'nik', 'no_telp'));
        return response()->json(['message' => 'Ketua bus berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $ketua = KetuaBus::findOrFail($id);

        $request->validate([
            'kode_bus' => 'required|string|max:10|unique:ketua_bus,kode_bus,' . $id,
            'nik'      => 'required|exists:karyawans,nik',
            'no_telp'  => 'nullable|string|max:20',
        ]);

        $ketua->update($request->only('kode_bus', 'nik', 'no_telp'));
        return response()->json(['message' => 'Data berhasil diupdate.']);
    }

    public function destroy($id)
    {
        KetuaBus::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus.']);
    }

    public function edit($id)
    {
        $ketua = KetuaBus::with('karyawan')->findOrFail($id);
        return response()->json($ketua);
    }

    public function layout($kode)
    {
        $ketua = KetuaBus::with('karyawan')->where('kode_bus', $kode)->firstOrFail();

        // Generate semua kursi A-1 s/d A-54
        $semuaKursi = collect(range(1, 54))->map(fn($n) => $kode . '-' . $n);

        // Kursi yang sudah terisi
        $terisi = Bus::where('kursi', 'like', $kode . '-%')
                    ->get(['nama_karyawan', 'nik', 'kursi'])
                    ->keyBy('kursi');

        return view('buses.layout', compact('ketua', 'kode', 'semuaKursi', 'terisi'));
    }
}