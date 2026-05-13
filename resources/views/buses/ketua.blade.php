@extends('layouts.app')
@section('title', 'Master Ketua Bus')
@section('page-title', 'Transportasi')

@section('content')

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-users-gear" style="color:#0b4614;margin-right:6px;"></i>Master Ketua Bus
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $ketuaList->total() }} ketua bus
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="{{ route('bus.card') }}" class="btn btn-outline"
               style="display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-bus"></i> Layout Bus
            </a>
            <button class="btn btn-primary" onclick="openModalCreate()">
                <i class="fa-solid fa-plus"></i> Tambah Ketua Bus
            </button>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:45px;">#</th>
                    <th>Kode Bus</th>
                    <th>Nama Ketua</th>
                    <th>Departemen</th>
                    <th>No. Telp</th>
                    <th style="width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ketuaList as $k)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($ketuaList->currentPage() - 1) * $ketuaList->perPage() }}
                    </td>
                    <td>
                        <span style="background:#0b4614;color:#fff;border-radius:6px;
                                     padding:3px 10px;font-size:13px;font-weight:700;">
                            Bus {{ $k->kode_bus }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $k->karyawan?->nama ?? '-' }}</div>
                        <div style="font-size:11px;color:#64748b;">NIK: {{ $k->nik }}</div>
                    </td>
                    <td>
                        <span class="badge badge-success">{{ $k->karyawan?->departemen ?? '-' }}</span>
                    </td>
                    <td style="font-size:13px;color:#64748b;">
                        <i class="fa-solid fa-phone" style="margin-right:4px;font-size:11px;"></i>
                        {{ $k->no_telp ?? '-' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="action-btn action-btn-warning" onclick="openModalEdit({{ $k->id }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="action-btn action-btn-danger" onclick="confirmDelete({{ $k->id }}, '{{ addslashes($k->karyawan?->nama ?? '') }}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-users-slash" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Belum ada data ketua bus
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ketuaList->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $ketuaList->firstItem() }}–{{ $ketuaList->lastItem() }} dari {{ $ketuaList->total() }} data
        </div>
        <div class="pagination">
            @if($ketuaList->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $ketuaList->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
            @endif
            @foreach($ketuaList->getUrlRange(max(1,$ketuaList->currentPage()-2),min($ketuaList->lastPage(),$ketuaList->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $ketuaList->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($ketuaList->hasMorePages())
                <a href="{{ $ketuaList->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- MODAL CREATE --}}
<div class="modal-overlay" id="modalCreate">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-plus" style="color:#0b4614;margin-right:8px;"></i>Tambah Ketua Bus
                </div>
                <div class="modal-subtitle">Isi data ketua bus baru</div>
            </div>
            <button class="modal-close" onclick="closeModalCreate()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div class="k-form-group">
                <label class="k-form-label">Kode Bus <span class="required">*</span></label>
                <input type="text" id="c_kode_bus" class="k-form-input" placeholder="Contoh: A, B, AA, AB...">
                <div class="k-form-error" id="c_err_kode_bus"></div>
            </div>
            <div class="k-form-group">
                <label class="k-form-label">Ketua Bus <span class="required">*</span></label>
                <select id="c_nik" class="k-form-input">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $kar)
                        <option value="{{ $kar->nik }}">{{ $kar->nama }} — {{ $kar->departemen }}</option>
                    @endforeach
                </select>
                <div class="k-form-error" id="c_err_nik"></div>
            </div>
            <div class="k-form-group">
                <label class="k-form-label">No. Telp</label>
                <input type="text" id="c_no_telp" class="k-form-input" placeholder="08xx...">
                <div class="k-form-error" id="c_err_no_telp"></div>
            </div>
            <div class="k-form-actions">
                <button type="button" class="btn btn-outline" onclick="closeModalCreate()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" id="btnCreate" class="btn btn-primary" onclick="doCreate()">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-pen" style="color:#f59e0b;margin-right:8px;"></i>Edit Ketua Bus
                </div>
                <div class="modal-subtitle" id="editSub">–</div>
            </div>
            <button class="modal-close" onclick="closeModalEdit()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="e_id">
            <div class="k-form-group">
                <label class="k-form-label">Kode Bus <span class="required">*</span></label>
                <input type="text" id="e_kode_bus" class="k-form-input">
                <div class="k-form-error" id="e_err_kode_bus"></div>
            </div>
            <div class="k-form-group">
                <label class="k-form-label">Ketua Bus <span class="required">*</span></label>
                <select id="e_nik" class="k-form-input">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $kar)
                        <option value="{{ $kar->nik }}">{{ $kar->nama }} — {{ $kar->departemen }}</option>
                    @endforeach
                </select>
                <div class="k-form-error" id="e_err_nik"></div>
            </div>
            <div class="k-form-group">
                <label class="k-form-label">No. Telp</label>
                <input type="text" id="e_no_telp" class="k-form-input">
                <div class="k-form-error" id="e_err_no_telp"></div>
            </div>
            <div class="k-form-actions">
                <button type="button" class="btn btn-outline" onclick="closeModalEdit()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" id="btnEdit" class="btn btn-primary" onclick="doEdit()">
                    <i class="fa-solid fa-floppy-disk"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DELETE --}}
<div class="modal-overlay" id="modalDelete">
    <div class="modal-box" style="max-width:400px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;margin-right:8px;"></i>Hapus Ketua Bus
                </div>
                <div class="modal-subtitle">Tindakan ini tidak bisa dibatalkan</div>
            </div>
            <button class="modal-close" onclick="closeModalDelete()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p style="color:#475569;font-size:14px;margin-bottom:20px;">
                Yakin ingin menghapus ketua bus <strong id="deleteTargetName"></strong>?
            </p>
            <div class="k-form-actions">
                <button type="button" class="btn btn-outline" onclick="closeModalDelete()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" id="btnConfirmDelete" class="btn k-btn-danger" onclick="doDelete()">
                    <i class="fa-solid fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

$(document).ready(function () {
    $('#c_nik').select2({
        placeholder   : '-- Cari atau pilih karyawan --',
        allowClear    : true,
        width         : '100%',
        dropdownParent: $('#modalCreate'),
    });
    $('#e_nik').select2({
        placeholder   : '-- Cari atau pilih karyawan --',
        allowClear    : true,
        width         : '100%',
        dropdownParent: $('#modalEdit'),
    });
});

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
    ['kode_bus','nik','no_telp'].forEach(f => {
        const el = document.getElementById(`${prefix}_err_${f}`);
        if (el) el.textContent = '';
    });
}

