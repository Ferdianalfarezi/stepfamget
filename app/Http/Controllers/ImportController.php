<?php

namespace App\Http\Controllers;

use App\Imports\KaryawanImport;
use App\Imports\DetailKaryawanImport;
use App\Models\Karyawan;
use App\Models\DetailKaryawan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index', [
            'totalKaryawan' => Karyawan::count(),
            'totalDetail'   => DetailKaryawan::count(),
        ]);
    }

    public function importKaryawan(Request $request)
    {
        $request->validate([
            'file_karyawan' => 'required|mimes:xlsx,xls|max:10240',
            'mode_karyawan' => 'required|in:append,replace',
        ], [
            'file_karyawan.required' => 'File Excel wajib dipilih.',
            'file_karyawan.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_karyawan.max'      => 'Ukuran file maksimal 10MB.',
        ]);

        try {
            if ($request->mode_karyawan === 'replace') {
                Karyawan::truncate();
            }

            $import = new KaryawanImport();
            Excel::import($import, $request->file('file_karyawan'));

            return back()->with('success_karyawan',
                "✅ Import berhasil! {$import->imported} data karyawan diimport" .
                ($import->skipped > 0 ? ", {$import->skipped} baris dilewati." : ".")
            );
        } catch (\Exception $e) {
            return back()->with('error_karyawan', '❌ Gagal import: ' . $e->getMessage());
        }
    }

    public function importDetail(Request $request)
    {
        $request->validate([
            'file_detail' => 'required|mimes:xlsx,xls|max:10240',
            'mode_detail' => 'required|in:append,replace',
        ], [
            'file_detail.required' => 'File Excel wajib dipilih.',
            'file_detail.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_detail.max'      => 'Ukuran file maksimal 10MB.',
        ]);

        try {
            if ($request->mode_detail === 'replace') {
                DetailKaryawan::truncate();
            }

            $import = new DetailKaryawanImport();
            Excel::import($import, $request->file('file_detail'));

            return back()->with('success_detail',
                "✅ Import berhasil! {$import->imported} data detail/keluarga diimport" .
                ($import->skipped > 0 ? ", {$import->skipped} baris dilewati." : ".")
            );
        } catch (\Exception $e) {
            return back()->with('error_detail', '❌ Gagal import: ' . $e->getMessage());
        }
    }
}
