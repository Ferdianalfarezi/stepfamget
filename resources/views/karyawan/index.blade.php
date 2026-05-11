@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('page-title', 'Karyawan')

@section('content')
<!-- FILTER BAR -->
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('karyawan.index') }}">
            <div class="filters">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, NIK, departemen..."
                           value="{{ request('search') }}" style="width:280px;">
                </div>
                <select name="departemen" class="form-control" style="width:auto;min-width:160px;">
                    <option value="">Semua Departemen</option>
                    @foreach($departemenList as $dept)
                        <option value="{{ $dept }}" {{ request('departemen') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
                <select name="keterangan" class="form-control" style="width:auto;min-width:130px;">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('keterangan') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Non-Aktif" {{ request('keterangan') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
                @if(request()->hasAny(['search','departemen','keterangan']))
                    <a href="{{ route('karyawan.index') }}" class="btn btn-outline"><i class="fa-solid fa-xmark"></i> Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- TABLE CARD -->
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-users" style="color:#0b4614;margin-right:2px;"></i>Data Karyawan
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $karyawans->total() }} karyawan ditemukan
            </div>
        </div>
        {{-- Grup tombol kanan --}}
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="{{ route('karyawan.export', request()->query()) }}"
            class="btn"
            style="background:#16a34a;color:#fff;border:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
            <button class="btn btn-primary" onclick="openModalCreate()">
                <i class="fa-solid fa-plus"></i> Tambah Karyawan
            </button>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:45px;">#</th>
                    <th>NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Departemen</th>
                    <th>Jml. Keluarga</th>
                    <th>Status</th>
                    <th>Hadir</th>
                    <th style="width:150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karyawans as $k)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($karyawans->currentPage() - 1) * $karyawans->perPage() }}
                    </td>
                    <td>
                        <span style="font-size:13px;background:#f1f5f900;padding:2px 8px;border-radius:5px;">
                            {{ $k->nik }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13.5px;">{{ $k->nama }}</div>
                        <div style="font-size:11px;color:#0b4614;">NIK Login: {{ $k->nik_login }}</div>
                    </td>
                    <td><span class="badge badge-success">{{ $k->departemen }}</span></td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:13px;">
                            <i class="fa-solid fa-people-group" style="color:#0b4614;font-size:12px;"></i>
                            {{ $k->jumlah_keluarga }} orang
                        </span>
                    </td>
                    <td>
                        @if($k->keterangan == 'Aktif')
                            <span class="badge badge-success">
                                <i class="fa-solid fa-circle" style="font-size:7px;"></i> Aktif
                            </span>
                        @else
                            <span class="badge badge-danger">
                                <i class="fa-solid fa-circle" style="font-size:7px;"></i> {{ $k->keterangan }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($k->status_kehadiran)
                            <span class="badge badge-success">Ya</span>
                        @else
                            <span class="badge badge-gray">Tidak</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="action-btn" onclick="showDetail({{ $k->id }}, '{{ addslashes($k->nama) }}')">
                                <i class="fa-solid fa-eye"></i> 
                            </button>
                            <button class="action-btn action-btn-warning" onclick="openModalEdit({{ $k->id }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="action-btn action-btn-danger" onclick="confirmDelete({{ $k->id }}, '{{ addslashes($k->nama) }}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-users-slash" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Tidak ada data karyawan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    @if($karyawans->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $karyawans->firstItem() }}–{{ $karyawans->lastItem() }} dari {{ $karyawans->total() }} data
        </div>
        <div class="pagination">
            @if($karyawans->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $karyawans->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            @foreach($karyawans->getUrlRange(max(1, $karyawans->currentPage()-2), min($karyawans->lastPage(), $karyawans->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $karyawans->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($karyawans->hasMorePages())
                <a href="{{ $karyawans->nextPageUrl() }}" class="page-link">
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
    <div class="modal-box modal-box-lg">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-user-plus" style="color:#0b4614;margin-right:8px;"></i>Tambah Karyawan
                </div>
                <div class="modal-subtitle">Isi data karyawan baru beserta anggota keluarga</div>
            </div>
            <button class="modal-close" onclick="closeModalCreate()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            @include('karyawan.create')
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: EDIT                   --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box modal-box-md">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color:#f59e0b;margin-right:8px;"></i>Edit Karyawan
                </div>
                <div class="modal-subtitle" id="editModalSub">Memuat data...</div>
            </div>
            <button class="modal-close" onclick="closeModalEdit()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            @include('karyawan.edit')
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: DETAIL                 --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box modal-box-lg">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="modalName">–</div>
                <div class="modal-subtitle" id="modalSub">–</div>
            </div>
            <button class="modal-close" onclick="closeModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body" id="modalContent">
            <div class="spinner"></div>
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
                    <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;margin-right:8px;"></i>Hapus Karyawan
                </div>
                <div class="modal-subtitle">Tindakan ini tidak bisa dibatalkan</div>
            </div>
            <button class="modal-close" onclick="closeModalDelete()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <p style="color:#475569;font-size:14px;margin-bottom:20px;">
                Yakin ingin menghapus karyawan
                <strong id="deleteKaryawanName" style="color:#1e293b;"></strong>?
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

@endsection
@push('scripts')
<script>
// ─────────────────────────────────────────────
// CSRF
// ─────────────────────────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ─────────────────────────────────────────────
// TOAST
// ─────────────────────────────────────────────
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

// ─────────────────────────────────────────────
// VALIDATION HELPERS
// ─────────────────────────────────────────────
function clearErrors(prefix = '') {
    document.querySelectorAll(`[id^="${prefix}kerr_"]`).forEach(el => el.textContent = '');
    document.querySelectorAll('.k-form-input.is-invalid, .k-fc.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function showErrors(errors, prefix = '') {
    Object.keys(errors).forEach(field => {
        const errEl  = document.getElementById(`${prefix}kerr_${field}`);
        const formId = prefix ? 'formEdit' : 'formCreate';
        const input  = document.querySelector(`#${formId} [name="${field}"]`);
        if (errEl) errEl.textContent = errors[field][0];
        if (input) input.classList.add('is-invalid');
    });
}

// ─────────────────────────────────────────────
// MODAL CREATE
// ─────────────────────────────────────────────
function openModalCreate() {
    document.getElementById('formCreate').reset();
    clearErrors();
    if (typeof _resetFamilyRows === 'function') _resetFamilyRows();
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
    clearErrors();

    fetch('{{ route('karyawan.store') }}', {
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
            showErrors(body.errors);
        } else if (status === 200 || status === 201) {
            closeModalCreate();
            showToast('Karyawan berhasil ditambahkan!');
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

// ─────────────────────────────────────────────
// MODAL EDIT
// ─────────────────────────────────────────────
function openModalEdit(id) {
    clearErrors('edit_');
    document.getElementById('editModalSub').textContent = 'Memuat data...';
    document.getElementById('modalEdit').classList.add('show');

    fetch(`/karyawan/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const k       = data.karyawan ?? data;   // support both {karyawan, details} or flat
        const details = data.details ?? [];

        document.getElementById('edit_id').value               = k.id;
        document.getElementById('edit_nik').value              = k.nik ?? '';
        document.getElementById('edit_nik_login').value        = k.nik_login ?? '';
        document.getElementById('edit_nama').value             = k.nama ?? '';
        document.getElementById('edit_departemen').value       = k.departemen ?? '';
        document.getElementById('edit_keterangan').value       = k.keterangan ?? 'Aktif';
        document.getElementById('edit_status_kehadiran').value = k.status_kehadiran ? '1' : '0';
        document.getElementById('editModalSub').textContent    = `NIK: ${k.nik}`;

        // load family rows
        if (typeof loadEditFamilyRows === 'function') loadEditFamilyRows(details);
    })
    .catch(() => {
        closeModalEdit();
        showToast('Gagal memuat data', 'error');
    });
}
function closeModalEdit() {
    document.getElementById('modalEdit').classList.remove('show');
}

function submitEdit(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitEdit');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    clearErrors('edit_');

    const id   = document.getElementById('edit_id').value;
    const data = new FormData(document.getElementById('formEdit'));
    data.append('_method', 'PUT');

    fetch(`/karyawan/${id}`, {
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
            showErrors(body.errors, 'edit_');
        } else if (status === 200) {
            closeModalEdit();
            showToast('Data karyawan berhasil diupdate!');
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

// ─────────────────────────────────────────────
// MODAL DELETE
// ─────────────────────────────────────────────
let deleteTargetId = null;

function confirmDelete(id, nama) {
    deleteTargetId = id;
    document.getElementById('deleteKaryawanName').textContent = nama;
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

    fetch(`/karyawan/${deleteTargetId}`, {
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
            showToast('Karyawan berhasil dihapus!');
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

// ─────────────────────────────────────────────
// MODAL DETAIL
// ─────────────────────────────────────────────
function showDetail(id, nama) {
    document.getElementById('modalName').textContent    = nama;
    document.getElementById('modalSub').textContent     = 'Memuat data...';
    document.getElementById('modalContent').innerHTML   = '<div class="spinner"></div>';
    document.getElementById('modalDetail').classList.add('show');

    fetch(`/karyawan/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const k       = data.karyawan;
        const details = data.details;

        document.getElementById('modalSub').textContent = `NIK: ${k.nik} · ${k.departemen}`;

        const hubunganClass = h => ({
            'Karyawan' :'hubungan-karyawan', 'Karyawati':'hubungan-karyawati',
            'Istri'    :'hubungan-istri',    'Suami'    :'hubungan-suami',
            'Anak'     :'hubungan-anak',     'Saudara'  :'hubungan-saudara',
        }[h] || 'badge-gray');

        let rows = '';
        if (details.length > 0) {
            details.forEach((d, i) => {
                const tl = d.tanggal_lahir
                    ? new Date(d.tanggal_lahir).toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'})
                    : '-';
                rows += `<tr>
                    <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #f1f5f9;color:#64748b;">${i+1}</td>
                    <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #f1f5f9;font-weight:600;">${d.nama_keluarga}</td>
                    <td style="padding:10px 14px;border-bottom:1px solid #f1f5f9;">
                        <span class="badge ${hubunganClass(d.hubungan)}">${d.hubungan}</span>
                    </td>
                    <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #f1f5f9;">
                        <span style="display:inline-flex;align-items:center;gap:5px;">
                            <i class="fa-solid ${d.jenis_kelamin==='Laki-laki'?'fa-mars':'fa-venus'}"
                               style="color:${d.jenis_kelamin==='Laki-laki'?'#3b82f6':'#ec4899'};"></i>
                            ${d.jenis_kelamin}
                        </span>
                    </td>
                    <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #f1f5f9;">${tl}</td>
                    <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #f1f5f9;">${d.umur} thn</td>
                    <td style="padding:10px 14px;border-bottom:1px solid #f1f5f9;">
                        <span class="badge badge-gray">${d.ukuran_kaos ?? '-'}</span>
                    </td>
                </tr>`;
            });
        } else {
            rows = `<tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8;">
                Belum ada data keluarga</td></tr>`;
        }

        const statusBadge = k.keterangan === 'Aktif'
            ? `<span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px;"></i> Aktif</span>`
            : `<span class="badge badge-danger">${k.keterangan}</span>`;

        document.getElementById('modalContent').innerHTML = `
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-item-label">NIK Login</div>
                    <div class="info-item-value" style="font-family:monospace;">${k.nik_login ?? '-'}</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Departemen</div>
                    <div class="info-item-value">${k.departemen ?? '-'}</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Status</div>
                    <div class="info-item-value">${statusBadge}</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Jumlah Keluarga</div>
                    <div class="info-item-value">${k.jumlah_keluarga} Orang</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Status Kehadiran</div>
                    <div class="info-item-value">
                        ${k.status_kehadiran
                            ? '<span class="badge badge-success">Hadir</span>'
                            : '<span class="badge badge-gray">Tidak</span>'}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Total Anggota</div>
                    <div class="info-item-value">${details.length} Data</div>
                </div>
            </div>
            <div class="family-section">
                <h4>
                    <i class="fa-solid fa-people-group" 
                    style="color:#0b4614; margin-right:8px; margin-bottom:10px;"></i>
                    Data Keluarga
                </h4>
                <div style="overflow-x:auto;border-radius:10px;border:1px solid #f1f5f9;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                ${['#','Nama','Hubungan','Jenis Kelamin','Tgl Lahir','Umur','Kaos']
                                    .map(h => `<th style="padding:10px 14px;font-size:11px;font-weight:600;
                                        color:#64748b;text-transform:uppercase;letter-spacing:.8px;
                                        text-align:left;white-space:nowrap;">${h}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            </div>`;
    })
    .catch(() => {
        document.getElementById('modalContent').innerHTML =
            `<div style="text-align:center;padding:40px;color:#ef4444;">
                <i class="fa-solid fa-circle-exclamation" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                Gagal memuat data
            </div>`;
    });
}
function closeModal() {
    document.getElementById('modalDetail').classList.remove('show');
}

// ─────────────────────────────────────────────
// CLOSE ON OVERLAY / ESC
// ─────────────────────────────────────────────
['modalCreate','modalEdit','modalDetail','modalDelete'].forEach(id => {
    document.getElementById(id).addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('show');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['modalCreate','modalEdit','modalDetail','modalDelete'].forEach(id => {
            document.getElementById(id).classList.remove('show');
        });
    }
});
</script>
@endpush