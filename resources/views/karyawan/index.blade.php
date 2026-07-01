@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('page-title', 'Karyawan')

@section('content')

{{-- ── SUMMARY CARDS ── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:14px;">

    {{-- Total Semua --}}
    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-users" style="color:#16a34a;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Total Karyawan</div>
            <div style="font-size:20px;font-weight:800;color:#16a34a;">{{ $totalKaryawan }}</div>
        </div>
    </div>

    {{-- Karyawan STEP --}}
    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-user-check" style="color:#0369a1;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Karyawan STEP</div>
            <div style="font-size:20px;font-weight:800;color:#0369a1;">{{ $totalValid }}</div>
        </div>
    </div>

    {{-- Eksternal --}}
    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#fef9c3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-user-slash" style="color:#ca8a04;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Eksternal</div>
            <div style="font-size:20px;font-weight:800;color:#ca8a04;">{{ $totalEksternal }}</div>
            <div style="font-size:10px;color:#94a3b8;margin-top:1px;">RJU, SPARE, dll</div>
        </div>
    </div>

    {{-- Karyawan vs Karyawati --}}
    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#ede9fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fa-solid fa-venus-mars" style="color:#7c3aed;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;margin-bottom:5px;">Karyawan / Karyawati</div>
            <div style="display:flex;gap:10px;align-items:center;">
                <span style="font-size:12px;font-weight:700;color:#3b82f6;display:flex;align-items:center;gap:3px;">
                    <i class="fa-solid fa-mars"></i> {{ $totalKaryawanLaki }}
                </span>
                <span style="color:#e2e8f0;">|</span>
                <span style="font-size:12px;font-weight:700;color:#ec4899;display:flex;align-items:center;gap:3px;">
                    <i class="fa-solid fa-venus"></i> {{ $totalKaryawanWanita }}
                </span>
            </div>
        </div>
    </div>

    {{-- Konfirmasi Baju --}}
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

    {{-- Konfirmasi Transport --}}
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

    {{-- Kehadiran --}}
    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;margin-bottom:5px;">Kehadiran</div>
            <div style="display:flex;gap:10px;align-items:center;">
                <span style="font-size:12px;font-weight:700;color:#16a34a;display:flex;align-items:center;gap:3px;"><i class="fa-solid fa-circle-check"></i> {{ $totalHadir }}</span>
                <span style="color:#e2e8f0;">|</span>
                <span style="font-size:12px;font-weight:700;color:#ef4444;display:flex;align-items:center;gap:3px;"><i class="fa-solid fa-circle-xmark"></i> {{ $totalTidakHadir }}</span>
                <span style="color:#e2e8f0;">|</span>
                <span style="font-size:12px;font-weight:700;color:#94a3b8;display:flex;align-items:center;gap:3px;"><i class="fa-solid fa-clock"></i> {{ $totalBelumHadir }}</span>
            </div>
        </div>
    </div>

</div>

</div>

<div class="card" style="padding:16px 18px;margin-bottom:14px;">
    <p style="font-size:11px;font-weight:500;color:#94a3b8;letter-spacing:.6px;text-transform:uppercase;margin:0 0 12px;">
        Rekap per Departemen
    </p>
    <div style="position:relative;width:100%;height:260px;">
        <canvas id="deptChart" role="img" aria-label="Bar chart jumlah karyawan per departemen">
            Data karyawan per departemen.
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

<!-- FILTER BAR -->
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:16px 20px;overflow-x:auto;">
        <form method="GET" action="{{ route('karyawan.index') }}">
            <div class="filters" style="flex-wrap:nowrap;min-width:max-content;">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama / NIK..."
                           value="{{ request('search') }}" style="width:180px;">
                </div>
                <select name="departemen" class="form-control" style="width:auto;min-width:120px;">
                    <option value="">Semua Dept.</option>
                    @foreach($departemenList as $dept)
                        <option value="{{ $dept }}" {{ request('departemen') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
                <select name="keterangan" class="form-control" style="width:auto;min-width:100px;">
                    <option value="">Semua Status</option>
                    <option value="Aktif"     {{ request('keterangan') == 'Aktif'     ? 'selected' : '' }}>Aktif</option>
                    <option value="Non-Aktif" {{ request('keterangan') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
                <select name="baju" class="form-control" style="width:auto;min-width:110px;">
                    <option value="">Semua Baju</option>
                    <option value="confirmed" {{ request('baju') == 'confirmed' ? 'selected' : '' }}>✓ Konfirmasi</option>
                    <option value="belum"     {{ request('baju') == 'belum'     ? 'selected' : '' }}>○ Belum</option>
                </select>
                <select name="trans" class="form-control" style="width:auto;min-width:110px;">
                    <option value="">Semua Trans</option>
                    <option value="confirmed" {{ request('trans') == 'confirmed' ? 'selected' : '' }}>✓ Konfirmasi</option>
                    <option value="belum"     {{ request('trans') == 'belum'     ? 'selected' : '' }}>○ Belum</option>
                </select>
                <select name="hadir" class="form-control" style="width:auto;min-width:110px;">
                    <option value="">Semua Hadir</option>
                    <option value="2" {{ request('hadir') == '2'  ? 'selected' : '' }}>✓ Hadir</option>
                    <option value="1" {{ request('hadir') == '1'  ? 'selected' : '' }}>✗ Tidak</option>
                    <option value="0" {{ request('hadir') === '0' ? 'selected' : '' }}>○ Belum</option>
                </select>
                <select name="hubungan" class="form-control" style="width:auto;min-width:120px;">
                    <option value="">Semua Hubungan</option>
                    <option value="Karyawan"   {{ request('hubungan') == 'Karyawan'   ? 'selected' : '' }}>
                        ♂ Karyawan
                    </option>
                    <option value="Karyawati"  {{ request('hubungan') == 'Karyawati'  ? 'selected' : '' }}>
                        ♀ Karyawati
                    </option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
                @if(request()->hasAny(['search','departemen','keterangan','baju','trans','hadir','hubungan']))
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
            <a href="{{ route('karyawan.detail.all') }}"
               class="btn"
               style="background:#0369a1;color:#fff;border:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-table-list"></i> Detail View
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
                    <th style="width:110px;">Baju</th>
                    <th style="width:110px;">Transport</th>
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
                    <td>
                        @if(in_array($k->departemen, $excludedDept))
                            <span class="badge badge-gray">{{ $k->departemen }}</span>
                        @else
                            <span class="badge badge-success">{{ $k->departemen }}</span>
                        @endif
                    </td>
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
                        @if($k->status_kehadiran == 2)
                            <span class="badge badge-success">Hadir</span>
                        @elseif($k->status_kehadiran == 1)
                            <span class="badge badge-danger">Tidak Hadir</span>
                        @else
                            <span class="badge badge-gray">
                                <i class="fa-solid fa-clock" style="font-size:9px;"></i>
                                Belum
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($k->isBajuConfirmedThisYear())
                            <span class="badge badge-success">
                                <i class="fa-solid fa-circle-check" style="font-size:9px;"></i>
                                Konfirmasi
                            </span>
                        @else
                            <span class="badge badge-gray">
                                <i class="fa-solid fa-clock" style="font-size:9px;"></i>
                                Belum
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($k->isTransConfirmedThisYear())
                            <span class="badge badge-success">
                                <i class="fa-solid fa-circle-check" style="font-size:9px;"></i>
                                Konfirmasi
                            </span>
                        @else
                            <span class="badge badge-gray">
                                <i class="fa-solid fa-clock" style="font-size:9px;"></i>
                                Belum
                            </span>
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

{{-- ============================= --}}
{{-- MODAL: IMPORT BAJU            --}}
{{-- ============================= --}}
<div class="modal-overlay" id="modalImport">
    <div class="modal-box" style="max-width:460px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-file-arrow-up" style="color:#0369a1;margin-right:8px;"></i>Import Data Baju
                </div>
                <div class="modal-subtitle">Upload file excel data baju family gathering</div>
            </div>
            <button class="modal-close" onclick="closeModalImport()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">

            <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:12px 14px;margin-bottom:14px;font-size:12px;color:#0369a1;">
                <div style="font-weight:600;margin-bottom:6px;">
                    <i class="fa-solid fa-circle-info" style="margin-right:4px;"></i>Kolom yang dibaca dari excel:
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:2px 12px;">
                    <span>• NIK</span>
                    <span>• Nama Karyawan</span>
                    <span>• Departemen</span>
                    <span>• Nama Anggota</span>
                    <span>• Status/Hubungan</span>
                    <span>• Ukuran Baju</span>
                    <span>• Keterangan</span>
                </div>
            </div>

            <div style="background:#fefce8;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#92400e;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-right:4px;"></i>
                Data yang sudah ada akan di-<strong>update</strong> ukuran &amp; tipe bajunya. Data baru akan ditambahkan.
            </div>

            <div class="k-form-group">
                <label class="k-form-label">File Excel <span class="required">*</span></label>
                <input type="file" id="importFileInput" class="k-form-input"
                       accept=".xlsx,.xls" style="padding:6px;">
                <div class="k-form-error" id="importFileError"></div>
            </div>

            <div id="importResult" style="display:none;margin-top:12px;"></div>

            <div class="k-form-actions" style="margin-top:20px;">
                <button type="button" class="btn btn-outline" onclick="closeModalImport()">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button type="button" id="btnDoImport" onclick="doImportBaju()"
                        class="btn" style="background:#0369a1;color:#fff;">
                    <i class="fa-solid fa-file-arrow-up"></i> Import
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
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
        const k       = data.karyawan ?? data;
        const details = data.details ?? [];

        document.getElementById('edit_id').value               = k.id;
        document.getElementById('edit_nik').value              = k.nik ?? '';
        document.getElementById('edit_nik_login').value        = k.nik_login ?? '';
        document.getElementById('edit_nama').value             = k.nama ?? '';
        document.getElementById('edit_departemen').value       = k.departemen ?? '';
        document.getElementById('edit_keterangan').value       = k.keterangan ?? 'Aktif';
        document.getElementById('edit_status_kehadiran').value = k.status_kehadiran;
        document.getElementById('editModalSub').textContent    = `NIK: ${k.nik}`;

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

                const jenisBadge = d.jenis_kaos === 'Anak'
                    ? `<span class="badge badge-danger"><i class="fa-solid fa-child" style="margin-right:3px;font-size:10px;"></i>Anak</span>`
                    : `<span class="badge badge-success"><i class="fa-solid fa-person" style="margin-right:3px;font-size:10px;"></i>Dewasa</span>`;

                const lenganBadge = d.lengan_kaos === 'Lengan Pendek'
                    ? `<span class="badge badge-gray"><i class="fa-solid fa-shirt" style="margin-right:3px;font-size:10px;"></i>Pendek</span>`
                    : d.lengan_kaos === 'Lengan Panjang'
                        ? `<span class="badge" style="background:#e0f2fe;color:#0369a1;"><i class="fa-solid fa-shirt" style="margin-right:3px;font-size:10px;"></i>Panjang</span>`
                        : `<span style="color:#94a3b8;font-size:12px;">-</span>`;

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
                    <td style="padding:10px 14px;border-bottom:1px solid #f1f5f9;">${jenisBadge}</td>
                    <td style="padding:10px 14px;border-bottom:1px solid #f1f5f9;">${lenganBadge}</td>
                </tr>`;
            });
        } else {
            rows = `<tr><td colspan="9" style="text-align:center;padding:30px;color:#94a3b8;">
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
                        ${k.status_kehadiran == 2
                        ? '<span class="badge badge-success">Hadir</span>'
                        : k.status_kehadiran == 1
                            ? '<span class="badge badge-danger">Tidak Hadir</span>'
                            : '<span class="badge badge-gray">Belum Konfirmasi</span>'}
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
                    style="color:#0b4614;margin-right:8px;margin-bottom:10px;"></i>
                    Data Keluarga
                </h4>
                <div style="overflow-x:auto;border-radius:10px;border:1px solid #f1f5f9;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                ${['#','Nama','Hubungan','Jenis Kelamin','Tgl Lahir','Umur','Ukuran','Jenis Kaos','Lengan']
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
// MODAL IMPORT BAJU
// ─────────────────────────────────────────────
function openModalImport() {
    document.getElementById('importFileInput').value = '';
    document.getElementById('importFileError').textContent = '';
    document.getElementById('importResult').style.display = 'none';
    document.getElementById('importResult').innerHTML = '';
    document.getElementById('modalImport').classList.add('show');
}
function closeModalImport() {
    document.getElementById('modalImport').classList.remove('show');
}

async function doImportBaju() {
    const fileInput = document.getElementById('importFileInput');
    const errEl     = document.getElementById('importFileError');
    const resultEl  = document.getElementById('importResult');
    const btn       = document.getElementById('btnDoImport');

    errEl.textContent = '';
    resultEl.style.display = 'none';

    if (!fileInput.files.length) {
        errEl.textContent = 'Pilih file excel terlebih dahulu.';
        return;
    }

    const formData = new FormData();
    formData.append('file_excel', fileInput.files[0]);

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengimport...';

    try {
        const res  = await fetch('{{ route('karyawan.importBaju') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        });

        const data = await res.json();

        resultEl.style.display = 'block';
        if (res.ok) {
            resultEl.innerHTML = `
                <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 14px;font-size:13px;color:#166534;">
                    <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>
                    <strong>${data.imported}</strong> data berhasil diproses,
                    <strong>${data.skipped}</strong> baris dilewati.
                </div>`;
            setTimeout(() => { closeModalImport(); location.reload(); }, 1800);
        } else {
            resultEl.innerHTML = `
                <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:12px 14px;font-size:13px;color:#991b1b;">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right:6px;"></i>${data.message}
                </div>`;
        }
    } catch {
        resultEl.style.display = 'block';
        resultEl.innerHTML = `
            <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:12px 14px;font-size:13px;color:#991b1b;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right:6px;"></i>Gagal menghubungi server.
            </div>`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-file-arrow-up"></i> Import';
    }
}

// ─────────────────────────────────────────────
// CLOSE ON OVERLAY / ESC
// ─────────────────────────────────────────────
['modalCreate','modalEdit','modalDetail','modalDelete','modalImport'].forEach(id => {
    document.getElementById(id).addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('show');
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['modalCreate','modalEdit','modalDetail','modalDelete','modalImport'].forEach(id => {
            document.getElementById(id).classList.remove('show');
        });
    }
});

(function () {
    const deptNormal   = @json($deptNormal);
    const deptExcluded = @json($deptExcluded);

    const labelsNormal   = Object.keys(deptNormal);
    const labelsExcluded = Object.keys(deptExcluded);

    const allLabels = [...labelsNormal, ...labelsExcluded];
    const allData   = [...Object.values(deptNormal), ...Object.values(deptExcluded)];
    const isDark    = matchMedia('(prefers-color-scheme: dark)').matches;

    const colors = [
        ...labelsNormal.map(() => '#16a34a'),
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
                    ticks: {
                        color: textSec,
                        font: { size: 11 },
                        maxRotation: 45,
                        minRotation: 30,
                        autoSkip: false,
                    },
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
})();
</script>
@endpush