// ── CREATE ────────────────────────────────────────────────────────────────────
function openModalCreate() {
    clearErrors('c');
    document.getElementById('c_kode_bus').value = '';
    document.getElementById('c_no_telp').value  = '';
    $('#c_nik').val(null).trigger('change');
    document.getElementById('modalCreate').classList.add('show');
}
function closeModalCreate() {
    document.getElementById('modalCreate').classList.remove('show');
}
async function doCreate() {
    clearErrors('c');
    const btn = document.getElementById('btnCreate');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res  = await fetch('{{ route("bus.ketua.store") }}', {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body   : JSON.stringify({
                kode_bus : document.getElementById('c_kode_bus').value.trim().toUpperCase(),
                nik      : $('#c_nik').val(),
                no_telp  : document.getElementById('c_no_telp').value.trim(),
            }),
        });
        const data = await res.json();
        if (res.status === 422) {
            Object.keys(data.errors).forEach(f => {
                const el = document.getElementById(`c_err_${f}`);
                if (el) el.textContent = data.errors[f][0];
            });
        } else if (res.ok) {
            closeModalCreate();
            showToast('Ketua bus berhasil ditambahkan!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message ?? 'Terjadi kesalahan', 'error');
        }
    } catch { showToast('Gagal menghubungi server', 'error'); }
    finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
    }
}

// ── EDIT ──────────────────────────────────────────────────────────────────────
function openModalEdit(id) {
    clearErrors('e');
    document.getElementById('editSub').textContent = 'Memuat...';
    document.getElementById('modalEdit').classList.add('show');

    fetch(`/bus/ketua/${id}/edit`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            document.getElementById('e_id').value       = data.id;
            document.getElementById('e_kode_bus').value = data.kode_bus;
            document.getElementById('e_no_telp').value  = data.no_telp ?? '';
            $('#e_nik').val(data.nik).trigger('change');
            document.getElementById('editSub').textContent = `Bus ${data.kode_bus}`;
        });
}
function closeModalEdit() {
    document.getElementById('modalEdit').classList.remove('show');
}
async function doEdit() {
    clearErrors('e');
    const id  = document.getElementById('e_id').value;
    const btn = document.getElementById('btnEdit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res  = await fetch(`/bus/ketua/${id}`, {
            method : 'PUT',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body   : JSON.stringify({
                kode_bus : document.getElementById('e_kode_bus').value.trim().toUpperCase(),
                nik      : $('#e_nik').val(),
                no_telp  : document.getElementById('e_no_telp').value.trim(),
            }),
        });
        const data = await res.json();
        if (res.status === 422) {
            Object.keys(data.errors).forEach(f => {
                const el = document.getElementById(`e_err_${f}`);
                if (el) el.textContent = data.errors[f][0];
            });
        } else if (res.ok) {
            closeModalEdit();
            showToast('Data berhasil diupdate!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message ?? 'Terjadi kesalahan', 'error');
        }
    } catch { showToast('Gagal menghubungi server', 'error'); }
    finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Update';
    }
}

// ── DELETE ────────────────────────────────────────────────────────────────────
let deleteId = null;
function confirmDelete(id, nama) {
    deleteId = id;
    document.getElementById('deleteTargetName').textContent = nama;
    document.getElementById('modalDelete').classList.add('show');
}
function closeModalDelete() {
    deleteId = null;
    document.getElementById('modalDelete').classList.remove('show');
}
async function doDelete() {
    if (!deleteId) return;
    const btn = document.getElementById('btnConfirmDelete');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghapus...';

    try {
        const res  = await fetch(`/bus/ketua/${deleteId}`, {
            method : 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) {
            closeModalDelete();
            showToast('Data berhasil dihapus!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message ?? 'Gagal menghapus', 'error');
        }
    } catch { showToast('Gagal menghubungi server', 'error'); }
    finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-trash"></i> Hapus';
    }
}

// ── Close on overlay / ESC ────────────────────────────────────────────────────
['modalCreate','modalEdit','modalDelete'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['modalCreate','modalEdit','modalDelete'].forEach(id => {
            document.getElementById(id).classList.remove('show');
        });
    }
});
</script>
@endpush