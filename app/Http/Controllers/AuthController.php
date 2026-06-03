<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ─── Landing ──────────────────────────────────────────
    public function landing()
    {
        if (Auth::check()) {
            return Auth::user()->isGuest()
                ? redirect()->route('guest.dashboard')
                : redirect()->route('dashboard');
        }
        return view('landing');
    }

    // ─── Admin: show login ────────────────────────────────
    public function showLoginAdmin()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login-admin');
    }

    // ─── Admin: proses login ──────────────────────────────
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->boolean('remember'))) {
            if (!Auth::user()->isAdmin()) {
                Auth::logout();
                return back()->withErrors(['username' => 'Akun ini bukan akun admin.']);
            }
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
    }

    // ─── Guest: show login ────────────────────────────────
    public function showLoginGuest()
    {
        if (Auth::check() && Auth::user()->isGuest()) {
            return redirect()->route('guest.dashboard');
        }
        return view('auth.login-guest');
    }

    // ─── Guest: proses login via NIK ──────────────────────
    public function loginGuest(Request $request)
    {
        $request->validate([
            'nik_login' => 'required|string|max:20',
        ], [
            'nik_login.required' => 'NIK Login wajib diisi.',
        ]);

        $nikLogin = trim($request->nik_login);

        $karyawan = Karyawan::where('nik_login', $nikLogin)
                            ->where('keterangan', 'Aktif')
                            ->first();

        if (!$karyawan) {
            return back()->withErrors(['nik_login' => 'NIK Login tidak ditemukan atau karyawan tidak aktif.'])
                        ->withInput();
        }

        $guestRole = Role::where('name', 'guest')->first();
        $user = User::where('karyawan_nik', $karyawan->nik)->first();

        if (!$user) {
            $user = User::create([
                'username'     => $karyawan->nik_login,
                'nama'         => $karyawan->nama,
                'password'     => Hash::make($nikLogin . '_guest_' . $karyawan->nik),
                'role_id'      => $guestRole->id,
                'karyawan_nik' => $karyawan->nik,
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // ── Catat waktu login ──
        $karyawan->update(['last_login_at' => now()]);

        return redirect()->route('guest.dashboard');
    }

    // ─── Logout ───────────────────────────────────────────
    public function logout(Request $request)
    {
        $isGuest = Auth::check() && Auth::user()->isGuest();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}