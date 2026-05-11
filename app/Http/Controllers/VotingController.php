<?php

namespace App\Http\Controllers;

use App\Models\VotingTempat;
use App\Models\VotingVote;
use App\Models\GuestMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotingController extends Controller
{
    // ─── ADMIN: halaman kelola kandidat ──────────────────
    public function index()
    {
        $tempats    = VotingTempat::withCount('votes')->orderByDesc('votes_count')->get();
        $totalVotes = VotingVote::count();
        return view('voting.index', compact('tempats', 'totalVotes'));
    }

    // ─── ADMIN: tambah kandidat ───────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:150',
            'lokasi'    => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $tempat = VotingTempat::create($request->only('nama', 'lokasi', 'deskripsi'));
        return response()->json(['message' => 'Tempat ditambahkan.', 'tempat' => $tempat], 201);
    }

    // ─── ADMIN: edit kandidat ─────────────────────────────
    public function update(Request $request, $id)
    {
        $tempat = VotingTempat::findOrFail($id);
        $request->validate([
            'nama'      => 'required|string|max:150',
            'lokasi'    => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);
        $tempat->update($request->only('nama', 'lokasi', 'deskripsi'));
        return response()->json(['message' => 'Tempat diupdate.', 'tempat' => $tempat]);
    }

    // ─── ADMIN: hapus kandidat ────────────────────────────
    public function destroy($id)
    {
        $tempat = VotingTempat::findOrFail($id);
        $tempat->votes()->delete();
        $tempat->delete();
        return response()->json(['message' => 'Tempat dihapus.']);
    }

    // ─── ADMIN: reset semua votes ─────────────────────────
    public function resetVotes()
    {
        VotingVote::truncate();
        return response()->json(['message' => 'Votes direset.']);
    }

    // ─── GUEST: halaman voting ────────────────────────────
    public function guestIndex()
    {
        $karyawan   = Auth::user()->karyawan;
        $tempats    = VotingTempat::where('is_active', true)->withCount('votes')->get();
        $totalVotes = VotingVote::count();
        $myVote     = VotingVote::where('karyawan_nik', $karyawan->nik)->first();
        $menu       = GuestMenu::where('key', 'voting')->first();

        return view('guest.partials.voting', compact('karyawan', 'tempats', 'totalVotes', 'myVote', 'menu'));
    }

    // ─── GUEST: submit vote ───────────────────────────────
    public function vote(Request $request)
    {
        $request->validate(['tempat_id' => 'required|exists:voting_tempat,id']);

        $karyawan = Auth::user()->karyawan;
        $existing = VotingVote::where('karyawan_nik', $karyawan->nik)->first();

        if ($existing) {
            $existing->update(['voting_tempat_id' => $request->tempat_id]);
            $message = 'Vote berhasil diubah.';
        } else {
            VotingVote::create([
                'karyawan_nik'     => $karyawan->nik,
                'voting_tempat_id' => $request->tempat_id,
            ]);
            $message = 'Vote berhasil disimpan!';
        }

        $tempats    = VotingTempat::where('is_active', true)->withCount('votes')->get();
        $totalVotes = VotingVote::count();

        return response()->json([
            'message'    => $message,
            'my_vote'    => $request->tempat_id,
            'totalVotes' => $totalVotes,
            'tempats'    => $tempats->map(fn($t) => [
                'id'          => $t->id,
                'votes_count' => $t->votes_count,
                'pct'         => $totalVotes > 0 ? round($t->votes_count / $totalVotes * 100) : 0,
            ]),
        ]);
    }
}