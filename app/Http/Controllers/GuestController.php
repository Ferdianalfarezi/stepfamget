<?php

namespace App\Http\Controllers;

use App\Models\GuestMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('guest.dashboard', compact('karyawan', 'menus', 'notif'));
    }

    public function menu(string $key)
    {
        if ($key === 'voting') {
            return redirect()->route('guest.voting');
        }

        $menu     = GuestMenu::where('key', $key)->where('is_active', true)->firstOrFail();
        $user     = Auth::user()->load('karyawan.details');
        $karyawan = $user->karyawan;
        $viewPath = 'guest.partials.' . $key;

        if (!view()->exists($viewPath)) {
            return view('guest.partials.coming_soon', compact('menu', 'karyawan'));
        }

        return view($viewPath, compact('menu', 'karyawan'));
    }

    public function konfirmasiKehadiran(Request $request)
    {
        $karyawan = Auth::user()->karyawan;

        if (!$karyawan) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $karyawan->status_kehadiran = !$karyawan->status_kehadiran;
        $karyawan->save();

        $label = $karyawan->status_kehadiran ? 'Hadir' : 'Tidak Hadir';

        return response()->json([
            'message'          => "Status kehadiran diperbarui: $label",
            'status_kehadiran' => $karyawan->status_kehadiran,
            'label'            => $label,
        ]);
    }
}