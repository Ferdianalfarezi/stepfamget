@extends('layouts.app')
@section('title', 'Penerimaan Hadiah')
@section('page-title', 'Penerimaan Hadiah')

@section('content')
{{-- ── FILTER BAR ── --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('penerimaan-hadiah.index') }}">
            <div class="filters">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari barang atau pemenang..."
                           value="{{ request('search') }}" style="width:280px;">
                </div>
                <select name="status" class="form-control" style="width:190px;">
                    <option value="">Semua Status</option>
                    <option value="belum_ada_pemenang" {{ request('status') === 'belum_ada_pemenang' ? 'selected' : '' }}>Belum Ada Pemenang</option>
                    <option value="siap_diambil"       {{ request('status') === 'siap_diambil'       ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="sudah_diambil"      {{ request('status') === 'sudah_diambil'      ? 'selected' : '' }}>Sudah Diambil</option>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('penerimaan-hadiah.index') }}" class="btn btn-outline">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── SCAN BAR ── --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:14px 20px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="font-size:13px;font-weight:600;color:#0b4614;white-space:nowrap;">
                <i class="fa-solid fa-qrcode" style="margin-right:6px;"></i>Scan QR
            </div>
            <input type="text" id="scanInput" class="form-control"
                   placeholder="Scan atau ketik QR code..." autocomplete="off"
                   style="max-width:320px;"
                   onkeydown="if(event.key==='Enter'){doScan();}">
            <button class="btn btn-primary" onclick="doScan()" style="white-space:nowrap;">
                <i class="fa-solid fa-barcode-scan"></i> Proses
            </button>
        </div>
        <div id="scanResult" style="display:none;margin-top:10px;border-radius:10px;padding:12px 16px;font-size:13px;"></div>
    </div>
</div>

