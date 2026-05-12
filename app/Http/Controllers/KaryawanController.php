<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\DetailKaryawan;
use Illuminate\Http\Request;
use App\Exports\KaryawanExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BajuFamilyGatheringImport;

class KaryawanController extends Controller
{
    // ─────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Karyawan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nik', 'like', "%$search%")
                  ->orWhere('departemen', 'like', "%$search%");
            });
        }

        if ($request->filled('departemen')) {
            $query->where('departemen', $request->departemen);
        }

        if ($request->filled('keterangan')) {
            $query->where('keterangan', $request->keterangan);
        }

        $karyawans      = $query->orderBy('nama')->paginate(15)->withQueryString();
        $departemenList = Karyawan::select('departemen')->distinct()->orderBy('departemen')->pluck('departemen');

        return view('karyawan.index', compact('karyawans', 'departemenList'));
    }

    // ─────────────────────────────────────────
    // STORE (AJAX)
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik'              => 'required|string|max:20|unique:karyawans,nik',
            'nik_login'        => 'nullable|string|max:20',
            'nama'             => 'required|string|max:100',
            'departemen'       => 'required|string|max:50',
            'keterangan'       => 'required|in:Aktif,Non-Aktif',
            'status_kehadiran' => 'nullable|boolean',

            'details'                       => 'nullable|array',
            'details.*.nama_keluarga'       => 'required|string|max:100',
            'details.*.hubungan'            => 'required|in:Karyawan,Karyawati,Istri,Suami,Anak,Saudara',
            'details.*.jenis_kelamin'       => 'required|in:Laki-laki,Perempuan',
            'details.*.tanggal_lahir'       => 'nullable|date',
            'details.*.umur'                => 'nullable|integer|min:0',
            'details.*.ukuran_kaos'         => 'nullable|string|max:10',
            'details.*.jenis_kaos'          => 'nullable|in:Dewasa,Anak',
            'details.*.lengan_kaos'         => 'nullable|in:Lengan Pendek,Lengan Panjang',
        ], [
            'nik.required'                     => 'NIK wajib diisi.',
            'nik.unique'                       => 'NIK sudah terdaftar.',
            'nama.required'                    => 'Nama karyawan wajib diisi.',
            'departemen.required'              => 'Departemen wajib diisi.',
            'keterangan.required'              => 'Status karyawan wajib dipilih.',
            'details.*.nama_keluarga.required' => 'Nama anggota keluarga wajib diisi.',
            'details.*.hubungan.required'      => 'Hubungan wajib dipilih.',
            'details.*.jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        $details        = $validated['details'] ?? [];
        $jumlahKeluarga = count($details);

        $karyawan = Karyawan::create([
            'nik'              => $validated['nik'],
            'nik_login'        => $validated['nik_login'] ?? null,
            'nama'             => $validated['nama'],
            'departemen'       => $validated['departemen'],
            'keterangan'       => $validated['keterangan'],
            'status_kehadiran' => $request->boolean('status_kehadiran'),
            'jumlah_keluarga'  => $jumlahKeluarga,
        ]);

        foreach ($details as $d) {
            DetailKaryawan::create($this->buildDetailPayload($karyawan->nik, $d));
        }

        return response()->json([
            'message'  => 'Karyawan berhasil ditambahkan.',
            'karyawan' => $karyawan->load('details'),
        ], 201);
    }

    // ─────────────────────────────────────────
    // SHOW (AJAX)
    // ─────────────────────────────────────────
    public function show($id)
    {
        $karyawan = Karyawan::with('details')->findOrFail($id);

        return response()->json([
            'karyawan' => $karyawan,
            'details'  => $karyawan->details,
        ]);
    }

    // ─────────────────────────────────────────
    // EDIT (AJAX)
    // ─────────────────────────────────────────
    public function edit($id)
    {
        $karyawan = Karyawan::with('details')->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'karyawan' => $karyawan,
                'details'  => $karyawan->details,
            ]);
        }

        $departemenList = Karyawan::select('departemen')->distinct()->orderBy('departemen')->pluck('departemen');
        return view('karyawan.index', compact('karyawan', 'departemenList'));
    }

    // ─────────────────────────────────────────
    // UPDATE (AJAX)
    // ─────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $validated = $request->validate([
            'nik'              => 'required|string|max:20|unique:karyawans,nik,' . $id,
            'nik_login'        => 'nullable|string|max:20',
            'nama'             => 'required|string|max:100',
            'departemen'       => 'required|string|max:50',
            'keterangan'       => 'required|in:Aktif,Non-Aktif',
            'status_kehadiran' => 'nullable|boolean',

            'details'                       => 'nullable|array',
            'details.*.id'                  => 'nullable|integer',
            'details.*.nama_keluarga'       => 'required|string|max:100',
            'details.*.hubungan'            => 'required|in:Karyawan,Karyawati,Istri,Suami,Anak,Saudara',
            'details.*.jenis_kelamin'       => 'required|in:Laki-laki,Perempuan',
            'details.*.tanggal_lahir'       => 'nullable|date',
            'details.*.umur'                => 'nullable|integer|min:0',
            'details.*.ukuran_kaos'         => 'nullable|string|max:10',
            'details.*.jenis_kaos'          => 'nullable|in:Dewasa,Anak',
            'details.*.lengan_kaos'         => 'nullable|in:Lengan Pendek,Lengan Panjang',
        ], [
            'nik.required'                     => 'NIK wajib diisi.',
            'nik.unique'                       => 'NIK sudah digunakan karyawan lain.',
            'nama.required'                    => 'Nama karyawan wajib diisi.',
            'departemen.required'              => 'Departemen wajib diisi.',
            'keterangan.required'              => 'Status karyawan wajib dipilih.',
            'details.*.nama_keluarga.required' => 'Nama anggota keluarga wajib diisi.',
            'details.*.hubungan.required'      => 'Hubungan wajib dipilih.',
            'details.*.jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        $details        = $validated['details'] ?? [];
        $jumlahKeluarga = count($details);

        $karyawan->update([
            'nik'              => $validated['nik'],
            'nik_login'        => $validated['nik_login'] ?? null,
            'nama'             => $validated['nama'],
            'departemen'       => $validated['departemen'],
            'keterangan'       => $validated['keterangan'],
            'status_kehadiran' => $request->boolean('status_kehadiran'),
            'jumlah_keluarga'  => $jumlahKeluarga,
        ]);

        $submittedIds = collect($details)
            ->filter(fn($d) => !empty($d['id']))
            ->pluck('id')
            ->toArray();

        $karyawan->details()->whereNotIn('id', $submittedIds)->delete();

        foreach ($details as $d) {
            $payload = $this->buildDetailPayload($karyawan->nik, $d);

            if (!empty($d['id'])) {
                DetailKaryawan::where('id', $d['id'])->update($payload);
            } else {
                DetailKaryawan::create($payload);
            }
        }

        return response()->json([
            'message'  => 'Data karyawan berhasil diupdate.',
            'karyawan' => $karyawan->fresh()->load('details'),
        ]);
    }

    // ─────────────────────────────────────────
    // DESTROY (AJAX)
    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->details()->delete();
        $karyawan->delete();

        return response()->json(['message' => 'Karyawan berhasil dihapus.']);
    }

    // ─────────────────────────────────────────
    // EXPORT
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $filename = 'data-karyawan-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new KaryawanExport($request), $filename);
    }

    // ─────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────
    private function buildDetailPayload(string $nik, array $d): array
    {
        $jenisKaos = $d['jenis_kaos'] ?? 'Dewasa';

        return [
            'nik'           => $nik,
            'nama_keluarga' => $d['nama_keluarga'],
            'hubungan'      => $d['hubungan'],
            'jenis_kelamin' => $d['jenis_kelamin'],
            'tanggal_lahir' => $d['tanggal_lahir'] ?? null,
            'umur'          => $d['umur'] ?? 0,
            'ukuran_kaos'   => $d['ukuran_kaos'] ?? null,
            'jenis_kaos'    => $jenisKaos,
            // kalau Anak, lengan tidak relevan → simpan null
            'lengan_kaos'   => $jenisKaos === 'Anak' ? null : ($d['lengan_kaos'] ?? null),
        ];
    }

    public function importBaju(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'file_excel.required' => 'File excel wajib dipilih.',
            'file_excel.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'      => 'Ukuran file maksimal 5MB.',
        ]);
 
        try {
            $import = new BajuFamilyGatheringImport();
            Excel::import($import, $request->file('file_excel'));
 
            return response()->json([
                'message'  => "Import berhasil! {$import->imported} data diproses, {$import->skipped} baris dilewati.",
                'imported' => $import->imported,
                'skipped'  => $import->skipped,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Import gagal: ' . $e->getMessage(),
            ], 500);
        }
    }
 
}