<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusController extends Controller
{
    public function store(Request $request)
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $request->validate([
            'pilihan'         => 'required|in:bus,kendaraan',
            'plat_no'         => 'required_if:pilihan,kendaraan|nullable|string|max:20',
            'jenis_kendaraan' => 'required_if:pilihan,kendaraan|nullable|in:mobil,motor,truk',
        ], [
            'pilihan.required'              => 'Pilihan transportasi wajib dipilih.',
            'pilihan.in'                    => 'Pilihan tidak valid.',
            'plat_no.required_if'           => 'Plat nomor kendaraan wajib diisi.',
            'jenis_kendaraan.required_if'   => 'Jenis kendaraan wajib dipilih.',
            'jenis_kendaraan.in'            => 'Jenis kendaraan tidak valid.',
        ]);

        if ($request->pilihan === 'bus') {
            Kendaraan::where('nik', $karyawan->nik)->delete();

            Bus::updateOrCreate(
                ['nik' => $karyawan->nik],
                ['nama_karyawan' => $karyawan->nama]
            );

            return response()->json([
                'message' => 'Pilihan transportasi berhasil disimpan: Naik Bus.',
                'pilihan' => 'bus',
            ]);
        }

        // Kendaraan pribadi
        Bus::where('nik', $karyawan->nik)->delete();

        Kendaraan::updateOrCreate(
            ['nik' => $karyawan->nik],
            [
                'nama_karyawan'  => $karyawan->nama,
                'plat_no'        => strtoupper(trim($request->plat_no)),
                'jenis_kendaraan'=> $request->jenis_kendaraan,  // ← tambahan
            ]
        );

        return response()->json([
            'message'         => 'Pilihan transportasi berhasil disimpan: Kendaraan Pribadi.',
            'pilihan'         => 'kendaraan',
            'plat_no'         => strtoupper(trim($request->plat_no)),
            'jenis_kendaraan' => $request->jenis_kendaraan,     // ← tambahan
        ]);
    }

    public function cancel()
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        Bus::where('nik', $karyawan->nik)->delete();
        Kendaraan::where('nik', $karyawan->nik)->delete();

        return response()->json([
            'message' => 'Pilihan transportasi dibatalkan.',
            'pilihan' => null,
        ]);
    }
}