<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\PenerimaanHadiah;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PenerimaanHadiahController extends Controller
{
    // ─────────────────────────────────────────
    // INDEX (Admin)
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = PenerimaanHadiah::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('barang', 'like', "%$search%")
                  ->orWhere('nama_pemenang', 'like', "%$search%")
                  ->orWhere('nik_pemenang', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items     = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $karyawans = Karyawan::orderBy('nama')->get(['nik', 'nama', 'departemen']);

        return view('penerimaan-hadiah.index', compact('items', 'karyawans'));
    }

    // ─────────────────────────────────────────
    // STORE — tambah hadiah baru (admin)
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang' => 'required|string|max:200',
        ], [
            'barang.required' => 'Nama barang wajib diisi.',
        ]);

        $item = PenerimaanHadiah::create([
            'barang' => $validated['barang'],
            'status' => 'belum_ada_pemenang',
        ]);

        return response()->json([
            'message' => 'Hadiah berhasil ditambahkan.',
            'item'    => $item,
        ], 201);
    }

    // ─────────────────────────────────────────
    // EDIT (AJAX) — load data untuk modal edit
    // ─────────────────────────────────────────
    public function edit($id)
    {
        $item = PenerimaanHadiah::findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['item' => $item]);
        }

        return view('penerimaan-hadiah.index', compact('item'));
    }

    // ─────────────────────────────────────────
    // UPDATE — set pemenang + generate QR
    // ─────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $item = PenerimaanHadiah::findOrFail($id);

        $validated = $request->validate([
            'barang'       => 'required|string|max:200',
            'nik_pemenang' => 'nullable|exists:karyawans,nik',
        ], [
            'barang.required'      => 'Nama barang wajib diisi.',
            'nik_pemenang.exists'  => 'Karyawan tidak ditemukan.',
        ]);

        $karyawan = null;
        if (!empty($validated['nik_pemenang'])) {
            $karyawan = Karyawan::where('nik', $validated['nik_pemenang'])->first();
        }

        // Generate QR code unik: NIK + random string
        $qrCode = null;
        if ($karyawan && $item->nik_pemenang !== $validated['nik_pemenang']) {
            // generate ulang hanya jika pemenang berubah
            do {
                $qrCode = strtoupper(Str::random(4)) . rand(1000, 9999) . strtoupper(Str::random(4));
            } while (PenerimaanHadiah::where('qr_code', $qrCode)->exists());
        } elseif ($karyawan && $item->qr_code) {
            // pemenang sama, pertahankan QR lama
            $qrCode = $item->qr_code;
        }

        $item->update([
            'barang'        => $validated['barang'],
            'nik_pemenang'  => $karyawan?->nik,
            'nama_pemenang' => $karyawan?->nama,
            'qr_code'       => $qrCode,
            'status'        => $karyawan ? 'siap_diambil' : 'belum_ada_pemenang',
            'scanned_at'    => $karyawan ? $item->scanned_at : null,
        ]);

        return response()->json([
            'message' => 'Data hadiah berhasil diupdate.',
            'item'    => $item->fresh(),
        ]);
    }

    // ─────────────────────────────────────────
    // DESTROY (AJAX)
    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $item = PenerimaanHadiah::findOrFail($id);
        $item->delete();

        return response()->json([
            'message' => 'Data hadiah berhasil dihapus.',
        ]);
    }

    // ─────────────────────────────────────────
    // SCAN — ubah status jadi sudah_diambil
    // ─────────────────────────────────────────
    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $item = PenerimaanHadiah::where('qr_code', trim($request->qr_code))->first();

        if (!$item) {
            return response()->json([
                'message' => 'QR Code tidak ditemukan.',
                'status'  => 'not_found',
            ], 404);
        }

        if ($item->status === 'sudah_diambil') {
            return response()->json([
                'message'      => 'Hadiah ini sudah diambil sebelumnya.',
                'status'       => 'already_taken',
                'item'         => $item,
                'scanned_at'   => $item->scanned_at?->format('d M Y, H:i'),
            ], 200);
        }

        if ($item->status === 'belum_ada_pemenang') {
            return response()->json([
                'message' => 'Hadiah belum memiliki pemenang.',
                'status'  => 'no_winner',
            ], 422);
        }

        $item->update([
            'status'     => 'sudah_diambil',
            'scanned_at' => now(),
        ]);

        return response()->json([
            'message'      => 'Berhasil! Hadiah telah diambil.',
            'status'       => 'success',
            'item'         => $item->fresh(),
            'scanned_at'   => $item->fresh()->scanned_at->format('d M Y, H:i'),
        ]);
    }

    // ─────────────────────────────────────────
    // RESET PEMENANG (AJAX)
    // ─────────────────────────────────────────
    public function resetPemenang($id)
    {
        $item = PenerimaanHadiah::findOrFail($id);
        $item->update([
            'nik_pemenang'  => null,
            'nama_pemenang' => null,
            'qr_code'       => null,
            'status'        => 'belum_ada_pemenang',
            'scanned_at'    => null,
        ]);

        return response()->json([
            'message' => 'Pemenang berhasil direset.',
            'item'    => $item->fresh(),
        ]);
    }

    public function guestIndex(Request $request)
{
    $karyawan = Auth::user()->karyawan;

    if (!$karyawan) {
        return redirect()->route('landing');
    }

    $hadiah = PenerimaanHadiah::where('nik_pemenang', $karyawan->nik)->first();

    return view('guest.partials.hadiah', [
        'hadiah'   => $hadiah,
        'karyawan' => $karyawan,
    ]);
}

    public function printView($id)
    {
        $hadiah = PenerimaanHadiah::findOrFail($id);
        return view('penerimaan-hadiah.print', compact('hadiah'));
    }
}