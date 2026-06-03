<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanBarang;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PenerimaanBarangController extends Controller
{
    // ─────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = PenerimaanBarang::with('supplier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('barang', 'like', "%$search%")
                  ->orWhere('pic', 'like', "%$search%")
                  ->orWhereHas('supplier', fn($sq) =>
                      $sq->where('nama', 'like', "%$search%")
                  );
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $items     = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('nama')->get(['id', 'nama']);

        return view('penerimaan-barang.index', compact('items', 'suppliers'));
    }

    // ─────────────────────────────────────────
    // STORE (AJAX)
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'pic'                  => 'required|string|max:150',
            'items'                => 'required|array|min:1',
            'items.*.barang'       => 'required|string|max:200',
            'items.*.harga'        => 'required|numeric|min:0',
            'items.*.qty'          => 'required|numeric|min:0.01',
        ], [
            'supplier_id.required'       => 'Supplier wajib dipilih.',
            'supplier_id.exists'         => 'Supplier tidak valid.',
            'pic.required'               => 'PIC wajib diisi.',
            'items.required'             => 'Minimal 1 item harus diisi.',
            'items.*.barang.required'    => 'Nama barang wajib diisi.',
            'items.*.harga.required'     => 'Harga wajib diisi.',
            'items.*.harga.numeric'      => 'Harga harus berupa angka.',
            'items.*.qty.required'       => 'Qty wajib diisi.',
            'items.*.qty.numeric'        => 'Qty harus berupa angka.',
            'items.*.qty.min'            => 'Qty minimal 0.01.',
        ]);

        $inserted = [];
        foreach ($request->items as $item) {
            $inserted[] = PenerimaanBarang::create([
                'supplier_id' => $request->supplier_id,
                'pic'         => $request->pic,
                'barang'      => $item['barang'],
                'harga'       => $item['harga'],
                'qty'         => $item['qty'],
                'total'       => $item['harga'] * $item['qty'],
            ]);
        }

        return response()->json([
            'message' => 'Berhasil menambahkan ' . count($inserted) . ' item.',
            'count'   => count($inserted),
        ], 201);
    }

    // ─────────────────────────────────────────
    // EDIT (AJAX)
    // ─────────────────────────────────────────
    public function edit($id)
    {
        $item = PenerimaanBarang::with('supplier')->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['item' => $item]);
        }

        return view('penerimaan-barang.index', compact('item'));
    }

    // ─────────────────────────────────────────
    // UPDATE (AJAX)
    // ─────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $item = PenerimaanBarang::findOrFail($id);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'barang'      => 'required|string|max:200',
            'harga'       => 'required|numeric|min:0',
            'qty'         => 'required|numeric|min:0.01',
            'pic'         => 'required|string|max:150',
        ], [
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'supplier_id.exists'   => 'Supplier tidak valid.',
            'barang.required'      => 'Nama barang wajib diisi.',
            'harga.required'       => 'Harga wajib diisi.',
            'harga.numeric'        => 'Harga harus berupa angka.',
            'qty.required'         => 'Qty wajib diisi.',
            'qty.numeric'          => 'Qty harus berupa angka.',
            'qty.min'              => 'Qty minimal 0.01.',
            'pic.required'         => 'PIC wajib diisi.',
        ]);

        $validated['total'] = $validated['harga'] * $validated['qty'];

        $item->update($validated);

        return response()->json([
            'message' => 'Data penerimaan barang berhasil diupdate.',
            'item'    => $item->fresh('supplier'),
        ]);
    }

    // ─────────────────────────────────────────
    // DESTROY (AJAX)
    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $item = PenerimaanBarang::findOrFail($id);
        $item->delete();

        return response()->json([
            'message' => 'Data penerimaan barang berhasil dihapus.',
        ]);
    }

    // ─────────────────────────────────────────
    // SEARCH SUPPLIER (AJAX)
    // ─────────────────────────────────────────
    public function searchSupplier(Request $request)
    {
        $q = $request->get('q', '');

        $suppliers = Supplier::where('nama', 'like', "%$q%")
            ->orderBy('nama')
            ->limit(20)
            ->get(['id', 'nama', 'alamat']);

        return response()->json(['results' => $suppliers]);
    }
}