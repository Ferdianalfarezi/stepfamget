@extends('layouts.app')
@section('title', 'Rundown Acara')
@section('page-title', 'Rundown')

@section('content')

{{-- ── TIMELINE HEADER ── --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:14px 20px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:42px;height:42px;background:linear-gradient(135deg,#0b4614,#16a34a);
                        border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-calendar-days" style="color:#fff;font-size:18px;"></i>
            </div>
            <div>
                <div style="font-size:15px;font-weight:700;color:#1e293b;">Timeline Acara</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px;">
                    {{ $rundowns->count() }} kegiatan &nbsp;·&nbsp;
                    @if($rundowns->count())
                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $rundowns->first()->mulai)->format('H:i') }}
                        –
                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $rundowns->last()->selesai)->format('H:i') }}
                    @else
                        Belum ada kegiatan
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── TABLE CARD ── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-list-check" style="color:#0b4614;margin-right:6px;"></i>Rundown Acara
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $rundowns->count() }} kegiatan terjadwal
            </div>
        </div>
    </div>

    <div class="table-wrap">
        <table id="rundownTable">
            <thead>
                <tr>
                    <th style="width:44px;">No</th>
                    <th style="width:100px;">Mulai</th>
                    <th style="width:100px;">Selesai</th>
                    <th style="width:76px;">Durasi</th>
                    <th>Kegiatan</th>
                    <th style="width:140px;">PIC</th>
                    <th style="width:160px;">Properti</th>
                    <th>Keterangan</th>
                    @if(auth()->user()->nama !== 'Hitz')
                        <th style="width:110px;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody id="rundownBody">
                @forelse($rundowns as $i => $r)

                {{-- DATA ROW --}}
                <tr class="data-row" data-id="{{ $r->id }}"
                    data-mulai="{{ \Carbon\Carbon::createFromFormat('H:i:s', $r->mulai)->format('H:i') }}"
                    data-selesai="{{ \Carbon\Carbon::createFromFormat('H:i:s', $r->selesai)->format('H:i') }}"
                    data-kegiatan="{{ $r->kegiatan }}"
                    data-pic="{{ $r->pic ?? '' }}"
                    data-properti="{{ $r->properti ?? '' }}"
                    data-keterangan="{{ $r->keterangan ?? '' }}">

                    <td class="td-no" style="color:#94a3b8;font-size:12px;text-align:center;">{{ $i + 1 }}</td>

                    <td class="td-mulai">
                        <div class="time-badge time-badge-green">
                            <i class="fa-regular fa-clock" style="color:#16a34a;font-size:11px;"></i>
                            <span style="font-weight:700;font-size:13px;color:#15803d;font-family:monospace;">
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $r->mulai)->format('H:i') }}
                            </span>
                        </div>
                    </td>

                    <td class="td-selesai">
                        <div class="time-badge time-badge-orange">
                            <i class="fa-regular fa-clock" style="color:#ea580c;font-size:11px;"></i>
                            <span style="font-weight:700;font-size:13px;color:#c2410c;font-family:monospace;">
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $r->selesai)->format('H:i') }}
                            </span>
                        </div>
                    </td>

                    <td class="td-durasi" style="text-align:center;">
                        <span class="durasi-badge">{{ $r->durasi }}</span>
                    </td>

                    <td class="td-kegiatan">
                        <div style="font-weight:600;font-size:13.5px;color:#1e293b;">{{ $r->kegiatan }}</div>
                    </td>

                    <td class="td-pic" style="font-size:12.5px;color:#475569;">
                        @if($r->pic)
                            <div style="display:flex;align-items:center;gap:5px;">
                                <i class="fa-solid fa-user" style="color:#94a3b8;font-size:11px;"></i>
                                {{ $r->pic }}
                            </div>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>

                    <td class="td-properti" style="font-size:12.5px;color:#475569;">
                        @if($r->properti)
                            <div style="display:flex;align-items:flex-start;gap:5px;">
                                <i class="fa-solid fa-box" style="color:#94a3b8;font-size:11px;margin-top:2px;"></i>
                                {{ $r->properti }}
                            </div>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>

                    <td class="td-keterangan" style="font-size:12.5px;color:#475569;max-width:280px;">
                        @if($r->keterangan)
                            <div style="white-space:pre-line;line-height:1.5;">{{ $r->keterangan }}</div>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>

                    
                    @if(auth()->user()->nama !== 'Hitz')
                        <td>
                            <div class="row-actions-view" style="display:flex;gap:6px;">
                                <button class="action-btn action-btn-warning"
                                        onclick="startEdit(this)" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button class="action-btn action-btn-danger"
                                        onclick="confirmDelete(this)" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                            <div class="row-actions-edit" style="display:none;gap:6px;">
                                <button class="action-btn action-btn-success"
                                        onclick="saveEdit(this)" title="Simpan">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <button class="action-btn action-btn-secondary"
                                        onclick="cancelEdit(this)" title="Batal">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </td>
                    @endif
                </tr>

                @empty
                <tr id="emptyRow">
                    <td colspan="9" style="text-align:center;padding:50px;color:#94a3b8;">
                        <i class="fa-solid fa-calendar-xmark" style="font-size:36px;display:block;margin-bottom:12px;opacity:.4;"></i>
                        <div style="font-weight:600;margin-bottom:4px;">Belum ada rundown</div>
                        <div style="font-size:12px;">Tambah baris di bawah untuk mulai menyusun jadwal</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── INLINE ADD ROWS ── --}}
    <div style="border-top:2px solid #e2e8f0;padding:16px 20px 20px;">

        <div id="inlineHeader" style="display:none;
             grid-template-columns:130px 130px 1fr 140px 160px 200px 36px;
             gap:8px;margin-bottom:6px;padding:0 2px;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;">JAM MULAI</div>
            <div style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;">JAM SELESAI</div>
            <div style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;">KEGIATAN</div>
            <div style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;">PIC</div>
            <div style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;">PROPERTI</div>
            <div style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;">KETERANGAN</div>
            <div></div>
        </div>

        <div id="inlineRows"></div>
        @if(auth()->user()->nama !== 'Hitz')
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:14px;flex-wrap:wrap;gap:10px;">
                <div style="font-size:12px;color:#94a3b8;">
                    <i class="fa-solid fa-circle-info" style="margin-right:4px;"></i>
                    Baris yang tidak diisi kegiatan akan otomatis dilewati.
                </div>
                <div style="display:flex;gap:8px;">
                    <button class="btn btn-outline" onclick="addInlineRow()" style="gap:6px;">
                        <i class="fa-solid fa-plus"></i> Tambah Baris
                    </button>
                    <button class="btn btn-primary" onclick="submitBulk()" id="btnSimpanSemua"
                            style="gap:6px;min-width:140px;display:none;">
                        <i class="fa-solid fa-check"></i> Simpan Semua
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- MODAL: DELETE CONFIRM --}}
<div class="modal-overlay" id="modalDelete">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;margin-right:8px;"></i>Hapus Kegiatan
                </div>
                <div class="modal-subtitle">Tindakan ini tidak bisa dibatalkan</div>
            </div>
            <button class="modal-close" onclick="closeModalDelete()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <p style="color:#475569;font-size:14px;margin-bottom:20px;">
                Yakin ingin menghapus kegiatan
                <strong id="deleteKegiatanName" style="color:#1e293b;"></strong>?
                Data yang sudah dihapus tidak dapat dikembalikan.
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

