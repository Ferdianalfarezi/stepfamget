<?php

namespace App\Http\Controllers;

use App\Imports\SupplierImport;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    // ─────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('alamat', 'like', "%$search%");
            });
        }

        $suppliers = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    // ─────────────────────────────────────────
    // STORE (AJAX)
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'   => 'required|string|max:150',
            'alamat' => 'nullable|string',
        ], [
            'nama.required' => 'Nama supplier wajib diisi.',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json([
            'message'  => 'Supplier berhasil ditambahkan.',
            'supplier' => $supplier,
        ], 201);
    }

    // ─────────────────────────────────────────
    // EDIT (AJAX)
    // ─────────────────────────────────────────
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['supplier' => $supplier]);
        }

        return view('suppliers.index', compact('supplier'));
    }

    // ─────────────────────────────────────────
    // UPDATE (AJAX)
    // ─────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'nama'   => 'required|string|max:150',
            'alamat' => 'nullable|string',
        ], [
            'nama.required' => 'Nama supplier wajib diisi.',
        ]);

        $supplier->update($validated);

        return response()->json([
            'message'  => 'Data supplier berhasil diupdate.',
            'supplier' => $supplier->fresh(),
        ]);
    }

    // ─────────────────────────────────────────
    // DESTROY (AJAX)
    // ─────────────────────────────────────────
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json([
            'message' => 'Supplier berhasil dihapus.',
        ]);
    }

    // ─────────────────────────────────────────
    // IMPORT EXCEL (AJAX)
    // ─────────────────────────────────────────
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File wajib dipilih.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $import = new SupplierImport();
            Excel::import($import, $request->file('file'));

            return response()->json([
                'message' => 'Import berhasil.',
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors   = collect($failures)->map(fn($f) =>
                "Baris {$f->row()}: " . implode(', ', $f->errors())
            )->toArray();

            return response()->json([
                'message' => 'Import gagal karena validasi.',
                'errors'  => $errors,
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Import gagal: ' . $th->getMessage(),
            ], 500);
        }
    }
}