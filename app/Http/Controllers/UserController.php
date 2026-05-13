<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'admin'))
            ->orderBy('nama');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('username', 'like', "%$s%")
                ->orWhere('nama', 'like', "%$s%")
            );
        }

        $users    = $query->paginate(15)->withQueryString();
        $adminRole = Role::where('name', 'admin')->first();

        return view('users.index', compact('users', 'adminRole'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'nama'     => 'required|string|max:100',
            'password' => 'required|string|min:6',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'nama.required'     => 'Nama wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $adminRole = Role::where('name', 'admin')->firstOrFail();

        User::create([
            'username'     => $request->username,
            'nama'         => $request->nama,
            'password'     => Hash::make($request->password),
            'role_id'      => $adminRole->id,
            'karyawan_nik' => null,
        ]);

        return response()->json(['message' => 'User berhasil ditambahkan.'], 201);
    }

    public function edit($id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json(['user' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'nama'     => 'required|string|max:100',
            'password' => 'nullable|string|min:6',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'nama.required'     => 'Nama wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $data = [
            'username' => $request->username,
            'nama'     => $request->nama,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['message' => 'User berhasil diupdate.']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Jangan hapus diri sendiri
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri.'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus.']);
    }
}