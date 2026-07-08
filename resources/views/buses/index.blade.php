@extends('layouts.app')
@section('title', 'Data Naik Bus')
@section('page-title', 'Transportasi')

@section('content')

{{-- ── SUMMARY CARDS ────────────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-bottom:14px;">

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-user-tie" style="color:#0369a1;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Karyawan</div>
            <div style="font-size:20px;font-weight:800;color:#0369a1;">{{ $totalKaryawan }}</div>
        </div>
    </div>

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#fef9c3;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-people-roof" style="color:#ca8a04;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Keluarga</div>
            <div style="font-size:20px;font-weight:800;color:#ca8a04;">{{ $totalKeluarga }}</div>
        </div>
    </div>

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-users" style="color:#16a34a;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Total Naik Bus</div>
            <div style="font-size:20px;font-weight:800;color:#16a34a;">{{ $total }}</div>
        </div>
    </div>

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-bus" style="color:#0b4614;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Estimasi Bus</div>
            <div style="font-size:20px;font-weight:800;color:#0b4614;">
                {{ $jumlahBus }}
                <span style="font-size:11px;font-weight:500;color:#94a3b8;">(54 kursi/bus)</span>
            </div>
        </div>
    </div>

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#fdf2f8;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-bus-simple" style="color:#be185d;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Estimasi Bus (Jika Full)</div>
            <div style="font-size:20px;font-weight:800;color:#be185d;">
                {{ $estimasiBusFullDesimal }}
                <span style="font-size:11px;font-weight:500;color:#94a3b8;">({{ $estimasiNaikBusFull }} org)</span>
            </div>
        </div>
    </div>

</div>

{{-- FILTER BAR --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('buses.index') }}">
            <div class="filters">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama atau NIK..."
                           value="{{ request('search') }}" style="width:280px;">
                </div>

                <select name="kursi_status" class="form-control" style="width:180px;">
                    <option value="">Semua Status Kursi</option>
                    <option value="filled" {{ request('kursi_status') === 'filled' ? 'selected' : '' }}>
                        Sudah Terisi
                    </option>
                    <option value="empty" {{ request('kursi_status') === 'empty' ? 'selected' : '' }}>
                        Belum Terisi
                    </option>
                </select>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'kursi_status']))
                    <a href="{{ route('buses.index') }}" class="btn btn-outline">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- TABLE CARD --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-bus" style="color:#0b4614;margin-right:6px;"></i>Data Naik Bus
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="{{ route('bus.card') }}" class="btn btn-outline"
               style="display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-bus"></i> Layout Bus
            </a>
            <a href="{{ route('bus.ketua.index') }}"
                class="btn btn-outline"
                style="display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-users-gear"></i> Ketua Bus
            </a>
            <a href="{{ route('kendaraans.index') }}"
               class="btn btn-outline"
               style="display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-car"></i> Kendaraan Pribadi
            </a>
            <button onclick="openModalImport()"
                    class="btn"
                    style="background:#0369a1;color:#fff;border:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-file-arrow-up"></i> Import Kursi
            </button>
            <a href="{{ route('buses.export', request()->query()) }}"
               class="btn"
               style="background:#16a34a;color:#fff;border:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:45px;">#</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Hubungan</th>
                    <th>Kursi</th>
                    <th>Terdaftar Pada</th>
                </tr>
            </thead>
            <tbody>
                @php $no = ($buses->currentPage() - 1) * $buses->perPage(); @endphp
                @forelse($buses as $b)
                    @php $no++; @endphp
                    <tr>
                        <td style="color:#94a3b8;font-size:12px;">{{ $no }}</td>
                        <td>
                            <span style="font-size:13px;font-weight:700;">{{ $b->nik }}</span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;border-radius:10px;
                                            background:#e8f5e9;color:#2e7d32;
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:12px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($b->nama_karyawan, 0, 2)) }}
                                </div>
                                <div style="font-weight:600;font-size:13.5px;">{{ $b->nama_karyawan }}</div>
                            </div>
                        </td>
                        <td>
                            <span style="background:#dbeafe;color:#1e40af;border-radius:6px;
                                         padding:3px 10px;font-size:11px;font-weight:700;">
                                Karyawan
                            </span>
                        </td>
                        <td>
                            @if($b->kursi)
                                <span style="background:#1e293b;color:#fff;border-radius:8px;
                                             padding:4px 12px;font-size:13px;font-weight:800;
                                             letter-spacing:.5px;">
                                    {{ $b->kursi }}
                                </span>
                            @else
                                <span style="color:#cbd5e1;font-size:12px;font-style:italic;">Belum diisi</span>
                            @endif
                        </td>
                        <td style="font-size:13px;color:#64748b;">
                            <i class="fa-regular fa-clock" style="margin-right:4px;"></i>
                            {{ $b->created_at->format('d M Y, H:i') }}
                        </td>
                    </tr>

                    @foreach($b->keluarga as $k)
                        @php $no++; @endphp
                        <tr style="background:#fafafa;">
                            <td style="color:#cbd5e1;font-size:12px;">{{ $no }}</td>
                            <td>
                                <span style="font-size:12px;color:#cbd5e1;">{{ $b->nik }}</span>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;padding-left:20px;">
                                    <i class="fa-solid fa-share fa-rotate-90" style="color:#cbd5e1;font-size:11px;"></i>
                                    <div style="font-size:13px;color:#475569;">{{ $k->nama_keluarga }}</div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $hubClass = match($k->hubungan) {
                                        'Istri'  => 'hubungan-istri',
                                        'Suami'  => 'hubungan-suami',
                                        'Anak'   => 'hubungan-anak',
                                        default  => 'badge-gray',
                                    };
                                @endphp
                                <span class="badge {{ $hubClass }}">{{ $k->hubungan }}</span>
                            </td>
                            <td>
                                @if($k->kursi_bus)
                                    <span style="background:#1e293b;color:#fff;border-radius:8px;
                                                 padding:4px 12px;font-size:13px;font-weight:800;
                                                 letter-spacing:.5px;">
                                        {{ $k->kursi_bus }}
                                    </span>
                                @else
                                    <span style="color:#cbd5e1;font-size:12px;font-style:italic;">Belum diisi</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:#cbd5e1;">-</td>
                        </tr>
                    @endforeach

                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-bus-slash" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Belum ada karyawan yang memilih naik bus
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($buses->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $buses->firstItem() }}–{{ $buses->lastItem() }} dari {{ $buses->total() }} data
        </div>
        <div class="pagination">
            @if($buses->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $buses->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif
            @foreach($buses->getUrlRange(max(1,$buses->currentPage()-2), min($buses->lastPage(),$buses->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $buses->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($buses->hasMorePages())
                <a href="{{ $buses->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- MODAL IMPORT KURSI --}}
<div class="modal-overlay" id="modalImport">
    <div class="modal-box" style="max-width:460px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">
                    <i class="fa-solid fa-file-arrow-up" style="color:#0369a1;margin-right:8px;"></i>Import Kursi Bus
                </div>
                <div class="modal-subtitle">Upload file excel data kursi bus</div>
            </div>
            <button class="modal-close" onclick="closeModalImport()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">

            <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:12px 14px;margin-bottom:14px;font-size:12px;color:#0369a1;">
                <div style="font-weight:600;margin-bottom:6px;">
                    <i class="fa-solid fa-circle-info" style="margin-right:4px;"></i>Format kolom excel:
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:4px;">
                    <span>• <strong>nik</strong></span>
                    <span>• <strong>nama</strong></span>
                    <span>• <strong>kursi</strong></span>
                </div>
                <div style="margin-top:6px;color:#64748b;">
                    Pencocokan berdasarkan NIK + nama. Jika nama tidak cocok, fallback ke NIK yang belum ada kursinya.
                </div>
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
                <button type="button" id="btnDoImport" onclick="doImport()"
                        class="btn" style="background:#0369a1;color:#fff;">
                    <i class="fa-solid fa-file-arrow-up"></i> Import
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

function openModalImport() {
    document.getElementById('importFileInput').value  = '';
    document.getElementById('importFileError').textContent = '';
    document.getElementById('importResult').style.display = 'none';
    document.getElementById('importResult').innerHTML = '';
    document.getElementById('modalImport').classList.add('show');
}
function closeModalImport() {
    document.getElementById('modalImport').classList.remove('show');
}

async function doImport() {
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
        const res  = await fetch('{{ route("buses.importKursi") }}', {
            method : 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body   : formData,
        });
        const data = await res.json();

        resultEl.style.display = 'block';
        if (res.ok) {
            resultEl.innerHTML = `
                <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 14px;font-size:13px;color:#166534;">
                    <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>
                    <strong>${data.updated}</strong> kursi berhasil diperbarui,
                    <strong>${data.skipped}</strong> baris dilewati.
                </div>`;
            showToast(`${data.updated} kursi berhasil diimport!`);
            setTimeout(() => { closeModalImport(); location.reload(); }, 1800);
        } else {
            resultEl.innerHTML = `
                <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:12px 14px;font-size:13px;color:#991b1b;">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right:6px;"></i>${data.message ?? 'Terjadi kesalahan.'}
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

document.getElementById('modalImport').addEventListener('click', function(e) {
    if (e.target === this) closeModalImport();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModalImport();
});
</script>
@endpush