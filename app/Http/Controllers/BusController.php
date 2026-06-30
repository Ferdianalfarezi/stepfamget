<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Kendaraan;
use App\Models\GuestMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BusController extends Controller
{
    // ── Helper cek expired ─────────────────────────
    private function isExpired(): bool
    {
        $menu = GuestMenu::where('key', 'bus')->first();
        if (!$menu || !$menu->berlaku_hingga) return false;
        return Carbon::now()->isAfter($menu->berlaku_hingga);
    }

    // ── Store ──────────────────────────────────────
    public function store(Request $request)
    {
        if ($this->isExpired()) {
            return response()->json([
                'message' => 'Batas waktu pendaftaran transportasi sudah habis.',
            ], 403);
        }

        $request->validate([
            'pilihan'         => 'required|in:bus,kendaraan',
            'plat_no'         => 'nullable|string|max:12',
            'jenis_kendaraan' => 'nullable|in:mobil,motor,truk',
        ]);

        $karyawan = Auth::user()->karyawan;

        // Hapus data lama dulu
        Bus::where('nik', $karyawan->nik)->delete();
        Kendaraan::where('nik', $karyawan->nik)->delete();

        if ($request->pilihan === 'bus') {
            Bus::create([
                'nik'           => $karyawan->nik,
                'nama_karyawan' => $karyawan->nama,
            ]);

            // Set trans_confirmed_at
            $karyawan->update(['trans_confirmed_at' => now()]);

            return response()->json([
                'message' => 'Pilihan bus berhasil disimpan.',
                'pilihan' => 'bus',
            ]);
        }

        // kendaraan
        Kendaraan::create([
            'nik'             => $karyawan->nik,
            'nama_karyawan'   => $karyawan->nama,
            'plat_no'         => strtoupper($request->plat_no),
            'jenis_kendaraan' => $request->jenis_kendaraan ?? 'mobil',
        ]);

        // Set trans_confirmed_at
        $karyawan->update(['trans_confirmed_at' => now()]);

        return response()->json([
            'message'         => 'Kendaraan pribadi berhasil disimpan.',
            'pilihan'         => 'kendaraan',
            'plat_no'         => strtoupper($request->plat_no),
            'jenis_kendaraan' => $request->jenis_kendaraan ?? 'mobil',
        ]);
    }

    // ── Cancel ─────────────────────────────────────
    public function cancel(Request $request)
    {
        if ($this->isExpired()) {
            return response()->json([
                'message' => 'Batas waktu sudah habis, pilihan tidak bisa dibatalkan.',
            ], 403);
        }

        $karyawan = Auth::user()->karyawan;

        Bus::where('nik', $karyawan->nik)->delete();
        Kendaraan::where('nik', $karyawan->nik)->delete();

        // Reset trans_confirmed_at
        $karyawan->update(['trans_confirmed_at' => null]);

        return response()->json([
            'message' => 'Pilihan transportasi berhasil dibatalkan.',
        ]);
    }
}