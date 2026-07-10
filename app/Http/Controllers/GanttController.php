<?php

namespace App\Http\Controllers;

use App\Models\GanttActivity;
use Illuminate\Http\Request;

class GanttController extends Controller
{
    public function index()
    {
        $activities = GanttActivity::orderBy('urutan')->orderBy('id')->get();
        return view('gantt.index', compact('activities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity'           => 'required|string|max:200',
            'detail'             => 'nullable|string|max:300',
            'is_highlight'       => 'nullable|boolean',
            'plan_start_month'   => 'required|integer|min:1|max:12',
            'plan_start_week'    => 'required|integer|min:1|max:4',
            'plan_end_month'     => 'required|integer|min:1|max:12',
            'plan_end_week'      => 'required|integer|min:1|max:4',
            'actual_start_month' => 'nullable|integer|min:1|max:12',
            'actual_start_week'  => 'nullable|integer|min:1|max:4',
            'actual_end_month'   => 'nullable|integer|min:1|max:12',
            'actual_end_week'    => 'nullable|integer|min:1|max:4',
        ]);

        $validated['is_highlight'] = $request->boolean('is_highlight');

        $planStart = GanttActivity::toGlobal($validated['plan_start_month'], $validated['plan_start_week']);
        $planEnd   = GanttActivity::toGlobal($validated['plan_end_month'],   $validated['plan_end_week']);
        if ($planEnd < $planStart) {
            return response()->json([
                'errors' => ['plan_end_month' => ['Selesai plan harus setelah mulai plan.']]
            ], 422);
        }

        if (!empty($validated['actual_start_month']) && !empty($validated['actual_end_month'])) {
            $actStart = GanttActivity::toGlobal($validated['actual_start_month'], $validated['actual_start_week'] ?? 1);
            $actEnd   = GanttActivity::toGlobal($validated['actual_end_month'],   $validated['actual_end_week']   ?? 4);
            if ($actEnd < $actStart) {
                return response()->json([
                    'errors' => ['actual_end_month' => ['Selesai actual harus setelah mulai actual.']]
                ], 422);
            }
        }

        $validated['urutan'] = GanttActivity::max('urutan') + 1;
        $activity = GanttActivity::create($validated);

        return response()->json([
            'message'  => 'Activity berhasil ditambahkan!',
            'activity' => $activity,
        ]);
    }

    public function update(Request $request, GanttActivity $ganttActivity)
    {
        $validated = $request->validate([
            'activity'           => 'required|string|max:200',
            'detail'             => 'nullable|string|max:300',
            'is_highlight'       => 'nullable|boolean',
            'plan_start_month'   => 'required|integer|min:1|max:12',
            'plan_start_week'    => 'required|integer|min:1|max:4',
            'plan_end_month'     => 'required|integer|min:1|max:12',
            'plan_end_week'      => 'required|integer|min:1|max:4',
            'actual_start_month' => 'nullable|integer|min:1|max:12',
            'actual_start_week'  => 'nullable|integer|min:1|max:4',
            'actual_end_month'   => 'nullable|integer|min:1|max:12',
            'actual_end_week'    => 'nullable|integer|min:1|max:4',
        ]);

        $validated['is_highlight'] = $request->boolean('is_highlight');

        $planStart = GanttActivity::toGlobal($validated['plan_start_month'], $validated['plan_start_week']);
        $planEnd   = GanttActivity::toGlobal($validated['plan_end_month'],   $validated['plan_end_week']);
        if ($planEnd < $planStart) {
            return response()->json([
                'errors' => ['plan_end_month' => ['Selesai plan harus setelah mulai plan.']]
            ], 422);
        }

        if (!empty($validated['actual_start_month']) && !empty($validated['actual_end_month'])) {
            $actStart = GanttActivity::toGlobal($validated['actual_start_month'], $validated['actual_start_week'] ?? 1);
            $actEnd   = GanttActivity::toGlobal($validated['actual_end_month'],   $validated['actual_end_week']   ?? 4);
            if ($actEnd < $actStart) {
                return response()->json([
                    'errors' => ['actual_end_month' => ['Selesai actual harus setelah mulai actual.']]
                ], 422);
            }
        }

        $ganttActivity->update($validated);

        return response()->json([
            'message'  => 'Activity berhasil diupdate!',
            'activity' => $ganttActivity->fresh(),
        ]);
    }

    public function destroy(GanttActivity $ganttActivity)
    {
        $ganttActivity->delete();
        return response()->json(['message' => 'Activity berhasil dihapus!']);
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:gantt_activities,id',
        ]);

        foreach ($validated['ids'] as $index => $id) {
            GanttActivity::where('id', $id)->update(['urutan' => $index + 1]);
        }

        return response()->json(['message' => 'Urutan berhasil disimpan!']);
    }
}