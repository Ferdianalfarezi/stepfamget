@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'User')

@section('content')

{{-- FILTER --}}
<div class="card" style="margin-bottom:10px;">
    <div class="card-body" style="padding:14px 20px;">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="filters">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari username atau nama..."
                           value="{{ request('search') }}" style="width:260px;">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request('search'))
                    <a href="{{ route('users.index') }}" class="btn btn-outline">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- TABLE --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-users-gear" style="color:#0b4614;margin-right:6px;"></i>Manajemen User Admin
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $users->total() }} user admin
            </div>
        </div>
        <button class="btn btn-primary" onclick="openModalCreate()">
            <i class="fa-solid fa-plus"></i> Tambah User
        </button>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:45px;">#</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Dibuat</th>
                    <th style="width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:10px;background:#e8f5e9;color:#0b4614;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:14px;font-weight:800;flex-shrink:0;">
                                {{ strtoupper(substr($u->nama, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:13px;">{{ $u->username }}</div>
                                @if($u->id === auth()->id())
                                    <div style="font-size:10px;color:#0b4614;font-weight:600;">● Anda</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:#374151;">{{ $u->nama }}</td>
                    <td>
                        <span class="badge badge-success">
                            <i class="fa-solid fa-shield-halved" style="font-size:10px;margin-right:3px;"></i>Admin
                        </span>
                    </td>
                    <td style="font-size:12px;color:#94a3b8;">
                        {{ $u->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="action-btn action-btn-warning"
                                    onclick="openModalEdit({{ $u->id }})"
                                    title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            @if($u->id !== auth()->id())
                            <button class="action-btn action-btn-danger"
                                    onclick="confirmDelete({{ $u->id }}, '{{ addslashes($u->username) }}')"
                                    title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-users-slash" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Tidak ada user admin
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} data
        </div>
        <div class="pagination">
            @if($users->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif
            @foreach($users->getUrlRange(max(1,$users->currentPage()-2), min($users->lastPage(),$users->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $users->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
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
                    <i class="fa-solid fa-user-plus" style="color:#0b4614;margin-right:8px;"></i>Tambah User Admin
                </div>
                <div class="modal-subtitle">Buat akun admin baru</div>
            </div>
            <button class="modal-close" onclick="closeModalCreate()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formCreate" onsubmit="submitCreate(event)">
                @csrf
                <div class="k-form-grid">
                    <div class="k-form-group k-form-col-2">
                        <label class="k-form-label">Nama <span class="required">*</span></label>
                        <input type="text" name="nama" class="k-form-input" placeholder="Nama lengkap" required>
                        <div class="k-form-error" id="kerr_nama"></div>
                    </div>
                    <div class="k-form-group k-form-col-2">
                        <label class="k-form-label">Username <span class="required">*</span></label>
                        <input type="text" name="username" class="k-form-input" placeholder="Username login" required>
                        <div class="k-form-error" id="kerr_username"></div>
                    </div>
                    <div class="k-form-group k-form-col-2">
                        <label class="k-form-label">Password <span class="required">*</span></label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="create_pw" class="k-form-input"
                                   placeholder="Min. 6 karakter" required style="padding-right:40px;">
                            <button type="button" onclick="togglePw('create_pw','create_pw_eye')"
                                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                           background:none;border:none;cursor:pointer;color:#94a3b8;">
                                <i class="fa-solid fa-eye" id="create_pw_eye" style="font-size:13px;"></i>
                            </button>
                        </div>
                        <div class="k-form-error" id="kerr_password"></div>
                    </div>
                </div>
                <div class="k-form-actions" style="margin-top:20px;">
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

{{-- MODAL EDIT --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color:#f59e0b;margin-right:8px;"></i>Edit User
                </div>
                <div class="modal-subtitle" id="editSub">–</div>
            </div>
            <button class="modal-close" onclick="closeModalEdit()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formEdit" onsubmit="submitEdit(event)">
                @csrf
                <input type="hidden" id="edit_id">
                <div class="k-form-grid">
                    <div class="k-form-group k-form-col-2">
                        <label class="k-form-label">Nama <span class="required">*</span></label>
                        <input type="text" name="nama" id="edit_nama" class="k-form-input" required>
                        <div class="k-form-error" id="edit_kerr_nama"></div>
                    </div>
                    <div class="k-form-group k-form-col-2">
                        <label class="k-form-label">Username <span class="required">*</span></label>
                        <input type="text" name="username" id="edit_username" class="k-form-input" required>
                        <div class="k-form-error" id="edit_kerr_username"></div>
                    </div>
                    <div class="k-form-group k-form-col-2">
                        <label class="k-form-label">Password Baru</label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="edit_pw" class="k-form-input"
                                   placeholder="Kosongkan jika tidak diubah" style="padding-right:40px;">
                            <button type="button" onclick="togglePw('edit_pw','edit_pw_eye')"
                                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                           background:none;border:none;cursor:pointer;color:#94a3b8;">
                                <i class="fa-solid fa-eye" id="edit_pw_eye" style="font-size:13px;"></i>
                            </button>
                        </div>
                        <div class="k-form-error" id="edit_kerr_password"></div>
                    </div>
                </div>
                <div class="k-form-actions" style="margin-top:20px;">
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

{{-- MODAL DELETE --}}
<div class="modal-overlay" id="modalDelete">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;margin-right:8px;"></i>Hapus User
                </div>
                <div class="modal-subtitle">Tindakan ini tidak bisa dibatalkan</div>
            </div>
            <button class="modal-close" onclick="closeModalDelete()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <p style="color:#475569;font-size:14px;margin-bottom:20px;">
                Yakin ingin menghapus user <strong id="deleteUserName" style="color:#1e293b;"></strong>?
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
const CSRF = '{{ csrf_token() }}';

// ── Toast ─────────────────────────────────────────────────────────────────────
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

// ── Validation helpers ────────────────────────────────────────────────────────
function clearErrors(prefix = '') {
    document.querySelectorAll(`[id^="${prefix}kerr_"]`).forEach(el => el.textContent = '');
    document.querySelectorAll('.k-form-input.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}
function showErrors(errors, prefix = '') {
    Object.keys(errors).forEach(field => {
        const errEl = document.getElementById(`${prefix}kerr_${field}`);
        if (errEl) errEl.textContent = errors[field][0];
    });
}

// ── Toggle password visibility ────────────────────────────────────────────────
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type  = 'text';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        input.type  = 'password';
        icon.className = 'fa-solid fa-eye';
    }
}

// ── Modal Create ──────────────────────────────────────────────────────────────
function openModalCreate() {
    document.getElementById('formCreate').reset();
    clearErrors();
    document.getElementById('modalCreate').classList.add('show');
}
function closeModalCreate() {
    document.getElementById('modalCreate').classList.remove('show');
}

function submitCreate(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitCreate');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    btn.disabled  = true;
    clearErrors();

    fetch('{{ route('users.store') }}', {
        method : 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body   : new FormData(document.getElementById('formCreate')),
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 422) { showErrors(body.errors); }
        else if (status === 201) {
            closeModalCreate();
            showToast('User berhasil ditambahkan!');
            setTimeout(() => location.reload(), 800);
        } else { showToast(body.message ?? 'Terjadi kesalahan', 'error'); }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => { btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan'; btn.disabled = false; });
}

// ── Modal Edit ────────────────────────────────────────────────────────────────
function openModalEdit(id) {
    clearErrors('edit_');
    document.getElementById('editSub').textContent = 'Memuat data...';
    document.getElementById('modalEdit').classList.add('show');

    fetch(`/users/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const u = data.user;
        document.getElementById('edit_id').value       = u.id;
        document.getElementById('edit_nama').value     = u.nama;
        document.getElementById('edit_username').value = u.username;
        document.getElementById('edit_pw').value       = '';
        document.getElementById('editSub').textContent = u.username;
    })
    .catch(() => { closeModalEdit(); showToast('Gagal memuat data', 'error'); });
}
function closeModalEdit() {
    document.getElementById('modalEdit').classList.remove('show');
}

function submitEdit(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitEdit');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    btn.disabled  = true;
    clearErrors('edit_');

    const id   = document.getElementById('edit_id').value;
    const data = new FormData(document.getElementById('formEdit'));
    data.append('_method', 'PUT');

    fetch(`/users/${id}`, {
        method : 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body   : data,
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 422) { showErrors(body.errors, 'edit_'); }
        else if (status === 200) {
            closeModalEdit();
            showToast('User berhasil diupdate!');
            setTimeout(() => location.reload(), 800);
        } else { showToast(body.message ?? 'Terjadi kesalahan', 'error'); }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => { btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Update'; btn.disabled = false; });
}

// ── Modal Delete ──────────────────────────────────────────────────────────────
let deleteTargetId = null;

function confirmDelete(id, username) {
    deleteTargetId = id;
    document.getElementById('deleteUserName').textContent = username;
    document.getElementById('modalDelete').classList.add('show');
}
function closeModalDelete() {
    deleteTargetId = null;
    document.getElementById('modalDelete').classList.remove('show');
}
function doDelete() {
    if (!deleteTargetId) return;
    const btn = document.getElementById('btnConfirmDelete');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghapus...';
    btn.disabled  = true;

    fetch(`/users/${deleteTargetId}`, {
        method : 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 200) {
            closeModalDelete();
            showToast('User berhasil dihapus!');
            setTimeout(() => location.reload(), 800);
        } else { showToast(body.message ?? 'Gagal menghapus', 'error'); }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => { btn.innerHTML = '<i class="fa-solid fa-trash"></i> Hapus'; btn.disabled = false; });
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