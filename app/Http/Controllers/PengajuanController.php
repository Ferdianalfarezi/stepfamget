<?php

namespace App\Http\Controllers;

use App\Models\DetailKaryawan;
use App\Models\Karyawan;
use App\Models\PengajuanAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengajuanController extends Controller
{
    // ═════════════════════════════════════════════════════════════════════════
    // GUEST — Submit pengajuan
    // ═════════════════════════════════════════════════════════════════════════

    public function store(Request $request)
    {
        $nik = Auth::user()->karyawan_nik;

        if (!$nik) {
            return response()->json([
                'message' => 'Data NIK pengguna tidak ditemukan. Hubungi administrator.',
            ], 422);
        }

        $karyawan = Karyawan::where('nik', $nik)->first();

        if (!$karyawan) {
            return response()->json([
                'message' => 'Data karyawan tidak ditemukan. Hubungi administrator.',
            ], 422);
        }

        $hasPending = PengajuanAnggota::where('nik', $nik)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return response()->json([
                'message' => 'Masih ada pengajuan yang sedang ditinjau. Tunggu hasilnya terlebih dahulu.',
            ], 422);
        }

        $validated = $request->validate([
            'nama_keluarga' => 'required|string|max:100',
            'hubungan'      => 'required|in:Istri,Suami,Anak',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'nullable|date',
            'umur'          => 'nullable|integer|min:0|max:150',
            'ukuran_kaos'   => 'nullable|string|max:10',
            'jenis_kaos'    => 'nullable|string|max:20',
            'lengan_kaos'   => 'nullable|string|max:30',
        ], [
            'nama_keluarga.required' => 'Nama anggota keluarga wajib diisi.',
            'hubungan.required'      => 'Hubungan wajib dipilih.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        $pengajuan = PengajuanAnggota::create([
            'nik'           => $nik,
            'departemen'    => $karyawan->departemen,
            'nama_keluarga' => $validated['nama_keluarga'],
            'hubungan'      => $validated['hubungan'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'umur'          => $validated['umur']          ?? null,
            'ukuran_kaos'   => $validated['ukuran_kaos']   ?? null,
            'jenis_kaos'    => $validated['jenis_kaos']    ?? null,
            'lengan_kaos'   => $validated['lengan_kaos']   ?? null,
            'status'        => 'pending',
        ]);

        return response()->json([
            'message'   => 'Pengajuan berhasil dikirim. Mohon tunggu konfirmasi dari panitia.',
            'pengajuan' => $pengajuan,
        ], 201);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // ADMIN — Kelola pengajuan
    // ═════════════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = PengajuanAnggota::with('karyawan')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_keluarga', 'like', "%$s%")
                  ->orWhere('departemen', 'like', "%$s%")
                  ->orWhereHas('karyawan', fn($k) => $k->where('nama', 'like', "%$s%")
                                                        ->orWhere('nik', 'like', "%$s%"));
            });
        }

        $pengajuans = $query->paginate(15)->withQueryString();

        $counts = [
            'all'      => PengajuanAnggota::count(),
            'pending'  => PengajuanAnggota::where('status', 'pending')->count(),
            'approved' => PengajuanAnggota::where('status', 'approved')->count(),
            'rejected' => PengajuanAnggota::where('status', 'rejected')->count(),
        ];

        return view('pengajuans.index', compact('pengajuans', 'counts'));
    }

    public function approve($id)
    {
        $pengajuan = PengajuanAnggota::findOrFail($id);

        if (!$pengajuan->isPending()) {
            return response()->json(['message' => 'Pengajuan sudah diproses sebelumnya.'], 422);
        }

        DetailKaryawan::create([
            'nik'           => $pengajuan->nik,
            'nama_keluarga' => $pengajuan->nama_keluarga,
            'hubungan'      => $pengajuan->hubungan,
            'jenis_kelamin' => $pengajuan->jenis_kelamin,
            'tanggal_lahir' => $pengajuan->tanggal_lahir,
            'umur'          => $pengajuan->umur ?? 0,
            'ukuran_kaos'   => $pengajuan->ukuran_kaos,
            'jenis_kaos'    => $pengajuan->jenis_kaos,
            'lengan_kaos'   => $pengajuan->lengan_kaos,
        ]);

        $karyawan = Karyawan::where('nik', $pengajuan->nik)->first();
        if ($karyawan) {
            $karyawan->increment('jumlah_keluarga');
        }

        $pengajuan->update([
            'status'       => 'approved',
            'reviewed_at'  => now(),
            'reviewed_by'  => Auth::user()->id,
            'alasan_tolak' => null,
        ]);

        return response()->json([
            'message' => 'Pengajuan disetujui dan data anggota keluarga telah ditambahkan.',
        ]);
    }

    public function reject(Request $request, $id)
    {
        $pengajuan = PengajuanAnggota::findOrFail($id);

        if (!$pengajuan->isPending()) {
            return response()->json(['message' => 'Pengajuan sudah diproses sebelumnya.'], 422);
        }

        $validated = $request->validate([
            'alasan_tolak' => 'nullable|string|max:500',
        ]);

        $pengajuan->update([
            'status'       => 'rejected',
            'reviewed_at'  => now(),
            'reviewed_by'  => Auth::user()->id,
            'alasan_tolak' => $validated['alasan_tolak'] ?? null,
        ]);

        return response()->json([
            'message' => 'Pengajuan telah ditolak.',
        ]);
    }
}