<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\DetailKaryawan;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = Karyawan::count();
        $totalAktif    = Karyawan::where('keterangan', 'Aktif')->count();

        $totalHadir    = Karyawan::where('keterangan', 'Aktif')
                            ->where('status_kehadiran', true)
                            ->count();

        $topDepartemen = Karyawan::selectRaw('departemen, COUNT(*) as total_login')
                    ->whereNotNull('last_login_at')
                    ->groupBy('departemen')
                    ->orderByDesc('total_login')
                    ->take(5)
                    ->get();

        // Keluarga dari karyawan yang HADIR saja (exclude karyawan/karyawati itu sendiri)
        $totalKeluargaHadir = DetailKaryawan::whereHas('karyawan', function ($q) {
                                    $q->where('keterangan', 'Aktif')
                                      ->where('status_kehadiran', true);
                                })
                                ->whereNotIn('hubungan', ['Karyawan', 'Karyawati'])
                                ->count();

        // Total keluarga keseluruhan (untuk stat card "Total Anggota Keluarga")
        $totalKeluarga = DetailKaryawan::whereNotIn('hubungan', ['Karyawan', 'Karyawati'])
                            ->count();

        // Total peserta = karyawan hadir + keluarga mereka
        $totalPeserta  = $totalHadir + $totalKeluargaHadir;

        $perDepartemen = Karyawan::selectRaw('departemen, count(*) as total')
                            ->groupBy('departemen')
                            ->orderByDesc('total')
                            ->get();

        $departemen     = Karyawan::select('departemen')->distinct()->pluck('departemen');
        $recentKaryawan = Karyawan::latest()->take(5)->get();

        $lastLoginGuests = Karyawan::whereNotNull('last_login_at')
                            ->orderByDesc('last_login_at')
                            ->take(5)
                            ->get(['nik', 'nama', 'departemen', 'last_login_at']);

        return view('dashboard', compact(
            'totalKaryawan',
            'totalAktif',
            'totalHadir',
            'totalKeluarga',
            'totalKeluargaHadir',
            'totalPeserta',
            'perDepartemen',
            'departemen',
            'recentKaryawan',
            'lastLoginGuests',
            'topDepartemen',
        ));
    }
}