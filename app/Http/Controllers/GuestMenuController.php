<?php

namespace App\Http\Controllers;

use App\Models\GuestMenu;
use Illuminate\Http\Request;

class GuestMenuController extends Controller
{
    // ─── List semua menu ──────────────────────────────────
    public function index()
    {
        $menus = GuestMenu::orderBy('urutan')->get();
        return view('guest-menu.index', compact('menus'));
    }

    // ─── Toggle aktif/nonaktif (AJAX) ─────────────────────
    public function toggle(Request $request, $id)
    {
        $menu = GuestMenu::findOrFail($id);
        $menu->is_active = !$menu->is_active;
        $menu->save();

        return response()->json([
            'message'   => 'Status menu diperbarui.',
            'is_active' => $menu->is_active,
        ]);
    }

    // ─── Update urutan (AJAX drag & drop) ─────────────────
    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $urutan => $id) {
            GuestMenu::where('id', $id)->update(['urutan' => $urutan + 1]);
        }

        return response()->json(['message' => 'Urutan disimpan.']);
    }

    // ─── Update berlaku_hingga (AJAX) ─────────────────────
    public function updateDeadline(Request $request, $id)
    {
        $request->validate([
            'berlaku_hingga' => 'nullable|date',
        ]);

        $menu = GuestMenu::findOrFail($id);
        $menu->berlaku_hingga = $request->berlaku_hingga ?: null;
        $menu->save();

        return response()->json([
            'message'        => 'Batas waktu disimpan.',
            'berlaku_hingga' => $menu->berlaku_hingga?->format('Y-m-d\TH:i'),
        ]);
    }
}