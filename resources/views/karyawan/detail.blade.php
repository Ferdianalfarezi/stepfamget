@extends('layouts.app')
@section('title', 'Detail Karyawan')
@section('page-title', 'Detail Karyawan')

@section('content')

{{-- ── SUMMARY CARDS ── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:14px;">

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-people-group" style="color:#16a34a;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Total Anggota</div>
            <div style="font-size:20px;font-weight:800;color:#16a34a;">{{ $totalAnggota }}</div>
        </div>
    </div>

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-user-check" style="color:#0369a1;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Karyawan STEP</div>
            <div style="font-size:20px;font-weight:800;color:#0369a1;">{{ $totalAnggotaValid }}</div>
        </div>
    </div>

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#fef9c3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-user-slash" style="color:#ca8a04;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Eksternal</div>
            <div style="font-size:20px;font-weight:800;color:#ca8a04;">{{ $totalAnggota - $totalAnggotaValid }}</div>
            <div style="font-size:10px;color:#94a3b8;margin-top:1px;">RJU, SPARE, dll</div>
        </div>
    </div>

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-shirt" style="color:#16a34a;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Konfirmasi Baju</div>
            <div style="font-size:20px;font-weight:800;color:#16a34a;">
                {{ $totalBajuConfirmed }}<span style="font-size:13px;font-weight:500;color:#94a3b8;">/{{ $totalKaryawan }}</span>
            </div>
        </div>
    </div>

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#f0f9ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-bus" style="color:#0369a1;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Konfirmasi Transport</div>
            <div style="font-size:20px;font-weight:800;color:#0369a1;">
                {{ $totalTransConfirmed }}<span style="font-size:13px;font-weight:500;color:#94a3b8;">/{{ $totalKaryawan }}</span>
            </div>
        </div>
    </div>

</div>

{{-- ── BARCHART PER DEPARTEMEN ── --}}
@if($summaryDept->isNotEmpty())
@php
    $deptNormal   = $summaryDept->filter(fn($v, $k) => !in_array($k, $excludedDept))->sortKeys();
    $deptExcluded = $summaryDept->filter(fn($v, $k) =>  in_array($k, $excludedDept))->sortKeys();
@endphp

<div class="card" style="padding:16px 18px;margin-bottom:14px;">
    <p style="font-size:11px;font-weight:500;color:#94a3b8;letter-spacing:.6px;text-transform:uppercase;margin:0 0 12px;">
        Rekap per Departemen
    </p>
    <div style="position:relative;width:100%;height:260px;">
        <canvas id="deptChart" role="img" aria-label="Bar chart jumlah anggota per departemen">
            Data anggota per departemen.
        </canvas>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:12px;font-size:12px;color:#64748b;">
        <span style="display:flex;align-items:center;gap:5px;">
            <span style="width:10px;height:10px;border-radius:2px;background:#16a34a;"></span>Karyawan STEP
        </span>
        <span style="display:flex;align-items:center;gap:5px;">
            <span style="width:10px;height:10px;border-radius:2px;background:#c3c2b7;"></span>Eksternal
        </span>
    </div>
</div>
@endif

{{-- ── FILTER BAR ── --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('karyawan.detail.all') }}">
            <div class="filters">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama, NIK, departemen..."
                           value="{{ request('search') }}" style="width:280px;">
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
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
                @if(request()->hasAny(['search','departemen','hubungan']))
                    <a href="{{ route('karyawan.detail.all') }}" class="btn btn-outline">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── TABEL ── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-people-group" style="color:#0b4614;margin-right:2px;"></i>Detail Anggota Keluarga
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $total }} anggota ditemukan
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="{{ route('karyawan.index') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:45px;">#</th>
                    <th style="width:100px;">NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Nama Anggota</th>
                    <th>Hubungan</th>
                    <th style="width:90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($karyawans as $k)
                @php
                    $details   = $k->details;
                    $rowspan   = max($details->count(), 1);
                    $hasFamily = $details->count() > 0;
                @endphp

                <tr style="border-top:2px solid #f1f5f9;">
                    <td rowspan="{{ $rowspan }}" style="color:#94a3b8;font-size:12px;vertical-align:top;padding-top:14px;">
                        {{ $no++ }}
                    </td>
                    <td rowspan="{{ $rowspan }}" style="vertical-align:top;padding-top:12px;">
                        <span style="background:#0b4614;color:#fff;border-radius:6px;padding:2px 8px;font-size:12px;font-weight:700;">
                            {{ $k->nik }}
                        </span>
                    </td>
                    <td rowspan="{{ $rowspan }}" style="vertical-align:top;padding-top:12px;">
                        <div style="font-weight:600;font-size:13.5px;">{{ $k->nama }}</div>
                        <div style="font-size:11px;color:#64748b;">{{ $k->departemen }}</div>
                    </td>

                    @if($hasFamily)
                    @php $first = $details->first(); @endphp
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $first->nama_keluarga }}</div>
                        <div style="font-size:11px;color:#94a3b8;">{{ in_array($first->hubungan, ['Karyawan','Karyawati']) ? $first->hubungan : 'Anggota Keluarga' }}</div>
                    </td>
                    <td>
                        @php
                            $hubClass = match($first->hubungan) {
                                'Karyawan'  => 'hubungan-karyawan',
                                'Karyawati' => 'hubungan-karyawati',
                                'Istri'     => 'hubungan-istri',
                                'Suami'     => 'hubungan-suami',
                                'Anak'      => 'hubungan-anak',
                                default     => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $hubClass }}">{{ $first->hubungan }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="action-btn action-btn-warning" onclick="openEditDetail({{ $first->id }})" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="action-btn action-btn-danger" onclick="confirmDeleteDetail({{ $first->id }}, '{{ addslashes($first->nama_keluarga) }}')" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                    @else
                    <td style="color:#cbd5e1;font-size:12px;font-style:italic;">Belum ada anggota</td>
                    <td></td>
                    <td></td>
                    @endif
                </tr>

                @foreach($details->skip(1) as $d)
                <tr>
                    <td style="padding:8px 12px;">
                        <div style="font-weight:600;font-size:13px;">{{ $d->nama_keluarga }}</div>
                        <div style="font-size:11px;color:#94a3b8;">{{ in_array($d->hubungan, ['Karyawan','Karyawati']) ? $d->hubungan : 'Anggota Keluarga' }}</div>
                    </td>
                    <td style="padding:8px 12px;">
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
                    <td style="padding:8px 12px;">
                        <div style="display:flex;gap:6px;">
                            <button class="action-btn action-btn-warning" onclick="openEditDetail({{ $d->id }})" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="action-btn action-btn-danger" onclick="confirmDeleteDetail({{ $d->id }}, '{{ addslashes($d->nama_keluarga) }}')" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach

                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-users-slash" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($karyawans->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $karyawans->firstItem() }}–{{ $karyawans->lastItem() }} dari {{ $karyawans->total() }} karyawan
        </div>
        <div class="pagination">
            @if($karyawans->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $karyawans->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
            @endif
            @foreach($karyawans->getUrlRange(max(1,$karyawans->currentPage()-2), min($karyawans->lastPage(),$karyawans->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $karyawans->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($karyawans->hasMorePages())
                <a href="{{ $karyawans->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- MODAL: EDIT DETAIL --}}
<div class="modal-overlay" id="modalEditDetail">
    <div class="modal-box" style="max-width:480px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-pen-to-square" style="color:#f59e0b;margin-right:8px;"></i>Edit Anggota Keluarga
                </div>
                <div class="modal-subtitle" id="editDetailSub">Memuat data...</div>
            </div>
            <button class="modal-close" onclick="closeEditDetail()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="ed_id">
            <div class="k-form-group">
                <label class="k-form-label">Nama Anggota <span class="required">*</span></label>
                <input type="text" id="ed_nama_keluarga" class="k-form-input" placeholder="Nama lengkap">
                <div class="k-form-error" id="ed_err_nama"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="k-form-group">
                    <label class="k-form-label">Hubungan <span class="required">*</span></label>
                    <select id="ed_hubungan" class="k-form-input">
                        @foreach(['Karyawan','Karyawati','Istri','Suami','Anak','Saudara'] as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                    <div class="k-form-error" id="ed_err_hubungan"></div>
                </div>
                <div class="k-form-group">
                    <label class="k-form-label">Jenis Kelamin <span class="required">*</span></label>
                    <select id="ed_jenis_kelamin" class="k-form-input">
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="k-form-group">
                    <label class="k-form-label">Tanggal Lahir</label>
                    <input type="date" id="ed_tanggal_lahir" class="k-form-input">
                </div>
                <div class="k-form-group">
                    <label class="k-form-label">Umur</label>
                    <input type="number" id="ed_umur" class="k-form-input" min="0" placeholder="0">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div class="k-form-group">
                    <label class="k-form-label">Ukuran Kaos</label>
                    <select id="ed_ukuran_kaos" class="k-form-input">
                        <option value="">-</option>
                        @foreach(['XS','S','M','L','XL','XXL','XXXL'] as $u)
                            <option value="{{ $u }}">{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="k-form-group">
                    <label class="k-form-label">Jenis Kaos</label>
                    <select id="ed_jenis_kaos" class="k-form-input" onchange="toggleLengan()">
                        <option value="Dewasa">Dewasa</option>
                        <option value="Anak">Anak</option>
                    </select>
                </div>
                <div class="k-form-group" id="ed_lengan_wrap">
                    <label class="k-form-label">Lengan</label>
                    <select id="ed_lengan_kaos" class="k-form-input">
                        <option value="">-</option>
                        <option value="Lengan Pendek">Pendek</option>
                        <option value="Lengan Panjang">Panjang</option>
                    </select>
                </div>
            </div>
            <div class="k-form-actions" style="margin-top:20px;">
                <button type="button" class="btn btn-outline" onclick="closeEditDetail()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveDetail" onclick="saveEditDetail()">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: DELETE DETAIL --}}
<div class="modal-overlay" id="modalDeleteDetail">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;margin-right:8px;"></i>Hapus Anggota
                </div>
                <div class="modal-subtitle">Tindakan ini tidak bisa dibatalkan</div>
            </div>
            <button class="modal-close" onclick="closeDeleteDetail()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p style="color:#475569;font-size:14px;margin-bottom:20px;">
                Yakin ingin menghapus anggota
                <strong id="deleteDetailName" style="color:#1e293b;"></strong>?
            </p>
            <div class="k-form-actions">
                <button type="button" class="btn btn-outline" onclick="closeDeleteDetail()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" class="btn k-btn-danger" id="btnConfirmDeleteDetail" onclick="doDeleteDetail()">
                    <i class="fa-solid fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
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

function toggleLengan() {
    const isAnak = document.getElementById('ed_jenis_kaos').value === 'Anak';
    document.getElementById('ed_lengan_wrap').style.opacity       = isAnak ? '.4' : '1';
    document.getElementById('ed_lengan_wrap').style.pointerEvents = isAnak ? 'none' : 'auto';
    if (isAnak) document.getElementById('ed_lengan_kaos').value   = '';
}

function openEditDetail(id) {
    document.getElementById('editDetailSub').textContent = 'Memuat data...';
    document.getElementById('ed_err_nama').textContent   = '';
    document.getElementById('modalEditDetail').classList.add('show');

    fetch(`/detail-karyawan/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        document.getElementById('ed_id').value             = d.id;
        document.getElementById('ed_nama_keluarga').value  = d.nama_keluarga ?? '';
        document.getElementById('ed_hubungan').value       = d.hubungan ?? '';
        document.getElementById('ed_jenis_kelamin').value  = d.jenis_kelamin ?? 'Laki-laki';
        document.getElementById('ed_tanggal_lahir').value  = d.tanggal_lahir ?? '';
        document.getElementById('ed_umur').value           = d.umur ?? '';
        document.getElementById('ed_ukuran_kaos').value    = d.ukuran_kaos ?? '';
        document.getElementById('ed_jenis_kaos').value     = d.jenis_kaos ?? 'Dewasa';
        document.getElementById('ed_lengan_kaos').value    = d.lengan_kaos ?? '';
        document.getElementById('editDetailSub').textContent = d.nama_keluarga;
        toggleLengan();
    })
    .catch(() => { closeEditDetail(); showToast('Gagal memuat data', 'error'); });
}

function closeEditDetail() { document.getElementById('modalEditDetail').classList.remove('show'); }

function saveEditDetail() {
    const id  = document.getElementById('ed_id').value;
    const btn = document.getElementById('btnSaveDetail');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    btn.disabled  = true;

    fetch(`/detail-karyawan/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            _method       : 'PUT',
            nama_keluarga : document.getElementById('ed_nama_keluarga').value,
            hubungan      : document.getElementById('ed_hubungan').value,
            jenis_kelamin : document.getElementById('ed_jenis_kelamin').value,
            tanggal_lahir : document.getElementById('ed_tanggal_lahir').value || null,
            umur          : document.getElementById('ed_umur').value || 0,
            ukuran_kaos   : document.getElementById('ed_ukuran_kaos').value || null,
            jenis_kaos    : document.getElementById('ed_jenis_kaos').value,
            lengan_kaos   : document.getElementById('ed_lengan_kaos').value || null,
        }),
    })
    .then(r => r.json().then(d => ({ status: r.status, body: d })))
    .then(({ status, body }) => {
        if (status === 200) {
            closeEditDetail();
            showToast('Data anggota berhasil diupdate!');
            setTimeout(() => location.reload(), 800);
        } else if (status === 422) {
            const errs = body.errors ?? {};
            document.getElementById('ed_err_nama').textContent    = errs.nama_keluarga?.[0] ?? '';
            document.getElementById('ed_err_hubungan').textContent = errs.hubungan?.[0] ?? '';
        } else {
            showToast(body.message ?? 'Terjadi kesalahan', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => {
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
        btn.disabled  = false;
    });
}

let deleteDetailId = null;

function confirmDeleteDetail(id, nama) {
    deleteDetailId = id;
    document.getElementById('deleteDetailName').textContent = nama;
    document.getElementById('modalDeleteDetail').classList.add('show');
}

function closeDeleteDetail() {
    deleteDetailId = null;
    document.getElementById('modalDeleteDetail').classList.remove('show');
}

function doDeleteDetail() {
    if (!deleteDetailId) return;
    const btn = document.getElementById('btnConfirmDeleteDetail');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menghapus...';
    btn.disabled  = true;

    fetch(`/detail-karyawan/${deleteDetailId}`, {
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
            closeDeleteDetail();
            showToast('Anggota berhasil dihapus!');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(body.message ?? 'Gagal menghapus', 'error');
        }
    })
    .catch(() => showToast('Gagal menghubungi server', 'error'))
    .finally(() => {
        btn.innerHTML = '<i class="fa-solid fa-trash"></i> Hapus';
        btn.disabled  = false;
    });
}

['modalEditDetail','modalDeleteDetail'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['modalEditDetail','modalDeleteDetail'].forEach(id => {
            document.getElementById(id).classList.remove('show');
        });
    }
});

(function () {
    @if($summaryDept->isNotEmpty())
    @php
        $dn = $summaryDept->filter(fn($v, $k) => !in_array($k, $excludedDept))->sortKeys();
        $de = $summaryDept->filter(fn($v, $k) =>  in_array($k, $excludedDept))->sortKeys();
    @endphp
    const deptNormal   = @json($dn);
    const deptExcluded = @json($de);

    const labelsNormal   = Object.keys(deptNormal);
    const labelsExcluded = Object.keys(deptExcluded);
    const allLabels      = [...labelsNormal, ...labelsExcluded];
    const allData        = [...Object.values(deptNormal), ...Object.values(deptExcluded)];
    const isDark         = matchMedia('(prefers-color-scheme: dark)').matches;

    const colors = [
        ...labelsNormal.map(()   => '#16a34a'),
        ...labelsExcluded.map(() => isDark ? '#444441' : '#c3c2b7'),
    ];

    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const textMuted = '#898781';
    const textSec   = isDark ? '#c3c2b7' : '#52514e';

    new Chart(document.getElementById('deptChart'), {
        type: 'bar',
        data: {
            labels: allLabels,
            datasets: [{
                data: allData,
                backgroundColor: colors,
                borderRadius: { topLeft: 4, topRight: 4 },
                borderSkipped: 'bottom',
                barThickness: 28,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.raw} orang` } }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: textSec, font: { size: 11 }, maxRotation: 45, minRotation: 30, autoSkip: false },
                    border: { display: false },
                },
                y: {
                    grid: { color: gridColor },
                    ticks: { color: textMuted, font: { size: 11 } },
                    border: { color: gridColor },
                    beginAtZero: true,
                }
            },
            layout: { padding: { top: 20 } }
        },
        plugins: [{
            id: 'valueLabel',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                chart.data.datasets[0].data.forEach((val, i) => {
                    const meta = chart.getDatasetMeta(0);
                    const bar  = meta.data[i];
                    ctx.save();
                    ctx.fillStyle    = textSec;
                    ctx.font         = '500 11px sans-serif';
                    ctx.textAlign    = 'center';
                    ctx.textBaseline = 'bottom';
                    ctx.fillText(val, bar.x, bar.y - 4);
                    ctx.restore();
                });
            }
        }]
    });
    @endif
})();
</script>
@endpush