<?php

namespace App\Http\Controllers;

use App\Models\Rundown;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RundownController extends Controller
{
    // ── HELPER ─────────────────────────────────────────────────────────────────
    private function hitungDurasi(string $mulai, string $selesai): string
    {
        $m    = Carbon::createFromFormat('H:i', $mulai);
        $s    = Carbon::createFromFormat('H:i', $selesai);
        $diff = $m->diffInMinutes($s);
        return sprintf('%02d:%02d', intdiv($diff, 60), $diff % 60);
    }

    // ── INDEX ──────────────────────────────────────────────────────────────────
    public function index()
    {
        $rundowns = Rundown::orderBy('mulai')->get();

        $lastSelesai = $rundowns->last()?->selesai ?? '07:00:00';
        $nextMulai   = Carbon::createFromFormat('H:i:s', $lastSelesai)
                            ->addMinute()
                            ->format('H:i');

        return view('rundowns.index', compact('rundowns', 'nextMulai'));
    }

    // ── BULK INSERT ────────────────────────────────────────────────────────────
    public function bulk(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.kegiatan'   => 'required|string|max:200',
            'items.*.mulai'      => 'required|date_format:H:i',
            'items.*.selesai'    => 'required|date_format:H:i|after:items.*.mulai',
            'items.*.pic'        => 'nullable|string|max:150',
            'items.*.properti'   => 'nullable|string|max:200',
            'items.*.keterangan' => 'nullable|string|max:1000',
        ], [
            'items.*.selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        $maxUrutan = Rundown::max('urutan') ?? 0;

        foreach ($request->items as $i => $item) {
            Rundown::create([
                'kegiatan'   => $item['kegiatan'],
                'mulai'      => $item['mulai'],
                'selesai'    => $item['selesai'],
                'durasi'     => $this->hitungDurasi($item['mulai'], $item['selesai']),
                'pic'        => $item['pic']        ?? null,
                'properti'   => $item['properti']   ?? null,
                'keterangan' => $item['keterangan'] ?? null,
                'urutan'     => $maxUrutan + $i + 1,
            ]);
        }

        $count = count($request->items);

        return response()->json([
            'message' => "{$count} kegiatan berhasil disimpan!",
        ]);
    }

    // ── EDIT (JSON untuk inline) ───────────────────────────────────────────────
    public function edit(Rundown $rundown)
    {
        return response()->json(['rundown' => $rundown]);
    }

    // ── UPDATE ─────────────────────────────────────────────────────────────────
    public function update(Request $request, Rundown $rundown)
    {
        $validated = $request->validate([
            'kegiatan'   => 'required|string|max:200',
            'mulai'      => 'required|date_format:H:i',
            'selesai'    => 'required|date_format:H:i|after:mulai',
            'pic'        => 'nullable|string|max:150',
            'properti'   => 'nullable|string|max:200',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        $validated['durasi'] = $this->hitungDurasi($validated['mulai'], $validated['selesai']);

        $rundown->update($validated);

        return response()->json([
            'message' => 'Kegiatan berhasil diupdate!',
            'rundown' => $rundown->fresh(),
        ]);
    }

    // ── DESTROY ────────────────────────────────────────────────────────────────
    public function destroy(Rundown $rundown)
    {
        $rundown->delete();

        return response()->json([
            'message' => 'Kegiatan berhasil dihapus!',
        ]);
    }
}