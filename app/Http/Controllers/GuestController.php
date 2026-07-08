<?php

namespace App\Http\Controllers;

use App\Models\GuestMenu;
use App\Models\PengajuanAnggota;
use App\Models\DetailKaryawan;
use App\Models\Rundown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bus;
use App\Models\KetuaBus;


class GuestController extends Controller
{
    public function dashboard()
    {
        $user     = Auth::user()->load('karyawan.details');
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            Auth::logout();
            return redirect()->route('landing');
        }

        $menus = GuestMenu::getActive();

        $notif = [];

        // Voting — belum vote
        $sudahVoting = \App\Models\VotingVote::where('karyawan_nik', $karyawan->nik)->exists();
        $adaKandidat = \App\Models\VotingTempat::where('is_active', true)->exists();
        if ($adaKandidat && !$sudahVoting) {
            $notif['voting'] = true;
        }

        // Transportasi — belum pilih
        $sudahPilihBus       = \App\Models\Bus::where('nik', $karyawan->nik)->exists();
        $sudahPilihKendaraan = \App\Models\Kendaraan::where('nik', $karyawan->nik)->exists();
        if (!$sudahPilihBus && !$sudahPilihKendaraan) {
            $notif['bus'] = true;
        }

        // Baju — belum konfirmasi di tahun ini
        if (!$karyawan->isBajuConfirmedThisYear()) {
            $notif['baju'] = true;
        }

        // Hadiah — ada hadiah yang siap diambil tapi belum diambil
        $adaHadiah = \App\Models\PenerimaanHadiah::where('nik_pemenang', $karyawan->nik)
            ->where('status', 'siap_diambil')
            ->exists();
        if ($adaHadiah) {
            $notif['penerimaan_hadiah'] = true;
        }

