<?php

namespace App\Http\Controllers;

use App\Models\Konsumsi;
use Illuminate\Http\Request;

class KonsumsiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $konsumsis = Konsumsi::when($search, fn($q) =>
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('satuan', 'like', "%{$search}%")
            )
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        // Hitung sekali, dipakai semua row
        $qtyHadir = Konsumsi::getQtyHadir();
        $qtySemua = Konsumsi::getQtySemua();

        return view('konsumsis.index', compact('konsumsis', 'qtyHadir', 'qtySemua', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'   => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'spare'  => 'required|integer',
        ]);

        Konsumsi::create($validated);

        return response()->json(['message' => 'Konsumsi berhasil ditambahkan.']);
    }

    public function show(Konsumsi $konsumsi)
    {
        $qtyHadir = Konsumsi::getQtyHadir();
        $qtySemua = Konsumsi::getQtySemua();

        return response()->json([
            'id'          => $konsumsi->id,
            'nama'        => $konsumsi->nama,
            'satuan'      => $konsumsi->satuan,
            'spare'       => $konsumsi->spare,
            'qty'         => $qtyHadir,
            'total'       => $qtyHadir + $konsumsi->spare,
            'qty_semua'   => $qtySemua,
            'total_semua' => $qtySemua + $konsumsi->spare,
        ]);
    }

    public function update(Request $request, Konsumsi $konsumsi)
    {
        $validated = $request->validate([
            'nama'   => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'spare'  => 'required|integer',
        ]);

        $konsumsi->update($validated);

        return response()->json(['message' => 'Konsumsi berhasil diperbarui.']);
    }

    public function destroy(Konsumsi $konsumsi)
    {
        $konsumsi->delete();
        return response()->json(['message' => 'Konsumsi berhasil dihapus.']);
    }
}