{{-- ── TABLE CARD ── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-gift" style="color:#0b4614;margin-right:6px;"></i>Data Penerimaan Hadiah
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $items->total() }} hadiah
            </div>
        </div>
        <button class="btn btn-primary" onclick="openModalCreate()">
            <i class="fa-solid fa-plus"></i> Tambah Hadiah
        </button>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Nama Barang</th>
                    <th>Pemenang</th>
                    <th>Status Pengambilan</th>
                    <th>QR Code</th>
                    <th>Waktu Scan</th>
                    <th style="width:110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13.5px;">{{ $item->barang }}</div>
                    </td>
                    <td>
                        @if($item->nama_pemenang)
                            <div style="font-weight:600;font-size:13px;">{{ $item->nama_pemenang }}</div>
                            <div style="font-size:11px;color:#94a3b8;">NIK: {{ $item->nik_pemenang }}</div>
                        @else
                            <span style="font-size:12px;color:#94a3b8;font-style:italic;">Belum ditentukan</span>
                        @endif
                    </td>
                    <td>
                        <span style="
                            display:inline-flex;align-items:center;gap:5px;
                            padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:600;
                            background:{{ $item->status_bg }};color:{{ $item->status_color }};">
                            <span style="width:6px;height:6px;border-radius:50%;background:{{ $item->status_color }};flex-shrink:0;"></span>
                            {{ $item->status_label }}
                        </span>
                    </td>
                    <td>
                        @if($item->qr_code)
                            <span style="font-family:monospace;font-size:12px;background:#f1f5f9;padding:3px 8px;border-radius:6px;color:#475569;">
                                {{ $item->qr_code }}
                            </span>
                        @else
                            <span style="font-size:12px;color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#64748b;white-space:nowrap;">
                        {{ $item->scanned_at ? $item->scanned_at->format('d M Y, H:i') : '—' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="action-btn action-btn-warning"
                                    onclick="openModalEdit({{ $item->id }})"
                                    title="Pilih Pemenang">
                                <i class="fa-solid fa-trophy"></i>
                            </button>
                            <button class="action-btn action-btn-info"
                                    onclick="printHadiah({{ $item->id }})">
                                <i class="fa-solid fa-print"></i>
                            </button>
                            <button class="action-btn action-btn-danger"
                                    onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->barang) }}')"
                                    title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-gift" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Tidak ada data hadiah
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($items->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }}
            dari {{ $items->total() }} data
        </div>
        <div class="pagination">
            @if($items->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $items->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif
            @foreach($items->getUrlRange(
                max(1, $items->currentPage()-2),
                min($items->lastPage(), $items->currentPage()+2)
            ) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $items->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach
            @if($items->hasMorePages())
                <a href="{{ $items->nextPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ============================= --}}
{{-- MODAL: CREATE                 --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalCreate">
    <div class="modal-box" style="max-width:480px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-gift" style="color:#0b4614;margin-right:8px;"></i>Tambah Hadiah
                </div>
                <div class="modal-subtitle">Isi nama barang hadiah</div>
            </div>
            <button class="modal-close" onclick="closeModalCreate()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formCreate" onsubmit="submitCreate(event)">
                @csrf
                <div class="k-form-grid" style="grid-template-columns:1fr;">
                    <div class="k-form-group">
                        <label class="k-form-label">Nama Barang <span class="required">*</span></label>
                        <input type="text" name="barang" id="create_barang" class="k-form-input"
                               placeholder="Contoh: Kulkas 2 Pintu Sharp" required maxlength="200">
                        <div class="k-form-error" id="cerr_barang"></div>
                    </div>
                </div>
                <div class="k-form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModalCreate()">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitCreate">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: EDIT (Pilih Pemenang)  --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-trophy" style="color:#f59e0b;margin-right:8px;"></i>Pilih Pemenang
                </div>
                <div class="modal-subtitle" id="editModalSub">Memuat data...</div>
            </div>
            <button class="modal-close" onclick="closeModalEdit()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formEdit" onsubmit="submitEdit(event)">
                @csrf
                <input type="hidden" id="edit_id">
                <div class="k-form-grid" style="grid-template-columns:1fr;">

                    {{-- NAMA BARANG --}}
                    <div class="k-form-group">
                        <label class="k-form-label">Nama Barang <span class="required">*</span></label>
                        <input type="text" name="barang" id="edit_barang" class="k-form-input"
                               required maxlength="200">
                        <div class="k-form-error" id="eerr_barang"></div>
                    </div>

                    {{-- KARYAWAN SEARCHABLE --}}
                    <div class="k-form-group">
                        <label class="k-form-label">Pemenang</label>
                        <div class="supplier-select-wrap" id="eKaryawanWrap">
                            <div class="supplier-select-display" id="eKaryawanDisplay"
                                 onclick="toggleKaryawanDropdown()" tabindex="0">
                                <span id="eKaryawanLabel" style="color:#94a3b8;">Pilih karyawan...</span>
                                <i class="fa-solid fa-chevron-down" style="font-size:11px;color:#94a3b8;"></i>
                            </div>
                            <div class="supplier-dropdown" id="eKaryawanDropdown" style="display:none;">
                                <div class="supplier-search-wrap">
                                    <i class="fa-solid fa-magnifying-glass" style="font-size:11px;color:#94a3b8;"></i>
                                    <input type="text" class="supplier-search-input" id="eKaryawanSearch"
                                           placeholder="Cari nama atau NIK..." oninput="filterKaryawan(this.value)">
                                </div>
                                <div class="supplier-options" id="eKaryawanOptions">
                                    <div class="supplier-option" data-nik="" data-nama=""
                                         onclick="selectKaryawan('', '', '')">
                                        <span style="color:#94a3b8;font-style:italic;">— Kosongkan Pemenang —</span>
                                    </div>
                                    @foreach($karyawans as $k)
                                    <div class="supplier-option"
                                         data-nik="{{ $k->nik }}"
                                         data-nama="{{ $k->nama }}"
                                         data-dept="{{ $k->departemen }}"
                                         onclick="selectKaryawan('{{ $k->nik }}', '{{ addslashes($k->nama) }}', '{{ addslashes($k->departemen) }}')">
                                        <div style="font-weight:600;font-size:13px;">{{ $k->nama }}</div>
                                        <div style="font-size:11px;color:#94a3b8;">NIK: {{ $k->nik }} · {{ $k->departemen }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="nik_pemenang" id="edit_nik_pemenang">
                        <div class="k-form-error" id="eerr_nik_pemenang"></div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                            <i class="fa-solid fa-circle-info" style="margin-right:3px;"></i>
                            Saat pemenang dipilih, QR code otomatis di-generate.
                        </div>
                    </div>

                    {{-- INFO QR (readonly) --}}
                    <div class="k-form-group" id="qrInfoWrap" style="display:none;">
                        <label class="k-form-label">QR Code Lama</label>
                        <div id="qrInfoDisplay" style="
                            padding:9px 14px;border-radius:8px;background:#f1f5f9;
                            border:1px solid #e2e8f0;font-size:13px;font-family:monospace;
                            color:#475569;">—</div>
                        <div style="font-size:11px;color:#f59e0b;margin-top:4px;">
                            <i class="fa-solid fa-triangle-exclamation" style="margin-right:3px;"></i>
                            Jika pemenang diubah, QR code lama tidak berlaku.
                        </div>
                    </div>

                </div>
                <div class="k-form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModalEdit()">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitEdit">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: DELETE                 --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalDelete">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;margin-right:8px;"></i>Hapus Hadiah
                </div>
                <div class="modal-subtitle">Tindakan ini tidak bisa dibatalkan</div>
            </div>
            <button class="modal-close" onclick="closeModalDelete()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <p style="color:#475569;font-size:14px;margin-bottom:20px;">
                Yakin ingin menghapus hadiah
                <strong id="deleteItemName" style="color:#1e293b;"></strong>?
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

{{-- ============================= --}}
{{-- MODAL: SCAN RESULT            --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalScan">
    <div class="modal-box" style="max-width:400px;">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="scanModalTitle">Hasil Scan</div>
                <div class="modal-subtitle" id="scanModalSub"></div>
            </div>
            <button class="modal-close" onclick="closeModalScan()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body" id="scanModalBody">
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.supplier-select-wrap { position:relative; }
.supplier-select-display {
    display:flex; align-items:center; justify-content:space-between;
    padding:9px 14px; border:1px solid #e2e8f0; border-radius:8px;
    background:#fff; cursor:pointer; font-size:13.5px; color:#1e293b;
    transition:border-color .15s,box-shadow .15s; user-select:none;
}
.supplier-select-display:focus,
.supplier-select-display.open {
    border-color:#0b4614; box-shadow:0 0 0 3px rgba(11,70,20,.08); outline:none;
}
.supplier-dropdown {
    position:absolute; top:calc(100% + 4px); left:0; right:0;
    background:#fff; border:1px solid #e2e8f0; border-radius:10px;
    box-shadow:0 8px 24px rgba(0,0,0,.10); z-index:999; overflow:hidden;
}
.supplier-search-wrap {
    display:flex; align-items:center; gap:8px;
    padding:10px 12px; border-bottom:1px solid #f1f5f9;
}
.supplier-search-input {
    border:none; outline:none; font-size:13px;
    width:100%; color:#1e293b; background:transparent;
}
.supplier-options { max-height:220px; overflow-y:auto; }
.supplier-option {
    padding:9px 14px; font-size:13px; color:#1e293b;
    cursor:pointer; transition:background .1s;
}
.supplier-option:hover  { background:#f0fdf4; color:#0b4614; }
.supplier-option.selected { background:#dcfce7; color:#0b4614; font-weight:600; }
.supplier-option.hidden { display:none; }
</style>
@endpush

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

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

// ── VALIDATION ─────────────────────────────
function clearErrors(prefix = '') {
    document.querySelectorAll(`[id^="${prefix}err_"]`).forEach(el => el.textContent = '');
    document.querySelectorAll('.k-form-input.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}
function showErrors(errors, prefix = '') {
    Object.keys(errors).forEach(field => {
        const errEl = document.getElementById(`${prefix}err_${field}`);
        const formId = prefix === 'e' ? 'formEdit' : 'formCreate';
        const input  = document.querySelector(`#${formId} [name="${field}"]`);
        if (errEl) errEl.textContent = errors[field][0];
        if (input) input.classList.add('is-invalid');
    });
}

// ── KARYAWAN DROPDOWN ──────────────────────
function toggleKaryawanDropdown() {
    const dropdown = document.getElementById('eKaryawanDropdown');
    const display  = document.getElementById('eKaryawanDisplay');
    const isOpen   = dropdown.style.display !== 'none';

    if (isOpen) {
        dropdown.style.display = 'none';
        display.classList.remove('open');
    } else {
        dropdown.style.display = 'block';
        display.classList.add('open');
        const si = document.getElementById('eKaryawanSearch');
        si.value = '';
        filterKaryawan('');
        setTimeout(() => si.focus(), 50);
    }
}

function filterKaryawan(q) {
    const search = q.toLowerCase();
    document.querySelectorAll('#eKaryawanOptions .supplier-option').forEach(opt => {
        if (!opt.dataset.nik) { opt.classList.remove('hidden'); return; } // opsi kosongkan
        const nama = (opt.dataset.nama + ' ' + opt.dataset.nik + ' ' + opt.dataset.dept).toLowerCase();
        opt.classList.toggle('hidden', !nama.includes(search));
    });
}

function selectKaryawan(nik, nama, dept) {
    document.getElementById('edit_nik_pemenang').value = nik;
    const label = document.getElementById('eKaryawanLabel');
    if (nik) {
        label.textContent = nama + (dept ? ' · ' + dept : '');
        label.style.color = '#1e293b';
    } else {
        label.textContent = 'Pilih karyawan...';
        label.style.color = '#94a3b8';
    }
    document.querySelectorAll('#eKaryawanOptions .supplier-option').forEach(opt => {
        opt.classList.toggle('selected', opt.dataset.nik === nik);
    });
    document.getElementById('eKaryawanDropdown').style.display = 'none';
    document.getElementById('eKaryawanDisplay').classList.remove('open');
}

document.addEventListener('click', function (e) {
    const wrap = document.getElementById('eKaryawanWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('eKaryawanDropdown').style.display = 'none';
        document.getElementById('eKaryawanDisplay').classList.remove('open');
    }
});

// ── MODAL CREATE ───────────────────────────
function openModalCreate() {
    document.getElementById('formCreate').reset();
    clearErrors('c');
    document.getElementById('modalCreate').classList.add('show');
}
function closeModalCreate() {
    document.getElementById('modalCreate').classList.remove('show');
}
function submitCreate(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitCreate');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    clearErrors('c');

    fetch('{{ route('penerimaan-hadiah.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: new FormData(document.getElementById('formCreate')),
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 422) {
            showErrors(body.errors, 'c');
        } else if (status === 200 || status === 201) {
            closeModalCreate();
            showToast('Hadiah berhasil ditambahkan!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(body.message ?? 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
    });
}

// ── MODAL EDIT ─────────────────────────────
function openModalEdit(id) {
    clearErrors('e');
    document.getElementById('editModalSub').textContent = 'Memuat data...';
    document.getElementById('modalEdit').classList.add('show');

    fetch(`/penerimaan-hadiah/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const s = data.item ?? data;
        document.getElementById('edit_id').value     = s.id;
        document.getElementById('edit_barang').value = s.barang ?? '';
        document.getElementById('editModalSub').textContent = s.barang;

        // Set karyawan
        const nik  = s.nik_pemenang  ?? '';
        const nama = s.nama_pemenang ?? '';
        document.getElementById('edit_nik_pemenang').value = nik;
        const label = document.getElementById('eKaryawanLabel');
        if (nik) {
            label.textContent = nama;
            label.style.color = '#1e293b';
        } else {
            label.textContent = 'Pilih karyawan...';
            label.style.color = '#94a3b8';
        }
        document.querySelectorAll('#eKaryawanOptions .supplier-option').forEach(opt => {
            opt.classList.toggle('selected', opt.dataset.nik === nik);
        });

        // QR info
        if (s.qr_code) {
            document.getElementById('qrInfoWrap').style.display = 'block';
            document.getElementById('qrInfoDisplay').textContent = s.qr_code;
        } else {
            document.getElementById('qrInfoWrap').style.display = 'none';
        }
    })
    .catch(() => { closeModalEdit(); showToast('Gagal memuat data', 'error'); });
}
function closeModalEdit() {
    document.getElementById('modalEdit').classList.remove('show');
}
function submitEdit(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitEdit');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    clearErrors('e');

    const id   = document.getElementById('edit_id').value;
    const data = new FormData(document.getElementById('formEdit'));
    data.append('_method', 'PUT');

    fetch(`/penerimaan-hadiah/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: data,
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 422) {
            showErrors(body.errors, 'e');
        } else if (status === 200) {
            closeModalEdit();
            showToast('Data berhasil diupdate!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(body.message ?? 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
    });
}

// ── MODAL DELETE ───────────────────────────
let deleteTargetId = null;
function confirmDelete(id, nama) {
    deleteTargetId = id;
    document.getElementById('deleteItemName').textContent = nama;
    document.getElementById('modalDelete').classList.add('show');
}
function closeModalDelete() {
    deleteTargetId = null;
    document.getElementById('modalDelete').classList.remove('show');
}
function doDelete() {
    if (!deleteTargetId) return;
    const btn = document.getElementById('btnConfirmDelete');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghapus...';

    fetch(`/penerimaan-hadiah/${deleteTargetId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 200) {
            closeModalDelete();
            showToast('Hadiah berhasil dihapus!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(body.message ?? 'Gagal menghapus', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-trash"></i> Hapus';
    });
}

// ── SCAN ───────────────────────────────────
function doScan() {
    const input = document.getElementById('scanInput');
    const qr    = input.value.trim();
    if (!qr) { showToast('QR code tidak boleh kosong', 'error'); return; }

    fetch('{{ route('penerimaan-hadiah.scan') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ qr_code: qr }),
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        input.value = '';
        showScanModal(body);
        if (body.status === 'success') setTimeout(() => location.reload(), 2000);
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'));
}

function showScanModal(body) {
    const modal   = document.getElementById('modalScan');
    const title   = document.getElementById('scanModalTitle');
    const sub     = document.getElementById('scanModalSub');
    const content = document.getElementById('scanModalBody');

    const cfg = {
        success: {
            icon: 'fa-circle-check', color: '#22c55e',
            title: 'Berhasil Discan!',
            sub: 'Status hadiah diperbarui',
        },
        already_taken: {
            icon: 'fa-circle-exclamation', color: '#f59e0b',
            title: 'Sudah Diambil',
            sub: 'Hadiah ini sudah pernah discan',
        },
        not_found: {
            icon: 'fa-circle-xmark', color: '#ef4444',
            title: 'QR Tidak Ditemukan',
            sub: 'Pastikan QR code benar',
        },
        no_winner: {
            icon: 'fa-circle-xmark', color: '#ef4444',
            title: 'Belum Ada Pemenang',
            sub: 'Tentukan pemenang terlebih dahulu',
        },
    };

    const c = cfg[body.status] ?? { icon: 'fa-circle-info', color: '#94a3b8', title: 'Info', sub: '' };
    title.innerHTML = `<i class="fa-solid ${c.icon}" style="color:${c.color};margin-right:8px;"></i>${c.title}`;
    sub.textContent = c.sub;

    let html = `<p style="font-size:14px;color:#475569;margin-bottom:8px;">${body.message}</p>`;
    if (body.item) {
        html += `
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px;font-size:13px;">
            <div style="margin-bottom:6px;"><span style="color:#94a3b8;font-size:11px;">BARANG</span><br><strong>${body.item.barang}</strong></div>
            <div style="margin-bottom:6px;"><span style="color:#94a3b8;font-size:11px;">PEMENANG</span><br><strong>${body.item.nama_pemenang ?? '-'}</strong></div>
            ${body.scanned_at ? `<div><span style="color:#94a3b8;font-size:11px;">WAKTU SCAN</span><br><strong>${body.scanned_at}</strong></div>` : ''}
        </div>`;
    }
    html += `
    <div class="k-form-actions" style="margin-top:16px;">
        <button class="btn btn-primary" onclick="closeModalScan()" style="width:100%;">
            <i class="fa-solid fa-check"></i> OK
        </button>
    </div>`;

    content.innerHTML = html;
    modal.classList.add('show');
}

function closeModalScan() {
    document.getElementById('modalScan').classList.remove('show');
}

// ── CLOSE ON OVERLAY / ESC ─────────────────
['modalCreate','modalEdit','modalDelete','modalScan'].forEach(id => {
    document.getElementById(id).addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('show');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['modalCreate','modalEdit','modalDelete','modalScan'].forEach(id => {
            document.getElementById(id).classList.remove('show');
        });
    }
});

function printHadiah(id) {
    window.open(`/penerimaan-hadiah/${id}/print`, '_blank');
}

// Auto focus scan input saat tidak ada modal aktif
document.getElementById('scanInput').addEventListener('focus', () => {});
</script>
@endpush