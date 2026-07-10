@extends('layouts.app')
@section('title', 'Gantt Chart')
@section('page-title', 'Gantt Chart 2026')

@section('content')

@php
$months  = [6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep'];
$curMonth = \Carbon\Carbon::now()->month;
$curWeek  = min((int) ceil(\Carbon\Carbon::now()->day / 7), 4);
@endphp

<style>
#ganttTableBody tr.dragging { opacity: .4; background: #f1f5f9; }
#ganttTableBody tr.drag-over-top { box-shadow: inset 0 2px 0 0 #16a34a; }
#ganttTableBody tr.drag-over-bottom { box-shadow: inset 0 -2px 0 0 #16a34a; }
.drag-handle { cursor: grab; }
.drag-handle:active { cursor: grabbing; }
#ganttTableBody tr.row-highlight { background: #fef9c3 !important; }
#ganttTableBody tr.row-highlight:hover { background: #fef08a !important; }
#ganttTableBody tr.row-highlight td { border-color: #fde68a !important; }

.k-toggle-wrap { display:flex; align-items:center; gap:10px; }
.k-toggle { position:relative; display:inline-block; width:42px; height:24px; flex-shrink:0; }
.k-toggle input { opacity:0; width:0; height:0; }
.k-toggle-slider { position:absolute; cursor:pointer; inset:0; background:#cbd5e1; border-radius:999px; transition:.2s; }
.k-toggle-slider::before { content:""; position:absolute; height:18px; width:18px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.2s; }
.k-toggle input:checked + .k-toggle-slider { background:#f59e0b; }
.k-toggle input:checked + .k-toggle-slider::before { transform:translateX(18px); }
</style>

{{-- HEADER --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:14px 20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:42px;height:42px;background:linear-gradient(135deg,#0b4614,#16a34a);
                            border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-chart-gantt" style="color:#fff;font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:15px;font-weight:700;color:#1e293b;">Gantt Chart 2026</div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;">
                        {{ $activities->count() }} activity &nbsp;·&nbsp; Jun – Sep 2026
                        &nbsp;·&nbsp;
                        <span style="display:inline-flex;align-items:center;gap:4px;">
                            <span style="width:10px;height:4px;border-radius:2px;background:#3b82f6;display:inline-block;"></span> Plan
                        </span>
                        &nbsp;
                        <span style="display:inline-flex;align-items:center;gap:4px;">
                            <span style="width:10px;height:4px;border-radius:2px;background:#16a34a;display:inline-block;"></span> Actual
                        </span>
                        &nbsp;
                        <span style="display:inline-flex;align-items:center;gap:4px;">
                            <span style="width:10px;height:10px;border-radius:2px;background:#fef08a;border:1px solid #fde68a;display:inline-block;"></span> Highlight
                        </span>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="openModalAdd()" style="gap:6px;">
                <i class="fa-solid fa-plus"></i> Tambah Activity
            </button>
        </div>
    </div>
</div>

{{-- GANTT TABLE --}}
<div class="card" style="overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                {{-- ROW 1: Bulan --}}
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th rowspan="2" style="width:36px;padding:8px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;border-right:1px solid #e2e8f0;vertical-align:middle;">No</th>
                    <th rowspan="2" style="width:230px;padding:8px 12px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;border-right:1px solid #e2e8f0;vertical-align:middle;">ACTIVITY</th>
<th rowspan="2" style="width:100px;padding:8px 12px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;border-right:1px solid #e2e8f0;vertical-align:middle;">DETAIL</th>
                    @foreach($months as $mn => $ml)
                    <th colspan="4"
                        style="padding:6px 4px;text-align:center;font-size:12px;font-weight:700;
                               color:{{ $mn === $curMonth ? '#16a34a' : '#64748b' }};
                               border-right:{{ $mn < 9 ? '2px solid #e2e8f0' : 'none' }};
                               background:{{ $mn === $curMonth ? '#f0fdf4' : '' }};">
                        {{ $ml }}
                    </th>
                    @endforeach
                    <th rowspan="2" style="width:80px;padding:8px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;border-left:2px solid #e2e8f0;vertical-align:middle;">AKSI</th>
                </tr>
                {{-- ROW 2: Minggu --}}
                <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                    @foreach($months as $mn => $ml)
                        @for($w = 1; $w <= 4; $w++)
                        <th style="padding:4px 2px;text-align:center;font-size:10px;font-weight:600;
                                   color:{{ ($mn === $curMonth && $w === $curWeek) ? '#16a34a' : '#b0bec5' }};
                                   border-right:{{ $w < 4 ? '1px solid #f1f5f9' : ($mn < 9 ? '2px solid #e2e8f0' : 'none') }};
                                   background:{{ ($mn === $curMonth && $w === $curWeek) ? '#bbf7d0' : '' }};
                                   min-width:36px;">
                            W{{ $w }}
                        </th>
                        @endfor
                    @endforeach
                </tr>
            </thead>
            <tbody id="ganttTableBody">
                @forelse($activities as $i => $act)
                <tr draggable="true" data-id="{{ $act->id }}"
                    class="{{ $act->is_highlight ? 'row-highlight' : '' }}"
                    style="border-bottom:1px solid #f1f5f9;transition:background .15s;"
                    @if(!$act->is_highlight)
                    onmouseenter="this.style.background='#fafafa'"
                    onmouseleave="this.style.background=''"
                    @endif>

                    <td class="drag-handle" style="padding:10px 8px;text-align:center;border-right:1px solid #f1f5f9;">
                        <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                            <i class="fa-solid fa-grip-vertical" style="color:#cbd5e1;font-size:12px;"></i>
                            <span class="row-no" style="font-size:12px;color:#94a3b8;">{{ $i + 1 }}</span>
                        </div>
                    </td>

                    <td style="padding:10px 12px;border-right:1px solid #f1f5f9;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            @if($act->is_highlight)
                                <i class="fa-solid fa-star" style="color:#f59e0b;font-size:11px;"></i>
                            @endif
                            <div style="font-size:13px;font-weight:600;color:#1e293b;line-height:1.3;">{{ $act->activity }}</div>
                        </div>
                    </td>

                    <td style="padding:10px 12px;border-right:1px solid #f1f5f9;">
                        <div style="font-size:12px;color:#64748b;">{{ $act->detail ?? '—' }}</div>
                    </td>

                    @foreach($months as $mn => $ml)
                        @for($w = 1; $w <= 4; $w++)
                        @php
                            $inPlan   = $act->inPlanRange($mn, $w);
                            $inActual = $act->inActualRange($mn, $w);
                            $isPlanStart = $act->plan_start_month == $mn && $act->plan_start_week == $w;
                            $isPlanEnd   = $act->plan_end_month   == $mn && $act->plan_end_week   == $w;
                            $isActStart  = $act->actual_start_month == $mn && $act->actual_start_week == $w;
                            $isActEnd    = $act->actual_end_month   == $mn && $act->actual_end_week   == $w;
                            $isCurWeek   = ($mn === $curMonth && $w === $curWeek);
                        @endphp
                        <td style="padding:6px 2px;
                                   border-right:{{ $w < 4 ? '1px solid #f8fafc' : ($mn < 9 ? '2px solid #e2e8f0' : 'none') }};
                                   background:{{ $isCurWeek ? 'rgba(22,163,74,.18)' : '' }};
                                   min-width:36px;">
                            <div style="display:flex;flex-direction:column;gap:3px;">
                                {{-- Plan bar --}}
                                <div style="height:9px;
                                            border-radius:{{ $isPlanStart && $isPlanEnd ? '5px' : ($isPlanStart ? '5px 0 0 5px' : ($isPlanEnd ? '0 5px 5px 0' : '0')) }};
                                            background:{{ $inPlan ? '#3b82f6' : 'transparent' }};">
                                </div>
                                {{-- Actual bar --}}
                                <div style="height:9px;
                                            border-radius:{{ $isActStart && $isActEnd ? '5px' : ($isActStart ? '5px 0 0 5px' : ($isActEnd ? '0 5px 5px 0' : '0')) }};
                                            background:{{ $inActual ? '#16a34a' : 'transparent' }};">
                                </div>
                            </div>
                        </td>
                        @endfor
                    @endforeach

                    <td style="padding:10px 8px;text-align:center;border-left:2px solid #f1f5f9;">
                        <div style="display:flex;gap:5px;justify-content:center;">
                            <button class="action-btn action-btn-warning"
                                    onclick="openModalEdit({{ json_encode($act) }})" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="action-btn action-btn-danger"
                                    onclick="confirmDelete({{ $act->id }},'{{ addslashes($act->activity) }}')" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="20" style="text-align:center;padding:50px;color:#94a3b8;">
                        <i class="fa-solid fa-chart-gantt" style="font-size:36px;display:block;margin-bottom:12px;opacity:.3;"></i>
                        <div style="font-weight:600;margin-bottom:4px;">Belum ada activity</div>
                        <div style="font-size:12px;">Klik "Tambah Activity" untuk mulai</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:10px 16px;background:#f8fafc;border-top:1px solid #e2e8f0;
                font-size:11px;color:#64748b;display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-circle" style="color:#16a34a;font-size:8px;"></i>
        Minggu berjalan:
        @if($curMonth >= 6 && $curMonth <= 9)
            <strong style="color:#16a34a;">W{{ $curWeek }} {{ $months[$curMonth] }} 2026</strong>
        @else
            <strong style="color:#94a3b8;">Di luar periode Jun–Sep 2026</strong>
        @endif
    </div>
</div>

{{-- ===== MODAL ADD ===== --}}
<div class="modal-overlay" id="modalAdd">
    <div class="modal-box" style="max-width:580px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-plus" style="color:#16a34a;margin-right:8px;"></i>Tambah Activity
                </div>
                <div class="modal-subtitle">Jun – Sep 2026</div>
            </div>
            <button class="modal-close" onclick="closeModalAdd()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form id="formAdd" onsubmit="submitAdd(event)">
                @csrf
                <div class="k-form-grid" style="grid-template-columns:1fr;">
                    <div class="k-form-group">
                        <label class="k-form-label">Activity <span class="required">*</span></label>
                        <input type="text" name="activity" id="add_activity" class="k-form-input"
                               placeholder="Nama activity..." maxlength="200" required>
                        <div class="k-form-error" id="aerr_activity"></div>
                    </div>
                </div>
                <div class="k-form-grid" style="grid-template-columns:1fr;">
                    <div class="k-form-group">
                        <label class="k-form-label">Detail</label>
                        <input type="text" name="detail" id="add_detail" class="k-form-input"
                               placeholder="Deskripsi singkat..." maxlength="300">
                    </div>
                </div>

                {{-- Highlight toggle --}}
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;margin-bottom:14px;">
                    <label class="k-toggle-wrap" style="cursor:pointer;margin:0;">
                        <span class="k-toggle">
                            <input type="checkbox" name="is_highlight" id="add_is_highlight">
                            <span class="k-toggle-slider"></span>
                        </span>
                        <span style="font-size:13px;">
                            <span style="font-weight:700;color:#92400e;"><i class="fa-solid fa-star" style="margin-right:4px;"></i>Highlight baris</span>
                            <span style="color:#78716c;display:block;font-size:11px;">Tandai activity penting, satu baris penuh jadi kuning</span>
                        </span>
                    </label>
                </div>

                {{-- Plan --}}
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px;margin-bottom:14px;">
                    <div style="font-size:12px;font-weight:700;color:#1d4ed8;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                        <span style="width:10px;height:4px;border-radius:2px;background:#3b82f6;display:inline-block;"></span>
                        Plan <span class="required">*</span>
                    </div>
                    <div class="k-form-grid" style="grid-template-columns:1fr 1fr 1fr 1fr;margin-bottom:0;gap:8px;">
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Mulai — Bulan</label>
                            <select name="plan_start_month" class="k-form-input" required>
                                <option value="">Pilih</option>
                                @foreach($months as $mn => $ml)<option value="{{ $mn }}">{{ $ml }}</option>@endforeach
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Mulai — Minggu</label>
                            <select name="plan_start_week" class="k-form-input" required>
                                <option value="">Pilih</option>
                                @for($w=1;$w<=4;$w++)<option value="{{ $w }}">W{{ $w }}</option>@endfor
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Selesai — Bulan</label>
                            <select name="plan_end_month" class="k-form-input" required>
                                <option value="">Pilih</option>
                                @foreach($months as $mn => $ml)<option value="{{ $mn }}">{{ $ml }}</option>@endforeach
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Selesai — Minggu</label>
                            <select name="plan_end_week" class="k-form-input" required>
                                <option value="">Pilih</option>
                                @for($w=1;$w<=4;$w++)<option value="{{ $w }}">W{{ $w }}</option>@endfor
                            </select>
                        </div>
                    </div>
                    <div class="k-form-error" id="aerr_plan_end_month" style="margin-top:6px;"></div>
                </div>

                {{-- Actual --}}
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px;margin-bottom:14px;">
                    <div style="font-size:12px;font-weight:700;color:#15803d;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                        <span style="width:10px;height:4px;border-radius:2px;background:#16a34a;display:inline-block;"></span>
                        Actual <span style="font-weight:400;color:#64748b;font-size:11px;">(opsional)</span>
                    </div>
                    <div class="k-form-grid" style="grid-template-columns:1fr 1fr 1fr 1fr;margin-bottom:0;gap:8px;">
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Mulai — Bulan</label>
                            <select name="actual_start_month" class="k-form-input">
                                <option value="">—</option>
                                @foreach($months as $mn => $ml)<option value="{{ $mn }}">{{ $ml }}</option>@endforeach
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Mulai — Minggu</label>
                            <select name="actual_start_week" class="k-form-input">
                                <option value="">—</option>
                                @for($w=1;$w<=4;$w++)<option value="{{ $w }}">W{{ $w }}</option>@endfor
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Selesai — Bulan</label>
                            <select name="actual_end_month" class="k-form-input">
                                <option value="">—</option>
                                @foreach($months as $mn => $ml)<option value="{{ $mn }}">{{ $ml }}</option>@endforeach
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Selesai — Minggu</label>
                            <select name="actual_end_week" class="k-form-input">
                                <option value="">—</option>
                                @for($w=1;$w<=4;$w++)<option value="{{ $w }}">W{{ $w }}</option>@endfor
                            </select>
                        </div>
                    </div>
                    <div class="k-form-error" id="aerr_actual_end_month" style="margin-top:6px;"></div>
                </div>

                <div class="k-form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModalAdd()">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAdd">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL EDIT ===== --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box" style="max-width:580px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color:#f59e0b;margin-right:8px;"></i>Edit Activity
                </div>
                <div class="modal-subtitle" id="editSub">—</div>
            </div>
            <button class="modal-close" onclick="closeModalEdit()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form id="formEdit" onsubmit="submitEdit(event)">
                @csrf
                <input type="hidden" id="edit_id">
                <div class="k-form-grid" style="grid-template-columns:1fr;">
                    <div class="k-form-group">
                        <label class="k-form-label">Activity <span class="required">*</span></label>
                        <input type="text" name="activity" id="edit_activity" class="k-form-input" maxlength="200" required>
                        <div class="k-form-error" id="eerr_activity"></div>
                    </div>
                </div>
                <div class="k-form-grid" style="grid-template-columns:1fr;">
                    <div class="k-form-group">
                        <label class="k-form-label">Detail</label>
                        <input type="text" name="detail" id="edit_detail" class="k-form-input" maxlength="300">
                    </div>
                </div>

                {{-- Highlight toggle --}}
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;margin-bottom:14px;">
                    <label class="k-toggle-wrap" style="cursor:pointer;margin:0;">
                        <span class="k-toggle">
                            <input type="checkbox" name="is_highlight" id="edit_is_highlight">
                            <span class="k-toggle-slider"></span>
                        </span>
                        <span style="font-size:13px;">
                            <span style="font-weight:700;color:#92400e;"><i class="fa-solid fa-star" style="margin-right:4px;"></i>Highlight baris</span>
                            <span style="color:#78716c;display:block;font-size:11px;">Tandai activity penting, satu baris penuh jadi kuning</span>
                        </span>
                    </label>
                </div>

                {{-- Plan --}}
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px;margin-bottom:14px;">
                    <div style="font-size:12px;font-weight:700;color:#1d4ed8;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                        <span style="width:10px;height:4px;border-radius:2px;background:#3b82f6;display:inline-block;"></span>
                        Plan <span class="required">*</span>
                    </div>
                    <div class="k-form-grid" style="grid-template-columns:1fr 1fr 1fr 1fr;margin-bottom:0;gap:8px;">
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Mulai — Bulan</label>
                            <select name="plan_start_month" id="edit_plan_start_month" class="k-form-input" required>
                                @foreach($months as $mn => $ml)<option value="{{ $mn }}">{{ $ml }}</option>@endforeach
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Mulai — Minggu</label>
                            <select name="plan_start_week" id="edit_plan_start_week" class="k-form-input" required>
                                @for($w=1;$w<=4;$w++)<option value="{{ $w }}">W{{ $w }}</option>@endfor
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Selesai — Bulan</label>
                            <select name="plan_end_month" id="edit_plan_end_month" class="k-form-input" required>
                                @foreach($months as $mn => $ml)<option value="{{ $mn }}">{{ $ml }}</option>@endforeach
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Selesai — Minggu</label>
                            <select name="plan_end_week" id="edit_plan_end_week" class="k-form-input" required>
                                @for($w=1;$w<=4;$w++)<option value="{{ $w }}">W{{ $w }}</option>@endfor
                            </select>
                        </div>
                    </div>
                    <div class="k-form-error" id="eerr_plan_end_month" style="margin-top:6px;"></div>
                </div>

                {{-- Actual --}}
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px;margin-bottom:14px;">
                    <div style="font-size:12px;font-weight:700;color:#15803d;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                        <span style="width:10px;height:4px;border-radius:2px;background:#16a34a;display:inline-block;"></span>
                        Actual <span style="font-weight:400;color:#64748b;font-size:11px;">(opsional)</span>
                    </div>
                    <div class="k-form-grid" style="grid-template-columns:1fr 1fr 1fr 1fr;margin-bottom:0;gap:8px;">
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Mulai — Bulan</label>
                            <select name="actual_start_month" id="edit_actual_start_month" class="k-form-input">
                                <option value="">—</option>
                                @foreach($months as $mn => $ml)<option value="{{ $mn }}">{{ $ml }}</option>@endforeach
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Mulai — Minggu</label>
                            <select name="actual_start_week" id="edit_actual_start_week" class="k-form-input">
                                <option value="">—</option>
                                @for($w=1;$w<=4;$w++)<option value="{{ $w }}">W{{ $w }}</option>@endfor
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Selesai — Bulan</label>
                            <select name="actual_end_month" id="edit_actual_end_month" class="k-form-input">
                                <option value="">—</option>
                                @foreach($months as $mn => $ml)<option value="{{ $mn }}">{{ $ml }}</option>@endforeach
                            </select>
                        </div>
                        <div class="k-form-group" style="margin-bottom:0;">
                            <label class="k-form-label" style="font-size:11px;">Selesai — Minggu</label>
                            <select name="actual_end_week" id="edit_actual_end_week" class="k-form-input">
                                <option value="">—</option>
                                @for($w=1;$w<=4;$w++)<option value="{{ $w }}">W{{ $w }}</option>@endfor
                            </select>
                        </div>
                    </div>
                    <div class="k-form-error" id="eerr_actual_end_month" style="margin-top:6px;"></div>
                </div>

                <div class="k-form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModalEdit()">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEdit">
                        <i class="fa-solid fa-floppy-disk"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL DELETE ===== --}}
<div class="modal-overlay" id="modalDelete">
    <div class="modal-box" style="max-width:400px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;margin-right:8px;"></i>Hapus Activity
                </div>
                <div class="modal-subtitle">Tindakan ini tidak bisa dibatalkan</div>
            </div>
            <button class="modal-close" onclick="closeModalDelete()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p style="color:#475569;font-size:14px;margin-bottom:20px;">
                Yakin ingin menghapus activity <strong id="deleteActivityName" style="color:#1e293b;"></strong>?
            </p>
            <div class="k-form-actions">
                <button type="button" class="btn btn-outline" onclick="closeModalDelete()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" class="btn k-btn-danger" id="btnConfirmDelete" onclick="doDelete()">
                    <i class="fa-solid fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

function showToast(msg, type = 'success') {
    let wrap = document.getElementById('toastContainer');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'toastContainer';
        wrap.className = 'toast-container';
        document.body.appendChild(wrap);
    }
    const icon  = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<i class="fa-solid ${icon}"></i>${msg}`;
    wrap.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}
function clearErrors(prefix) {
    document.querySelectorAll(`[id^="${prefix}err_"]`).forEach(el => el.textContent = '');
    document.querySelectorAll('.k-form-input.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}
function showErrors(errors, prefix, formId) {
    Object.entries(errors).forEach(([field, msgs]) => {
        const errEl = document.getElementById(`${prefix}err_${field}`);
        if (errEl) errEl.textContent = msgs[0];
        const input = document.querySelector(`#${formId} [name="${field}"]`);
        if (input) input.classList.add('is-invalid');
    });
}

// ── ADD ────────────────────────────────────
function openModalAdd() {
    clearErrors('a');
    document.getElementById('formAdd').reset();
    document.getElementById('modalAdd').classList.add('show');
    setTimeout(() => document.getElementById('add_activity').focus(), 100);
}
function closeModalAdd() { document.getElementById('modalAdd').classList.remove('show'); }
function submitAdd(e) {
    e.preventDefault();
    clearErrors('a');
    const btn = document.getElementById('btnSubmitAdd');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

    const fd = new FormData(document.getElementById('formAdd'));
    fd.set('is_highlight', document.getElementById('add_is_highlight').checked ? '1' : '0');

    fetch('{{ route('gantt.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: fd,
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 200 || status === 201) {
            closeModalAdd();
            showToast(body.message ?? 'Berhasil ditambahkan!');
            setTimeout(() => location.reload(), 700);
        } else if (status === 422) {
            showErrors(body.errors, 'a', 'formAdd');
        } else {
            showToast(body.message ?? 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => { btn.classList.remove('loading'); btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan'; });
}

// ── EDIT ───────────────────────────────────
function openModalEdit(data) {
    clearErrors('e');
    document.getElementById('edit_id').value                  = data.id;
    document.getElementById('edit_activity').value           = data.activity ?? '';
    document.getElementById('edit_detail').value             = data.detail   ?? '';
    document.getElementById('edit_is_highlight').checked     = !!data.is_highlight;
    document.getElementById('edit_plan_start_month').value   = data.plan_start_month  ?? '';
    document.getElementById('edit_plan_start_week').value    = data.plan_start_week   ?? '';
    document.getElementById('edit_plan_end_month').value     = data.plan_end_month    ?? '';
    document.getElementById('edit_plan_end_week').value      = data.plan_end_week     ?? '';
    document.getElementById('edit_actual_start_month').value = data.actual_start_month ?? '';
    document.getElementById('edit_actual_start_week').value  = data.actual_start_week  ?? '';
    document.getElementById('edit_actual_end_month').value   = data.actual_end_month   ?? '';
    document.getElementById('edit_actual_end_week').value    = data.actual_end_week    ?? '';
    document.getElementById('editSub').textContent           = data.activity;
    document.getElementById('modalEdit').classList.add('show');
}
function closeModalEdit() { document.getElementById('modalEdit').classList.remove('show'); }
function submitEdit(e) {
    e.preventDefault();
    clearErrors('e');
    const btn = document.getElementById('btnSubmitEdit');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    const id = document.getElementById('edit_id').value;
    const fd = new FormData(document.getElementById('formEdit'));
    fd.set('is_highlight', document.getElementById('edit_is_highlight').checked ? '1' : '0');
    fd.append('_method', 'PUT');
    fetch(`/gantt/${id}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: fd,
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 200) {
            closeModalEdit();
            showToast(body.message ?? 'Berhasil diupdate!');
            setTimeout(() => location.reload(), 700);
        } else if (status === 422) {
            showErrors(body.errors, 'e', 'formEdit');
        } else {
            showToast(body.message ?? 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => { btn.classList.remove('loading'); btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Update'; });
}

// ── DELETE ─────────────────────────────────
let deleteTargetId = null;
function confirmDelete(id, nama) {
    deleteTargetId = id;
    document.getElementById('deleteActivityName').textContent = nama;
    document.getElementById('modalDelete').classList.add('show');
}
function closeModalDelete() { deleteTargetId = null; document.getElementById('modalDelete').classList.remove('show'); }
function doDelete() {
    if (!deleteTargetId) return;
    const btn = document.getElementById('btnConfirmDelete');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghapus...';
    fetch(`/gantt/${deleteTargetId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 200) {
            closeModalDelete();
            showToast(body.message ?? 'Berhasil dihapus!');
            setTimeout(() => location.reload(), 700);
        } else {
            showToast(body.message ?? 'Gagal menghapus', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => { btn.classList.remove('loading'); btn.innerHTML = '<i class="fa-solid fa-trash"></i> Hapus'; });
}

['modalAdd','modalEdit','modalDelete'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') ['modalAdd','modalEdit','modalDelete'].forEach(id =>
        document.getElementById(id).classList.remove('show'));
});

// ── DRAG & DROP REORDER ────────────────────
let draggedRow = null;

function initDragDrop() {
    document.querySelectorAll('#ganttTableBody tr[draggable="true"]').forEach(row => {
        row.addEventListener('dragstart', handleDragStart);
        row.addEventListener('dragover', handleDragOver);
        row.addEventListener('dragleave', handleDragLeave);
        row.addEventListener('drop', handleDrop);
        row.addEventListener('dragend', handleDragEnd);
    });
}

function handleDragStart(e) {
    draggedRow = this;
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', this.dataset.id);
}

function handleDragOver(e) {
    e.preventDefault();
    if (this === draggedRow) return false;
    const rect = this.getBoundingClientRect();
    const midpoint = rect.top + rect.height / 2;
    this.classList.remove('drag-over-top', 'drag-over-bottom');
    this.classList.add(e.clientY < midpoint ? 'drag-over-top' : 'drag-over-bottom');
    return false;
}

function handleDragLeave() {
    this.classList.remove('drag-over-top', 'drag-over-bottom');
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    if (this === draggedRow) return false;

    const rect = this.getBoundingClientRect();
    const midpoint = rect.top + rect.height / 2;
    const tbody = this.parentNode;

    if (e.clientY < midpoint) {
        tbody.insertBefore(draggedRow, this);
    } else {
        tbody.insertBefore(draggedRow, this.nextSibling);
    }

    this.classList.remove('drag-over-top', 'drag-over-bottom');
    renumberRows();
    saveNewOrder();
    return false;
}

function handleDragEnd() {
    this.classList.remove('dragging');
    document.querySelectorAll('#ganttTableBody tr').forEach(r =>
        r.classList.remove('drag-over-top', 'drag-over-bottom'));
}

function renumberRows() {
    document.querySelectorAll('#ganttTableBody tr .row-no').forEach((el, idx) => {
        el.textContent = idx + 1;
    });
}

function saveNewOrder() {
    const ids = Array.from(document.querySelectorAll('#ganttTableBody tr')).map(r => r.dataset.id);
    fetch('{{ route('gantt.reorder') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ ids }),
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 200) {
            showToast(body.message ?? 'Urutan berhasil disimpan!');
        } else {
            showToast(body.message ?? 'Gagal menyimpan urutan', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'));
}

document.addEventListener('DOMContentLoaded', initDragDrop);
</script>
@endpush