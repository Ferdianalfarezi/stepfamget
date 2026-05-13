@extends('layouts.app')
@section('title', 'Penerimaan Baju')
@section('page-title', 'Penerimaan Baju')

@section('content')

{{-- ── SUMMARY CARDS ─────────────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:14px;">
    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-box-open" style="color:#0369a1;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Sudah Terima</div>
            <div style="font-size:20px;font-weight:800;color:#0369a1;" id="counterScanned">{{ $totalScanned }}</div>
        </div>
    </div>
    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#fef9c3;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-clock" style="color:#ca8a04;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Belum Terima</div>
            <div style="font-size:20px;font-weight:800;color:#ca8a04;" id="counterUnscanned">{{ $totalUnscanned }}</div>
        </div>
    </div>
    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-users" style="color:#16a34a;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Total</div>
            <div style="font-size:20px;font-weight:800;color:#16a34a;">{{ $totalScanned + $totalUnscanned }}</div>
        </div>
    </div>
</div>

{{-- ── FILTER ───────────────────────────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:10px;">
    <div class="card-body" style="padding:14px 18px;">
        <form method="GET" action="{{ route('penerimaan-baju.index') }}">
            <div class="filters" style="flex-wrap:wrap;gap:8px;">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama, NIK, departemen..."
                           value="{{ request('search') }}" style="width:220px;">
                </div>
                <select name="departemen" class="form-control" style="width:auto;min-width:160px;">
                    <option value="">Semua Departemen</option>
                    @foreach($departemenList as $dept)
                        <option value="{{ $dept }}" {{ request('departemen') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
                <select name="hubungan" class="form-control" style="width:auto;min-width:130px;">
                    <option value="">Semua Hubungan</option>
                    @foreach(['Karyawan','Karyawati','Istri','Suami','Anak','Saudara'] as $h)
                        <option value="{{ $h }}" {{ request('hubungan') == $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
                <select name="ukuran" class="form-control" style="width:auto;min-width:110px;">
                    <option value="">Semua Ukuran</option>
                    @foreach(['XS','S','M','L','XL','XXL','XXXL'] as $u)
                        <option value="{{ $u }}" {{ request('ukuran') == $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>
                <select name="status_terima" class="form-control" style="width:auto;min-width:140px;">
                    <option value="">Semua Status</option>
                    <option value="sudah" {{ request('status_terima') == 'sudah' ? 'selected' : '' }}>Sudah Terima</option>
                    <option value="belum" {{ request('status_terima') == 'belum' ? 'selected' : '' }}>Belum Terima</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
                @if(request()->hasAny(['search','departemen','hubungan','ukuran','status_terima']))
                    <a href="{{ route('penerimaan-baju.index') }}" class="btn btn-outline">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── TABEL ────────────────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-box-open" style="color:#0369a1;margin-right:4px;"></i>Penerimaan Baju
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $details->total() }} anggota
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">

            {{-- SCAN NIK --}}
            <div style="display:flex;align-items:center;border:1.5px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#f8fafc;">
                <div style="padding:0 10px;color:#94a3b8;font-size:13px;border-right:1px solid #e2e8f0;height:36px;display:flex;align-items:center;">
                    <i class="fa-solid fa-barcode"></i>
                </div>
                <input type="text" id="scanInput"
                       placeholder="Scan / ketik NIK..."
                       autocomplete="off"
                       style="border:none;background:transparent;padding:0 12px;height:36px;font-size:13px;width:180px;outline:none;color:#374151;">
                <button onclick="clearScan()" id="btnClearScan"
                        style="display:none;border:none;background:transparent;padding:0 10px;height:36px;cursor:pointer;color:#94a3b8;border-left:1px solid #e2e8f0;">
                    <i class="fa-solid fa-xmark" style="font-size:12px;"></i>
                </button>
            </div>

            <div id="scanStatus" style="display:none;font-size:12px;padding:6px 12px;border-radius:8px;font-weight:600;max-width:260px;"></div>

            {{-- Print per departemen --}}
            <div style="display:flex;align-items:center;gap:0;border:1.5px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#f8fafc;">
                <select id="selectDeptPrint" class="form-control"
                        style="border:none;background:transparent;height:36px;padding:0 10px;font-size:13px;min-width:160px;outline:none;">
                    <option value="">Pilih Departemen</option>
                    @foreach($departemenList as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
                <button onclick="doPrintDept()"
                        style="height:36px;border:none;border-left:1px solid #e2e8f0;padding:0 12px;background:#0369a1;color:#fff;cursor:pointer;font-size:12px;font-weight:600;display:flex;align-items:center;gap:5px;white-space:nowrap;">
                    <i class="fa-solid fa-print"></i> Cetak
                </button>
            </div>

            <button onclick="doResetAll()" class="btn"
                    style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-rotate-left"></i> Reset All
            </button>
            <a href="{{ route('penerimaan-baju.export', request()->query()) }}"
               class="btn"
               style="background:#0369a1;color:#fff;border:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-file-excel"></i> Export
            </a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Departemen</th>
                    <th>Nama Anggota</th>
                    <th>Hubungan</th>
                    <th>Ukuran</th>
                    <th>Jenis / Lengan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php $prevNik = null; @endphp
                @forelse($details as $d)
                @php
                    $k        = $d->karyawan;
                    $isNewNik = $d->nik !== $prevNik;
                    $prevNik  = $d->nik;
                @endphp
                <tr data-nik="{{ $d->nik }}"
                    data-nama="{{ $k->nama ?? '' }}"
                    data-dept="{{ $k->departemen ?? '' }}"
                    class="scan-row{{ $d->is_scanned_baju ? ' row-scanned' : '' }}">
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($details->currentPage() - 1) * $details->perPage() }}
                    </td>
                    <td>
                        @if($isNewNik)
                            <span style="background:#0369a1;color:#fff;border-radius:6px;padding:2px 8px;font-size:12px;font-weight:700;">
                                {{ $d->nik }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px;">{{ $d->nik }}</span>
                        @endif
                    </td>
                    <td>
                        @if($isNewNik)
                            <div style="font-weight:600;font-size:13px;">{{ $k->nama ?? '-' }}</div>
                        @else
                            <div style="font-size:12px;color:#cbd5e1;">{{ $k->nama ?? '-' }}</div>
                        @endif
                    </td>
                    <td>
                        @if($isNewNik && $k)
                            <span class="badge badge-success">{{ $k->departemen }}</span>
                        @endif
                    </td>
                    <td style="font-size:13px;font-weight:600;color:#0369a1;">{{ $d->nama_keluarga }}</td>
                    <td>
                        @php
                            $hubClass = match($d->hubungan) {
                                'Karyawan'  => 'hubungan-karyawan',
                                'Karyawati' => 'hubungan-karyawati',
                                'Istri'     => 'hubungan-istri',
                                'Suami'     => 'hubungan-suami',
                                'Anak'      => 'hubungan-anak',
                                default     => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $hubClass }}">{{ $d->hubungan }}</span>
                    </td>
                    <td>
                        @if($d->ukuran_kaos)
                            <span style="background:#1e293b;color:#fff;border-radius:8px;padding:4px 10px;font-size:13px;font-weight:800;">
                                {{ $d->ukuran_kaos }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px;font-style:italic;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($d->jenis_kaos === 'Anak')
                            <span style="font-size:12px;color:#64748b;">Anak</span>
                        @elseif($d->lengan_kaos)
                            <span style="font-size:12px;color:#64748b;">
                                {{ $d->lengan_kaos === 'Lengan Pendek' ? 'Pendek' : 'Panjang' }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px;">-</span>
                        @endif
                    </td>
                    <td>
                    <div style="display:flex;align-items:center;gap:6px;">
                        @if($d->is_scanned_baju)
                            <div>
                                <span class="badge" style="background:#dbeafe;color:#1d4ed8;">
                                    <i class="fa-solid fa-check" style="font-size:10px;margin-right:2px;"></i>Sudah
                                </span>
                                @if($d->scanned_baju_at)
                                    <div style="font-size:10px;color:#94a3b8;margin-top:2px;">
                                        {{ $d->scanned_baju_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </div>
                            @if($isNewNik)
                                <button onclick="resetSingleNik('{{ $d->nik }}', '{{ addslashes($k->nama ?? '') }}')"
                                    title="Reset penerimaan NIK ini"
                                    style="border:none;background:#fef2f2;color:#f87171;border:1.5px solid #fecaca;border-radius:7px;padding:5px 8px;cursor:pointer;font-size:12px;display:inline-flex;align-items:center;flex-shrink:0;transition:all .15s;"
                                    onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626';this.style.borderColor='#f87171';"
                                    onmouseout="this.style.background='#fef2f2';this.style.color='#f87171';this.style.borderColor='#fecaca';">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                            @endif
                        @else
                            <span class="badge badge-gray">Belum</span>
                        @endif
                    </div>
                </td>

                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-box-open" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($details->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $details->firstItem() }}–{{ $details->lastItem() }} dari {{ $details->total() }} data
        </div>
        <div class="pagination">
            @if($details->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $details->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
            @endif
            @foreach($details->getUrlRange(max(1,$details->currentPage()-2), min($details->lastPage(),$details->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $details->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($details->hasMorePages())
                <a href="{{ $details->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- MODAL KONFIRMASI BULK DEPARTEMEN --}}
<div class="modal-overlay" id="modalBulkDept">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-building" style="color:#0369a1;margin-right:8px;"></i>Scan Bulk Departemen
                </div>
                <div class="modal-subtitle" id="bulkDeptSub">–</div>
            </div>
            <button class="modal-close" onclick="closeBulkModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p style="color:#475569;font-size:14px;margin-bottom:20px;" id="bulkDeptMsg"></p>
            <div class="k-form-actions">
                <button type="button" class="btn btn-outline" onclick="closeBulkModal()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnConfirmBulk" onclick="confirmBulkDept()">
                    <i class="fa-solid fa-check"></i> Ya, Scan Se-Departemen
                </button>
                <button type="button" class="btn" id="btnSingleOnly"
                        style="background:#f1f5f9;color:#475569;"
                        onclick="confirmSingleOnly()">
                    <i class="fa-solid fa-user"></i> Hanya Ini
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: GENERIC CONFIRM --}}
<div class="modal-overlay" id="modalConfirm">
    <div class="modal-box" style="max-width:400px;">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="confirmTitle">–</div>
                <div class="modal-subtitle" id="confirmSubtitle">–</div>
            </div>
            <button class="modal-close" onclick="closeConfirm()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p style="color:#475569;font-size:14px;margin-bottom:20px;" id="confirmMsg"></p>
            <div class="k-form-actions">
                <button type="button" class="btn btn-outline" onclick="closeConfirm()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" id="btnConfirmOk" class="btn">
                    <i class="fa-solid fa-check"></i> <span id="confirmOkLabel">Ya</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
const CSRF           = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const SCAN_URL       = '{{ route("penerimaan-baju.scan") }}';
const SCAN_DEPT_URL  = '{{ route("penerimaan-baju.scanDepartemen") }}';
const RESET_NIK_URL  = '{{ route("penerimaan-baju.resetNik") }}';
const PRINT_URL      = '{{ route("penerimaan-baju.print") }}';

const scanInput  = document.getElementById('scanInput');
const btnClear   = document.getElementById('btnClearScan');
const scanStatus = document.getElementById('scanStatus');
let   scanTimer  = null;

let pendingNik  = null;
let pendingNama = null;
let pendingDept = null;

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    let wrap = document.getElementById('toastContainer');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'toastContainer';
        wrap.className = 'toast-container';
        document.body.appendChild(wrap);
    }
    const icon  = type === 'success' ? 'fa-circle-check' : type === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-exclamation';
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<i class="fa-solid ${icon}"></i>${msg}`;
    wrap.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

// ── Generic confirm modal ─────────────────────────────────────────────────────
let confirmCallback = null;

function showConfirm({ title, subtitle, msg, okLabel = 'Ya', okClass = 'btn-primary', onConfirm }) {
    document.getElementById('confirmTitle').innerHTML    = title;
    document.getElementById('confirmSubtitle').textContent = subtitle ?? '';
    document.getElementById('confirmMsg').innerHTML      = msg;
    document.getElementById('confirmOkLabel').textContent = okLabel;

    const btn = document.getElementById('btnConfirmOk');
    btn.className = `btn ${okClass}`;
    confirmCallback = onConfirm;

    document.getElementById('modalConfirm').classList.add('show');
}

function closeConfirm() {
    document.getElementById('modalConfirm').classList.remove('show');
    confirmCallback = null;
}

document.getElementById('btnConfirmOk').addEventListener('click', () => {
    closeConfirm();
    if (typeof confirmCallback === 'function') confirmCallback();
});

// ── Input scan ────────────────────────────────────────────────────────────────
scanInput.addEventListener('input', function () {
    clearTimeout(scanTimer);
    const val = this.value.trim();
    if (!val) { clearScan(); return; }
    btnClear.style.display = 'flex';
    scanTimer = setTimeout(() => doScan(val), 300);
});
scanInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        clearTimeout(scanTimer);
        const val = this.value.trim();
        if (val) doScan(val);
    }
});

// ── Scan utama ────────────────────────────────────────────────────────────────
async function doScan(nik) {
    setStatus('Memproses...', 'loading');
    try {
        const res  = await fetch(SCAN_URL, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body   : JSON.stringify({ nik }),
        });
        const data = await res.json();

        if (!res.ok || !data.found) {
            setStatus(data.message || 'NIK tidak ditemukan.', 'error');
            return;
        }

        if (data.already_done) {
            setStatus(data.nama + ' sudah mengambil baju.', 'warning');
            highlightRows(nik);
            clearInputDelayed();
            return;
        }

        pendingNik  = nik;
        pendingNama = data.nama;
        pendingDept = data.departemen;

        markRowsScanned(nik);
        updateCounters(data.count, -data.count);
        setStatus(data.nama + ' — ' + data.count + ' anggota ditandai', 'success');

        document.getElementById('bulkDeptSub').textContent = `Departemen: ${data.departemen}`;
        document.getElementById('bulkDeptMsg').innerHTML   =
            `<strong>${data.nama}</strong> berhasil di-scan. Mau scan semua karyawan departemen <strong>${data.departemen}</strong> sekaligus?`;
        document.getElementById('modalBulkDept').classList.add('show');

        clearInputDelayed();
    } catch (e) {
        setStatus('Gagal menghubungi server.', 'error');
    }
}

// ── Konfirmasi: scan se-departemen ───────────────────────────────────────────
async function confirmBulkDept() {
    closeBulkModal();
    if (!pendingDept) return;

    setStatus('Scanning departemen ' + pendingDept + '...', 'loading');
    try {
        const res  = await fetch(SCAN_DEPT_URL, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body   : JSON.stringify({ departemen: pendingDept }),
        });
        const data = await res.json();

        document.querySelectorAll('.scan-row').forEach(tr => {
            if (tr.dataset.dept === pendingDept) tr.classList.add('row-scanned');
        });

        updateCounters(data.count, -data.count);
        setStatus('Departemen ' + pendingDept + ' — ' + data.count + ' anggota ditandai', 'success');
        showToast('Departemen ' + pendingDept + ' berhasil di-scan!');
    } catch (e) {
        setStatus('Gagal scan departemen.', 'error');
        showToast('Gagal scan departemen.', 'error');
    }

    pendingNik = pendingNama = pendingDept = null;
}

function confirmSingleOnly() {
    closeBulkModal();
    pendingNik = pendingNama = pendingDept = null;
}

// ── Reset semua ───────────────────────────────────────────────────────────────
function doResetAll() {
    showConfirm({
        title    : '<i class="fa-solid fa-rotate-left" style="color:#dc2626;margin-right:8px;"></i>Reset Semua Penerimaan',
        subtitle : 'Tindakan ini tidak bisa dibatalkan',
        msg      : 'Yakin ingin mereset <strong>semua</strong> data penerimaan baju? Seluruh tanda akan dihapus.',
        okLabel  : 'Ya, Reset Semua',
        okClass  : 'k-btn-danger',
        onConfirm: async () => {
            try {
                await fetch('{{ route("penerimaan-baju.resetScan") }}', {
                    method : 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                });
                document.querySelectorAll('.row-scanned').forEach(tr => tr.classList.remove('row-scanned'));
                const total = parseInt('{{ $totalScanned + $totalUnscanned }}');
                document.getElementById('counterScanned').textContent   = '0';
                document.getElementById('counterUnscanned').textContent = total;
                setStatus('Semua penerimaan direset.', 'error');
                showToast('Semua penerimaan berhasil direset.', 'error');
            } catch (e) {
                showToast('Gagal reset.', 'error');
            }
        }
    });
}

function closeBulkModal() {
    document.getElementById('modalBulkDept').classList.remove('show');
}

// ── Reset per NIK ─────────────────────────────────────────────────────────────
function resetSingleNik(nik, nama) {
    showConfirm({
        title    : '<i class="fa-solid fa-rotate-left" style="color:#dc2626;margin-right:8px;"></i>Reset Penerimaan',
        subtitle : `NIK: ${nik}`,
        msg      : `Yakin ingin mereset penerimaan baju <strong>${nama}</strong>? Seluruh anggota keluarganya akan ditandai belum terima.`,
        okLabel  : 'Ya, Reset',
        okClass  : 'k-btn-danger',
        onConfirm: async () => {
            try {
                const res  = await fetch(RESET_NIK_URL, {
                    method : 'POST',
                    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
                    body   : JSON.stringify({ nik }),
                });
                const data = await res.json();

                document.querySelectorAll(`.scan-row[data-nik="${nik}"]`).forEach(tr => {
                    tr.classList.remove('row-scanned');
                });

                updateCounters(-data.count, data.count);
                setStatus(data.message, 'error');
                showToast(data.message, 'error');

                setTimeout(() => location.reload(), 1000);
            } catch (e) {
                showToast('Gagal reset.', 'error');
            }
        }
    });
}

// ── Print per departemen ──────────────────────────────────────────────────────
function doPrintDept() {
    const dept = document.getElementById('selectDeptPrint').value;
    if (!dept) {
        showToast('Pilih departemen terlebih dahulu.', 'warning');
        return;
    }
    window.open(PRINT_URL + '?departemen=' + encodeURIComponent(dept), '_blank');
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function markRowsScanned(nik) {
    document.querySelectorAll(`.scan-row[data-nik="${nik}"]`).forEach(tr => tr.classList.add('row-scanned'));
    const rows = document.querySelectorAll(`.scan-row[data-nik="${nik}"]`);
    if (rows.length) rows[0].scrollIntoView({ behavior:'smooth', block:'center' });
}

function highlightRows(nik) {
    document.querySelectorAll(`.scan-row[data-nik="${nik}"]`).forEach(tr => {
        tr.style.outline = '2px solid #bfdbfe';
        setTimeout(() => tr.style.outline = '', 2000);
    });
}

function updateCounters(deltaScanned, deltaUnscanned) {
    const cS = document.getElementById('counterScanned');
    const cU = document.getElementById('counterUnscanned');
    if (cS) cS.textContent = Math.max(0, parseInt(cS.textContent) + deltaScanned);
    if (cU) cU.textContent = Math.max(0, parseInt(cU.textContent) + deltaUnscanned);
}

function clearInputDelayed() {
    setTimeout(() => {
        scanInput.value        = '';
        btnClear.style.display = 'none';
        scanInput.focus();
    }, 1200);
}

function clearScan() {
    scanInput.value          = '';
    btnClear.style.display   = 'none';
    scanStatus.style.display = 'none';
    scanInput.focus();
}

function setStatus(msg, type) {
    scanStatus.style.display = 'block';
    scanStatus.textContent   = msg;
    const map = {
        success : ['#eff6ff','#1d4ed8','1px solid #bfdbfe'],
        error   : ['#fef2f2','#dc2626','1px solid #fca5a5'],
        warning : ['#fefce8','#ca8a04','1px solid #fde68a'],
        loading : ['#f8fafc','#64748b','1px solid #e2e8f0'],
    };
    const [bg, color, border] = map[type] || map.loading;
    scanStatus.style.background = bg;
    scanStatus.style.color      = color;
    scanStatus.style.border     = border;
}

// ── Close modal on overlay / ESC ─────────────────────────────────────────────
['modalBulkDept','modalConfirm'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['modalBulkDept','modalConfirm'].forEach(id => {
            document.getElementById(id).classList.remove('show');
        });
    }
});

scanInput.focus();
</script>

<style>
.row-scanned { background:#eff6ff !important; }
.row-scanned td { border-bottom-color:#dbeafe !important; }
</style>
@endpush