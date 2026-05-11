@extends('layouts.app')
@section('title', 'Data Supplier')
@section('page-title', 'Supplier')

@section('content')
{{-- ── FILTER BAR ── --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('suppliers.index') }}">
            <div class="filters">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama atau alamat..."
                           value="{{ request('search') }}" style="width:300px;">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── TABLE CARD ── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-truck" style="color:#0b4614;margin-right:6px;"></i>Data Supplier
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $suppliers->total() }} supplier ditemukan
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <button class="btn btn-primary" onclick="openModalCreate()">
                <i class="fa-solid fa-plus"></i> Tambah Supplier
            </button>
            <button class="btn" style="background:#0284c7;color:#fff;border:none;display:inline-flex;align-items:center;gap:6px;"
                    onclick="openModalImport()">
                <i class="fa-solid fa-file-import"></i> Import Excel
            </button>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Nama Supplier</th>
                    <th>Alamat</th>
                    <th style="width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13.5px;">{{ $s->nama }}</div>
                    </td>
                    <td style="font-size:13px;color:#475569;max-width:420px;">
                        {{ $s->alamat ?? '-' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="action-btn action-btn-warning"
                                    onclick="openModalEdit({{ $s->id }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="action-btn action-btn-danger"
                                    onclick="confirmDelete({{ $s->id }}, '{{ addslashes($s->nama) }}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-truck-slash" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Tidak ada data supplier
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($suppliers->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $suppliers->firstItem() }}–{{ $suppliers->lastItem() }}
            dari {{ $suppliers->total() }} data
        </div>
        <div class="pagination">
            @if($suppliers->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $suppliers->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            @foreach($suppliers->getUrlRange(
                max(1, $suppliers->currentPage()-2),
                min($suppliers->lastPage(), $suppliers->currentPage()+2)
            ) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $suppliers->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($suppliers->hasMorePages())
                <a href="{{ $suppliers->nextPageUrl() }}" class="page-link">
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
    <div class="modal-box" style="max-width:520px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-truck" style="color:#0b4614;margin-right:8px;"></i>Tambah Supplier
                </div>
                <div class="modal-subtitle">Isi data supplier baru</div>
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
                        <label class="k-form-label">Nama Supplier <span class="required">*</span></label>
                        <input type="text" name="nama" id="create_nama" class="k-form-input"
                               placeholder="Contoh: PT. Maju Jaya" required maxlength="150">
                        <div class="k-form-error" id="cerr_nama"></div>
                    </div>
                    <div class="k-form-group">
                        <label class="k-form-label">Alamat</label>
                        <textarea name="alamat" id="create_alamat" class="k-form-input"
                                  rows="3" placeholder="Alamat lengkap supplier"></textarea>
                        <div class="k-form-error" id="cerr_alamat"></div>
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
{{-- MODAL: EDIT                   --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box" style="max-width:520px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color:#f59e0b;margin-right:8px;"></i>Edit Supplier
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
                    <div class="k-form-group">
                        <label class="k-form-label">Nama Supplier <span class="required">*</span></label>
                        <input type="text" name="nama" id="edit_nama" class="k-form-input"
                               required maxlength="150">
                        <div class="k-form-error" id="eerr_nama"></div>
                    </div>
                    <div class="k-form-group">
                        <label class="k-form-label">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" class="k-form-input" rows="3"></textarea>
                        <div class="k-form-error" id="eerr_alamat"></div>
                    </div>
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

{{-- ============================= --}}
{{-- MODAL: DELETE CONFIRM         --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalDelete">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;margin-right:8px;"></i>Hapus Supplier
                </div>
                <div class="modal-subtitle">Tindakan ini tidak bisa dibatalkan</div>
            </div>
            <button class="modal-close" onclick="closeModalDelete()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <p style="color:#475569;font-size:14px;margin-bottom:20px;">
                Yakin ingin menghapus supplier
                <strong id="deleteSupplierName" style="color:#1e293b;"></strong>?
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
{{-- ============================= --}}
{{-- MODAL: IMPORT EXCEL           --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalImport">
    <div class="modal-box" style="max-width:460px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-file-import" style="color:#0284c7;margin-right:8px;"></i>Import Excel
                </div>
                <div class="modal-subtitle">Upload file .xlsx / .xls / .csv</div>
            </div>
            <button class="modal-close" onclick="closeModalImport()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">

            {{-- Format info — compact pill style --}}
            <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:14px 16px;margin-bottom:18px;">
                <div style="font-size:12px;font-weight:700;color:#0369a1;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                    <i class="fa-solid fa-circle-info"></i> Format kolom yang dibutuhkan
                </div>
                <div style="display:flex;gap:8px;margin-bottom:10px;">
                    <div style="flex:1;background:#fff;border:1px solid #e0f2fe;border-radius:8px;padding:10px 12px;">
                        <div style="font-family:monospace;font-size:12px;font-weight:700;color:#0284c7;">nama</div>
                        <div style="font-size:11px;color:#64748b;margin-top:2px;">Nama supplier</div>
                        <span style="font-size:10px;background:#fef2f2;color:#ef4444;border-radius:4px;padding:1px 6px;margin-top:4px;display:inline-block;">wajib</span>
                    </div>
                    <div style="flex:1;background:#fff;border:1px solid #e0f2fe;border-radius:8px;padding:10px 12px;">
                        <div style="font-family:monospace;font-size:12px;font-weight:700;color:#0284c7;">alamat</div>
                        <div style="font-size:11px;color:#64748b;margin-top:2px;">Alamat lengkap</div>
                        <span style="font-size:10px;background:#f1f5f9;color:#94a3b8;border-radius:4px;padding:1px 6px;margin-top:4px;display:inline-block;">opsional</span>
                    </div>
                </div>
                <div style="font-size:11px;color:#64748b;display:flex;align-items:flex-start;gap:5px;">
                    <i class="fa-solid fa-circle-exclamation" style="color:#94a3b8;margin-top:1px;"></i>
                    Baris pertama = heading row. Jika nama sudah ada, data akan di-<em>update</em>.
                </div>
            </div>

            <form id="formImport" onsubmit="submitImport(event)">
                @csrf

                {{-- Drop zone style file input --}}
                <div class="k-form-group" style="margin-bottom:6px;">
                    <label class="k-form-label">Pilih File <span class="required">*</span></label>
                    <label for="import_file" id="dropZone"
                           style="display:flex;flex-direction:column;align-items:center;justify-content:center;
                                  gap:8px;border:2px dashed #cbd5e1;border-radius:10px;padding:24px 16px;
                                  cursor:pointer;transition:border-color .2s,background .2s;background:#fafafa;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:28px;color:#94a3b8;" id="dropIcon"></i>
                        <div style="text-align:center;">
                            <div style="font-size:13px;font-weight:600;color:#475569;" id="dropLabel">Klik untuk pilih file</div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;">.xlsx &nbsp;·&nbsp; .xls &nbsp;·&nbsp; .csv</div>
                        </div>
                        <input type="file" name="file" id="import_file" accept=".xlsx,.xls,.csv"
                               required style="display:none;"
                               onchange="updateDropZone(this)">
                    </label>
                    <div class="k-form-error" id="ierr_file"></div>
                </div>

                {{-- Error list --}}
                <div id="importErrorList" style="display:none;margin-top:10px;
                     background:#fef2f2;border:1px solid #fecaca;border-radius:8px;
                     padding:10px 14px;font-size:12px;color:#b91c1c;max-height:150px;overflow-y:auto;">
                </div>

                <div class="k-form-actions" style="margin-top:18px;">
                    <button type="button" class="btn btn-outline" onclick="closeModalImport()">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitImport"
                            style="background:#0284c7;">
                        <i class="fa-solid fa-upload"></i> Upload &amp; Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

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
        const form  = prefix === 'e' ? 'formEdit' : 'formCreate';
        const input = document.querySelector(`#${form} [name="${field}"]`);
        if (errEl) errEl.textContent = errors[field][0];
        if (input) input.classList.add('is-invalid');
    });
}

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

    fetch('{{ route('suppliers.store') }}', {
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
            showToast('Supplier berhasil ditambahkan!');
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

    fetch(`/suppliers/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const s = data.supplier ?? data;
        document.getElementById('edit_id').value      = s.id;
        document.getElementById('edit_nama').value    = s.nama   ?? '';
        document.getElementById('edit_alamat').value  = s.alamat ?? '';
        document.getElementById('editModalSub').textContent = `ID: ${s.id}`;
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

    fetch(`/suppliers/${id}`, {
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
            showToast('Data supplier berhasil diupdate!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(body.message ?? 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Update';
    });
}

// ── MODAL DELETE ───────────────────────────
let deleteTargetId = null;
function confirmDelete(id, nama) {
    deleteTargetId = id;
    document.getElementById('deleteSupplierName').textContent = nama;
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

    fetch(`/suppliers/${deleteTargetId}`, {
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
            showToast('Supplier berhasil dihapus!');
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

// ── DROP ZONE ──────────────────────────────
function updateDropZone(input) {
    const zone  = document.getElementById('dropZone');
    const label = document.getElementById('dropLabel');
    const icon  = document.getElementById('dropIcon');
    if (input.files.length > 0) {
        const name = input.files[0].name;
        label.textContent = name;
        label.style.color = '#0284c7';
        icon.style.color  = '#0284c7';
        zone.style.borderColor  = '#0284c7';
        zone.style.background   = '#f0f9ff';
    } else {
        label.textContent = 'Klik untuk pilih file';
        label.style.color = '#475569';
        icon.style.color  = '#94a3b8';
        zone.style.borderColor  = '#cbd5e1';
        zone.style.background   = '#fafafa';
    }
}

// ── MODAL IMPORT ───────────────────────────
function openModalImport() {
    document.getElementById('formImport').reset();
    document.getElementById('ierr_file').textContent = '';
    document.getElementById('importErrorList').style.display = 'none';
    document.getElementById('importErrorList').innerHTML = '';
    updateDropZone(document.getElementById('import_file'));
    document.getElementById('modalImport').classList.add('show');
}
function closeModalImport() {
    document.getElementById('modalImport').classList.remove('show');
}
function submitImport(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitImport');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengimport...';
    document.getElementById('ierr_file').textContent = '';
    document.getElementById('importErrorList').style.display = 'none';

    fetch('{{ route('suppliers.import') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: new FormData(document.getElementById('formImport')),
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 200) {
            closeModalImport();
            showToast(body.message ?? 'Import berhasil!');
            setTimeout(() => location.reload(), 800);
        } else if (status === 422) {
            // validation error dari file input
            if (body.errors?.file) {
                document.getElementById('ierr_file').textContent = body.errors.file[0];
            }
            // validation error dari baris Excel
            if (Array.isArray(body.errors)) {
                const listEl = document.getElementById('importErrorList');
                listEl.innerHTML = '<strong style="display:block;margin-bottom:6px;">Error per baris:</strong>'
                    + body.errors.map(e => `<div>• ${e}</div>`).join('');
                listEl.style.display = 'block';
            }
            showToast(body.message ?? 'Import gagal validasi', 'error');
        } else {
            showToast(body.message ?? 'Import gagal', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-upload"></i> Upload & Import';
    });
}

// ── CLOSE ON OVERLAY / ESC ─────────────────
['modalCreate','modalEdit','modalDelete','modalImport'].forEach(id => {
    document.getElementById(id).addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('show');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['modalCreate','modalEdit','modalDelete','modalImport'].forEach(id => {
            document.getElementById(id).classList.remove('show');
        });
    }
});
</script>
@endpush