        return view('guest.dashboard', compact('karyawan', 'menus', 'notif'));
    }

    public function menu(string $key)
    {
        if ($key === 'voting') {
            return redirect()->route('guest.voting');
        }

        if ($key === 'baju') {
            return redirect()->route('guest.baju.index');
        }

        if ($key === 'penerimaan_hadiah') {
            return redirect()->route('guest.hadiah');
        }

        if ($key === 'rundown') {
            $rundowns = Rundown::orderBy('mulai')->get();
            $karyawan = Auth::user()->karyawan;
            return view('guest.partials.rundown', compact('rundowns', 'karyawan'));
        }

        $menu     = GuestMenu::where('key', $key)->where('is_active', true)->firstOrFail();
        $user     = Auth::user()->load('karyawan.details');
        $karyawan = $user->karyawan;
        $viewPath = 'guest.partials.' . $key;

        if (!view()->exists($viewPath)) {
            return view('guest.partials.coming_soon', compact('menu', 'karyawan'));
        }

        $pengajuanPending = null;
        $riwayatPengajuan = collect();

        if ($key === 'keluarga') {
            $pengajuanPending = PengajuanAnggota::where('nik', $karyawan->nik)
                ->where('status', 'pending')
                ->latest()
                ->first();

            $riwayatPengajuan = PengajuanAnggota::where('nik', $karyawan->nik)
                ->whereIn('status', ['approved', 'rejected'])
                ->latest()
                ->take(5)
                ->get();
        }

        $isExpired = $menu->berlaku_hingga !== null
                     && \Carbon\Carbon::now()->isAfter($menu->berlaku_hingga);

        return view($viewPath, compact('menu', 'karyawan', 'pengajuanPending', 'riwayatPengajuan', 'isExpired'));
    }

    public function konfirmasiKehadiran(Request $request)
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        // 0 (belum) → 2 (hadir), 2 (hadir) → 1 (tidak hadir), 1 (tidak hadir) → 2 (hadir)
        if ($karyawan->status_kehadiran == 0) {
            $karyawan->status_kehadiran = 2;
        } elseif ($karyawan->status_kehadiran == 2) {
            $karyawan->status_kehadiran = 1;
        } else {
            $karyawan->status_kehadiran = 2;
        }

        $karyawan->save();

        $label = $karyawan->status_kehadiran == 2 ? 'Hadir' : 'Tidak Hadir';

        return response()->json([
            'message'          => "Status kehadiran diperbarui: $label",
            'status_kehadiran' => $karyawan->status_kehadiran,
            'label'            => $label,
        ]);
    }

    // ─────────────────────────────────────────
    // BAJU — Tampil halaman
    // ─────────────────────────────────────────
    public function baju()
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            Auth::logout();
            return redirect()->route('landing');
        }

        $menu    = GuestMenu::where('key', 'baju')->firstOrFail();
        $members = $karyawan->details()->orderBy('id')->get();

        return view('guest.partials.baju', compact('menu', 'karyawan', 'members'));
    }

    // ─────────────────────────────────────────
    // BAJU — Update ukuran, jenis, lengan
    // ─────────────────────────────────────────
    public function bajuUpdate(Request $request)
    {
        $validated = $request->validate([
            'detail_id'   => 'required|integer|exists:detail_karyawans,id',
            'ukuran_kaos' => 'required|string|in:XS,S,M,L,XL,XXL,XXXL,XXXXL,XXXXXL',
            'jenis_kaos'  => 'required|in:Dewasa,Anak',
            'lengan_kaos' => 'nullable|in:Lengan Pendek,Lengan Panjang',
        ], [
            'detail_id.exists'     => 'Data anggota tidak ditemukan.',
            'ukuran_kaos.required' => 'Ukuran kaos wajib dipilih.',
            'ukuran_kaos.in'       => 'Ukuran kaos tidak valid.',
            'jenis_kaos.required'  => 'Jenis kaos wajib dipilih.',
            'jenis_kaos.in'        => 'Jenis kaos tidak valid.',
            'lengan_kaos.in'       => 'Tipe lengan tidak valid.',
        ]);

        $karyawan = Auth::user()->karyawan;

        // Security: pastikan detail milik karyawan yang login
        $detail = $karyawan->details()->findOrFail($validated['detail_id']);

        $detail->update([
            'ukuran_kaos' => $validated['ukuran_kaos'],
            'jenis_kaos'  => $validated['jenis_kaos'],
            'lengan_kaos' => $validated['jenis_kaos'] === 'Anak'
                                ? null
                                : ($validated['lengan_kaos'] ?? null),
        ]);

        // ── Auto-confirm kalau semua anggota sudah punya ukuran ──────────────
        $semuaLengkap = !$karyawan->details()
            ->where(function ($q) {
                $q->whereNull('ukuran_kaos')->orWhere('ukuran_kaos', '');
            })
            ->exists();

        if ($semuaLengkap && !$karyawan->isBajuConfirmedThisYear()) {
            $karyawan->update(['baju_confirmed_at' => now()]);
        }
        // ─────────────────────────────────────────────────────────────────────

        return response()->json([
            'message'     => 'Ukuran kaos berhasil disimpan.',
            'ukuran_kaos' => $detail->ukuran_kaos,
            'jenis_kaos'  => $detail->jenis_kaos,
            'lengan_kaos' => $detail->lengan_kaos,
        ]);
    }

    // ─────────────────────────────────────────
    // BAJU — Konfirmasi pakai data tahun lalu
    // ─────────────────────────────────────────
    public function bajuKonfirmasiTahunLalu(Request $request)
    {
        $karyawan = Auth::user()->karyawan;

        // Validasi: semua anggota harus sudah punya ukuran
        $belumLengkap = $karyawan->details()
            ->where(function ($q) {
                $q->whereNull('ukuran_kaos')->orWhere('ukuran_kaos', '');
            })
            ->exists();

        if ($belumLengkap) {
            return response()->json([
                'message' => 'Masih ada anggota yang belum memiliki ukuran baju.',
            ], 422);
        }

        $karyawan->update(['baju_confirmed_at' => now()]);

        return response()->json(['message' => 'Konfirmasi berhasil.']);
    }

    public function kursisBus()
    {
        $karyawan = Auth::user()->karyawan;
        $bus      = Bus::where('nik', $karyawan->nik)->first();

        if (!$bus || !$bus->kursi) {
            return redirect()->route('guest.dashboard')
                ->with('error', 'Kursi belum ditentukan.');
        }

        [$kode, $nomorKursi] = explode('-', $bus->kursi, 2);

        // Kursi yang terisi karyawan
        $terisiKaryawan = Bus::whereNotNull('kursi')
            ->where('kursi', 'like', $kode . '-%')
            ->get()
            ->keyBy('kursi')
            ->map(fn($b) => (object) [
                'nama_karyawan' => $b->nama_karyawan,
                'nik'           => $b->nik,
                'kursi'         => $b->kursi,
                'tipe'          => 'karyawan',
            ]);

        // Kursi yang terisi anggota keluarga
        $terisiKeluarga = DetailKaryawan::whereNotNull('kursi_bus')
            ->where('kursi_bus', 'like', $kode . '-%')
            ->get()
            ->keyBy('kursi_bus')
            ->map(fn($k) => (object) [
                'nama_karyawan' => $k->nama_keluarga,
                'nik'           => $k->nik,
                'kursi'         => $k->kursi_bus,
                'tipe'          => 'keluarga',
            ]);

        $terisi = $terisiKaryawan->merge($terisiKeluarga);

        // Semua kursi milik NIK yang login (karyawan + seluruh anggota keluarganya)
        $kursiSaya = collect([$bus->kursi])
            ->merge(
                DetailKaryawan::where('nik', $karyawan->nik)
                    ->whereNotNull('kursi_bus')
                    ->pluck('kursi_bus')
            )
            ->filter()
            ->values();

        $ketua = KetuaBus::with('karyawan')
            ->where('kode_bus', $kode)
            ->first();

        return view('guest.partials.kursi-bus', compact(
            'kode', 'terisi', 'ketua', 'karyawan', 'bus', 'nomorKursi', 'kursiSaya'
        ));
    }

    public function keluargaUpdate(Request $request)
    {
        $validated = $request->validate([
            'detail_id'     => 'required|integer|exists:detail_karyawans,id',
            'nama_keluarga' => 'required|string|max:255',
            // Karyawan/Karyawati ditambahkan karena record karyawan itu sendiri
            // juga bisa diedit dari halaman Keluarga (hubungan-nya fixed, gak bisa diganti dari UI).
            'hubungan'      => 'required|in:Suami,Istri,Anak,Karyawan,Karyawati',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'nullable|date',
            'umur'          => 'nullable|integer|min:0|max:150',
            // Ditambahin XXXXL & XXXXXL biar sinkron sama pilihan ukuran di form (khusus Dewasa)
            'ukuran_kaos'   => 'nullable|in:XS,S,M,L,XL,XXL,XXXL,XXXXL,XXXXXL',
            'jenis_kaos'    => 'nullable|in:Dewasa,Anak',
            'lengan_kaos'   => 'nullable|in:Lengan Pendek,Lengan Panjang',
        ], [
            'detail_id.exists'       => 'Data anggota tidak ditemukan.',
            'nama_keluarga.required' => 'Nama wajib diisi.',
            'hubungan.required'      => 'Hubungan wajib dipilih.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        $karyawan = Auth::user()->karyawan;

        // Security: pastikan detail milik karyawan yang login
        $detail = $karyawan->details()->findOrFail($validated['detail_id']);

        $detail->update([
            'nama_keluarga' => $validated['nama_keluarga'],
            'hubungan'      => $validated['hubungan'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'umur'          => $validated['umur'] ?? null,
            'ukuran_kaos'   => $validated['ukuran_kaos'] ?? null,
            'jenis_kaos'    => $validated['jenis_kaos'] ?? null,
            'lengan_kaos'   => $validated['jenis_kaos'] === 'Anak'
                                ? null
                                : ($validated['lengan_kaos'] ?? null),
        ]);

        return response()->json([
            'message' => 'Data anggota keluarga berhasil diperbarui.',
        ]);
    }
}