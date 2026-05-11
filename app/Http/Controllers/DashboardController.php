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
        $totalHadir    = Karyawan::where('status_kehadiran', true)->count();
        $totalKeluarga = DetailKaryawan::where('hubungan', '!=', 'Karyawan')
                            ->where('hubungan', '!=', 'Karyawati')->count();

        $departemen    = Karyawan::select('departemen')->distinct()->pluck('departemen');
        $perDepartemen = Karyawan::selectRaw('departemen, count(*) as total')
                            ->groupBy('departemen')->orderByDesc('total')->get();

        $recentKaryawan = Karyawan::latest()->take(5)->get();

        return view('dashboard', compact(
            'totalKaryawan',
            'totalAktif',
            'totalHadir',
            'totalKeluarga',
            'perDepartemen',
            'departemen',
            'recentKaryawan',
        ));
    }
}