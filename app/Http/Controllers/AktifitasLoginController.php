<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class AktifitasLoginController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::whereNotNull('last_login_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('departemen', 'like', "%$search%")
                  ->orWhere('nik', 'like', "%$search%");
            });
        }

        if ($request->filled('departemen')) {
            $query->where('departemen', $request->departemen);
        }

        $items         = $query->orderByDesc('last_login_at')->paginate(20)->withQueryString();
        $departemenList = Karyawan::whereNotNull('last_login_at')
                            ->distinct()->pluck('departemen')->sort()->values();

        return view('aktifitas-login.index', compact('items', 'departemenList'));
    }
}