<style>
.ie-input {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 5px 8px;
    font-size: 13px;
    color: #1e293b;
    background: #fff;
    outline: none;
    box-sizing: border-box;
    transition: border-color .15s;
}
.ie-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22,163,74,.12);
}
.ie-time {
    font-family: monospace;
    width: 90px;
}
tr.editing-row td {
    background: #f0fdf4 !important;
    padding-top: 6px !important;
    padding-bottom: 6px !important;
}
.time-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border-radius: 6px;
    padding: 4px 9px;
}
.time-badge-green  { background:#f0fdf4; border:1px solid #bbf7d0; }
.time-badge-orange { background:#fff7ed; border:1px solid #fed7aa; }
.durasi-badge {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    border-radius: 5px;
    padding: 3px 8px;
    font-family: monospace;
}
.action-btn-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #16a34a;
}
.action-btn-success:hover { background:#dcfce7; }
.action-btn-secondary {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #64748b;
}
.action-btn-secondary:hover { background:#e2e8f0; }
</style>

@endsection

@push('scripts')
<script>
const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const NEXT_MULAI = '{{ $nextMulai }}';

// ── TOAST ──────────────────────────────────
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

// ── HELPERS ────────────────────────────────
function calcDurasi(mulai, selesai) {
    if (!mulai || !selesai) return '—';
    const [mh, mm] = mulai.split(':').map(Number);
    const [sh, sm] = selesai.split(':').map(Number);
    const diff = (sh * 60 + sm) - (mh * 60 + mm);
    if (diff <= 0) return '—';
    return String(Math.floor(diff / 60)).padStart(2, '0') + ':' + String(diff % 60).padStart(2, '0');
}
function addMinutes(time, mins) {
    if (!time) return '';
    const [h, m] = time.split(':').map(Number);
    const total  = h * 60 + m + mins;
    return String(Math.floor(total / 60) % 24).padStart(2, '0') + ':' + String(total % 60).padStart(2, '0');
}
function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// ── INLINE EDIT ────────────────────────────
let currentEditRow = null;

function startEdit(btn) {
    if (currentEditRow) {
        cancelEdit(currentEditRow.querySelector('.row-actions-edit button:last-child'));
    }

    const tr = btn.closest('tr');
    currentEditRow = tr;
    tr.classList.add('editing-row');

    const d = tr.dataset;

    tr.querySelector('.td-mulai').innerHTML = `
        <input type="time" class="ie-input ie-time" value="${d.mulai}"
               onchange="onEditMulaiChange(this)">`;

    tr.querySelector('.td-selesai').innerHTML = `
        <input type="time" class="ie-input ie-time" value="${d.selesai}"
               onchange="onEditSelesaiChange(this)">`;

    tr.querySelector('.td-durasi').innerHTML = `
        <span class="durasi-badge" id="editDurasiPreview">${calcDurasi(d.mulai, d.selesai)}</span>`;

    tr.querySelector('.td-kegiatan').innerHTML = `
        <input type="text" class="ie-input" value="${escHtml(d.kegiatan)}"
               placeholder="Kegiatan..." maxlength="200">`;

    tr.querySelector('.td-pic').innerHTML = `
        <input type="text" class="ie-input" value="${escHtml(d.pic)}"
               placeholder="PIC" maxlength="150">`;

    tr.querySelector('.td-properti').innerHTML = `
        <input type="text" class="ie-input" value="${escHtml(d.properti)}"
               placeholder="Properti" maxlength="200">`;

    tr.querySelector('.td-keterangan').innerHTML = `
        <input type="text" class="ie-input" value="${escHtml(d.keterangan)}"
               placeholder="Keterangan" maxlength="1000">`;

    tr.querySelector('.row-actions-view').style.display = 'none';
    tr.querySelector('.row-actions-edit').style.display = 'flex';

    tr.querySelector('.td-kegiatan input').focus();
}

function onEditMulaiChange(input) {
    const tr       = input.closest('tr');
    const mulai    = input.value;
    const selInput = tr.querySelector('.td-selesai input');
    if (selInput && selInput.value <= mulai) {
        selInput.value = addMinutes(mulai, 30);
    }
    refreshDurasiPreview(tr);
    cascadeNextRow(tr);
}

function onEditSelesaiChange(input) {
    const tr = input.closest('tr');
    refreshDurasiPreview(tr);
    cascadeNextRow(tr);
}

function refreshDurasiPreview(tr) {
    const mulai   = tr.querySelector('.td-mulai input')?.value   ?? '';
    const selesai = tr.querySelector('.td-selesai input')?.value ?? '';
    const el = document.getElementById('editDurasiPreview');
    if (el) el.textContent = calcDurasi(mulai, selesai);
}

function cascadeNextRow(tr) {
    const selesaiVal = tr.querySelector('.td-selesai input')?.value ?? tr.dataset.selesai;
    if (!selesaiVal) return;

    let next = tr.nextElementSibling;
    while (next && !next.classList.contains('data-row')) {
        next = next.nextElementSibling;
    }
    if (!next || next.classList.contains('editing-row')) return;

    const oldMulai = next.dataset.mulai;
    const oldSel   = next.dataset.selesai;
    const [oh, om] = oldMulai.split(':').map(Number);
    const [sh, sm] = oldSel.split(':').map(Number);
    const durMins  = (sh * 60 + sm) - (oh * 60 + om);

    next.dataset.mulai   = selesaiVal;
    next.dataset.selesai = durMins > 0 ? addMinutes(selesaiVal, durMins) : oldSel;

    renderTimeCells(next);
}

function renderTimeCells(tr) {
    const mulai   = tr.dataset.mulai;
    const selesai = tr.dataset.selesai;

    tr.querySelector('.td-mulai').innerHTML = `
        <div class="time-badge time-badge-green">
            <i class="fa-regular fa-clock" style="color:#16a34a;font-size:11px;"></i>
            <span style="font-weight:700;font-size:13px;color:#15803d;font-family:monospace;">${mulai}</span>
        </div>`;

    tr.querySelector('.td-selesai').innerHTML = `
        <div class="time-badge time-badge-orange">
            <i class="fa-regular fa-clock" style="color:#ea580c;font-size:11px;"></i>
            <span style="font-weight:700;font-size:13px;color:#c2410c;font-family:monospace;">${selesai}</span>
        </div>`;

    tr.querySelector('.td-durasi').innerHTML =
        `<span class="durasi-badge">${calcDurasi(mulai, selesai)}</span>`;
}

function renderViewCells(tr) {
    const d = tr.dataset;

    renderTimeCells(tr);

    tr.querySelector('.td-kegiatan').innerHTML =
        `<div style="font-weight:600;font-size:13.5px;color:#1e293b;">${escHtml(d.kegiatan)}</div>`;

    tr.querySelector('.td-pic').innerHTML = d.pic
        ? `<div style="display:flex;align-items:center;gap:5px;">
               <i class="fa-solid fa-user" style="color:#94a3b8;font-size:11px;"></i>${escHtml(d.pic)}
           </div>`
        : `<span style="color:#cbd5e1;">—</span>`;

    tr.querySelector('.td-properti').innerHTML = d.properti
        ? `<div style="display:flex;align-items:flex-start;gap:5px;">
               <i class="fa-solid fa-box" style="color:#94a3b8;font-size:11px;margin-top:2px;"></i>${escHtml(d.properti)}
           </div>`
        : `<span style="color:#cbd5e1;">—</span>`;

    tr.querySelector('.td-keterangan').innerHTML = d.keterangan
        ? `<div style="white-space:pre-line;line-height:1.5;">${escHtml(d.keterangan)}</div>`
        : `<span style="color:#cbd5e1;">—</span>`;
}

function cancelEdit(btn) {
    const tr = btn.closest('tr');
    tr.classList.remove('editing-row');
    renderViewCells(tr);
    tr.querySelector('.row-actions-view').style.display = 'flex';
    tr.querySelector('.row-actions-edit').style.display = 'none';
    currentEditRow = null;
}

async function saveEdit(btn) {
    const tr = btn.closest('tr');
    const id = tr.dataset.id;

    const mulai      = tr.querySelector('.td-mulai input').value.trim();
    const selesai    = tr.querySelector('.td-selesai input').value.trim();
    const kegiatan   = tr.querySelector('.td-kegiatan input').value.trim();
    const pic        = tr.querySelector('.td-pic input').value.trim();
    const properti   = tr.querySelector('.td-properti input').value.trim();
    const keterangan = tr.querySelector('.td-keterangan input').value.trim();

    if (!mulai || !selesai || !kegiatan) {
        showToast('Jam mulai, jam selesai, dan kegiatan wajib diisi', 'error'); return;
    }
    if (selesai <= mulai) {
        showToast('Jam selesai harus lebih besar dari jam mulai', 'error'); return;
    }

    const savBtn = tr.querySelector('.row-actions-edit .action-btn-success');
    savBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    savBtn.disabled  = true;

    const fd = new FormData();
    fd.append('_method',    'PUT');
    fd.append('mulai',      mulai);
    fd.append('selesai',    selesai);
    fd.append('kegiatan',   kegiatan);
    fd.append('pic',        pic);
    fd.append('properti',   properti);
    fd.append('keterangan', keterangan);

    try {
        const r    = await fetch(`/rundowns/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: fd,
        });
        const body = await r.json();

        if (r.ok) {
            tr.dataset.mulai      = mulai;
            tr.dataset.selesai    = selesai;
            tr.dataset.kegiatan   = kegiatan;
            tr.dataset.pic        = pic;
            tr.dataset.properti   = properti;
            tr.dataset.keterangan = keterangan;

            tr.classList.remove('editing-row');
            renderViewCells(tr);

            tr.querySelector('.row-actions-view').style.display = 'flex';
            tr.querySelector('.row-actions-edit').style.display = 'none';
            currentEditRow = null;

            showToast(body.message ?? 'Kegiatan berhasil diupdate!');
        } else if (r.status === 422) {
            const firstErr = Object.values(body.errors)[0][0];
            showToast(firstErr, 'error');
        } else {
            showToast(body.message ?? 'Terjadi kesalahan', 'error');
        }
    } catch (err) {
        console.error('saveEdit error:', err);
        showToast('Gagal menghubungi server', 'error');
    } finally {
        savBtn.innerHTML = '<i class="fa-solid fa-check"></i>';
        savBtn.disabled  = false;
    }
}

// ── INLINE ADD ROWS ────────────────────────
let rowCount = 0;

function toggleSimpanBtn() {
    const has = document.querySelectorAll('.inline-row').length > 0;
    document.getElementById('btnSimpanSemua').style.display = has ? '' : 'none';
}

function getLastSelesai() {
    const rows = document.querySelectorAll('.inline-row');
    if (rows.length > 0) {
        return rows[rows.length - 1].querySelector('.ir-selesai').value || NEXT_MULAI;
    }
    return NEXT_MULAI;
}

function addInlineRow(mulaiDefault = null) {
    document.getElementById('inlineHeader').style.display = 'grid';
    rowCount++;
    const id      = `row_${rowCount}`;
    const mulai   = mulaiDefault ?? getLastSelesai();
    const selesai = mulai ? addMinutes(mulai, 30) : '';

    const div = document.createElement('div');
    div.className = 'inline-row';
    div.id = id;
    div.style.cssText = `
        display:grid;
        grid-template-columns:130px 130px 1fr 140px 160px 200px 36px;
        gap:8px;margin-bottom:8px;align-items:center;
    `;
    div.innerHTML = `
        <input type="time" class="k-form-input ir-mulai" value="${mulai}"
               style="font-family:monospace;font-size:13px;"
               onchange="onNewMulaiChange('${id}')">
        <div style="display:flex;align-items:center;gap:4px;">
            <span style="color:#94a3b8;font-size:12px;">–</span>
            <input type="time" class="k-form-input ir-selesai" value="${selesai}"
                   style="font-family:monospace;font-size:13px;flex:1;">
        </div>
        <input type="text" class="k-form-input ir-kegiatan"
               placeholder="Tulis kegiatan..." maxlength="200" style="font-size:13px;">
        <input type="text" class="k-form-input ir-pic"
               placeholder="PIC" maxlength="150" style="font-size:13px;">
        <input type="text" class="k-form-input ir-properti"
               placeholder="Properti" maxlength="200" style="font-size:13px;">
        <input type="text" class="k-form-input ir-keterangan"
               placeholder="Keterangan" maxlength="1000" style="font-size:13px;">
        <button onclick="removeRow('${id}')"
                style="width:32px;height:32px;border:1px solid #fecaca;background:#fef2f2;
                       border-radius:6px;color:#ef4444;cursor:pointer;flex-shrink:0;
                       display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-xmark" style="font-size:12px;"></i>
        </button>
    `;
    document.getElementById('inlineRows').appendChild(div);
    toggleSimpanBtn();
    setTimeout(() => div.querySelector('.ir-kegiatan').focus(), 50);
}

function onNewMulaiChange(id) {
    const row     = document.getElementById(id);
    const mulai   = row.querySelector('.ir-mulai').value;
    const selesai = row.querySelector('.ir-selesai').value;
    if (!selesai || selesai <= mulai) {
        row.querySelector('.ir-selesai').value = addMinutes(mulai, 30);
    }
}

function removeRow(id) {
    document.getElementById(id)?.remove();
    if (document.querySelectorAll('.inline-row').length === 0) {
        document.getElementById('inlineHeader').style.display = 'none';
    }
    toggleSimpanBtn();
}

async function submitBulk() {
    const rows   = document.querySelectorAll('.inline-row');
    const items  = [];
    let hasError = false;

    rows.forEach(row => {
        const mulai    = row.querySelector('.ir-mulai').value;
        const selesai  = row.querySelector('.ir-selesai').value;
        const kegiatan = row.querySelector('.ir-kegiatan').value.trim();
        if (!kegiatan && !mulai && !selesai) return;

        row.querySelectorAll('.ir-err').forEach(e => e.remove());
        let rowErr = [];
        if (!mulai)    rowErr.push('Jam mulai wajib');
        if (!selesai)  rowErr.push('Jam selesai wajib');
        if (!kegiatan) rowErr.push('Kegiatan wajib');
        if (mulai && selesai && selesai <= mulai) rowErr.push('Selesai > mulai');

        if (rowErr.length) {
            hasError = true;
            const errDiv = document.createElement('div');
            errDiv.className = 'ir-err';
            errDiv.style.cssText = 'grid-column:1/-1;font-size:11px;color:#ef4444;padding:2px 0 0;';
            errDiv.textContent = rowErr.join(' · ');
            row.appendChild(errDiv);
            return;
        }
        items.push({
            mulai, selesai, kegiatan,
            pic:        row.querySelector('.ir-pic').value.trim(),
            properti:   row.querySelector('.ir-properti').value.trim(),
            keterangan: row.querySelector('.ir-keterangan').value.trim(),
        });
    });

    if (hasError) return;
    if (!items.length) { showToast('Tidak ada data yang diisi', 'error'); return; }

    const btn = document.getElementById('btnSimpanSemua');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const r    = await fetch('{{ route('rundowns.bulk') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ items }),
        });
        const body = await r.json();

        if (r.ok) {
            document.getElementById('inlineRows').innerHTML = '';
            document.getElementById('inlineHeader').style.display = 'none';
            toggleSimpanBtn();
            showToast(body.message ?? `${items.length} kegiatan berhasil disimpan!`);
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(body.message ?? 'Terjadi kesalahan', 'error');
        }
    } catch (err) {
        console.error('submitBulk error:', err);
        showToast('Gagal menghubungi server', 'error');
    } finally {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Simpan Semua';
    }
}

// ── DELETE ─────────────────────────────────
let deleteTargetRow = null;

function confirmDelete(btn) {
    deleteTargetRow = btn.closest('tr');
    document.getElementById('deleteKegiatanName').textContent = deleteTargetRow.dataset.kegiatan;
    document.getElementById('modalDelete').classList.add('show');
}
function closeModalDelete() {
    deleteTargetRow = null;
    document.getElementById('modalDelete').classList.remove('show');
}

async function doDelete() {
    if (!deleteTargetRow) return;
    const id        = deleteTargetRow.dataset.id;
    const targetRow = deleteTargetRow; // ← simpan reference lokal
    const btn       = document.getElementById('btnConfirmDelete');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghapus...';

    try {
        const r    = await fetch(`/rundowns/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });
        const body = await r.json();

        if (r.ok) {
            closeModalDelete();
            targetRow.remove(); // ← pakai local variable
            renumberRows();
            showToast(body.message ?? 'Kegiatan berhasil dihapus!');
        } else {
            showToast(body.message ?? 'Gagal menghapus', 'error');
        }
    } catch (err) {
        console.error('doDelete error:', err);
        showToast('Gagal menghubungi server', 'error');
    } finally {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-trash"></i> Hapus';
    }
}

function renumberRows() {
    document.querySelectorAll('#rundownBody .data-row').forEach((tr, i) => {
        tr.querySelector('.td-no').textContent = i + 1;
    });
}

document.getElementById('modalDelete').addEventListener('click', function(e) {
    if (e.target === this) closeModalDelete();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModalDelete();
});
</script>
@endpush