@extends('layouts.app')
@section('title', 'Data Konveksi')
@section('page-title', 'Konveksi')

@section('content')

{{-- ── REKAP CARD ───────────────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:14px;">

    {{-- WAS: Sudah Isi --}}
    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-circle-check" style="color:#16a34a;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Sudah Konfirmasi</div>
            <div style="font-size:20px;font-weight:800;color:#16a34a;">{{ $totalKonfirmasi }}</div>
        </div>
    </div>

    {{-- WAS: Belum Isi --}}
    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-circle-exclamation" style="color:#ef4444;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Belum Konfirmasi</div>
            <div style="font-size:20px;font-weight:800;color:#ef4444;">{{ $totalBelumKonfirmasi }}</div>
        </div>
    </div>

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-users" style="color:#0369a1;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Total</div>
            <div style="font-size:20px;font-weight:800;color:#0369a1;">{{ $totalSudah + $totalBelum }}</div>
        </div>
    </div>

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-qrcode" style="color:#16a34a;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Sudah Scan</div>
            <div style="font-size:20px;font-weight:800;color:#16a34a;" id="counterScanned">{{ $totalScanned }}</div>
        </div>
    </div>

    <div class="card" style="padding:14px 18px;display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#fef9c3;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-clock" style="color:#ca8a04;font-size:16px;"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Belum Scan</div>
            <div style="font-size:20px;font-weight:800;color:#ca8a04;" id="counterUnscanned">{{ $totalUnscanned }}</div>
        </div>
    </div>

</div>

{{-- ── REKAP PER UKURAN ─────────────────────────────────────────────────────── --}}
@if($rekap->isNotEmpty())
@php
    $rekapAnak    = $rekap->where('jenis_kaos', 'Anak')->values();
    $rekapPanjang = $rekap->where('jenis_kaos', 'Dewasa')->where('lengan_kaos', 'Lengan Panjang')->values();
    $rekapPendek  = $rekap->where('jenis_kaos', 'Dewasa')->where('lengan_kaos', 'Lengan Pendek')->values();

    $maxAnak    = $rekapAnak->max('total') ?: 1;
    $maxPanjang = $rekapPanjang->max('total') ?: 1;
    $maxPendek  = $rekapPendek->max('total') ?: 1;
@endphp

<p style="font-size:11px;font-weight:600;color:#94a3b8;letter-spacing:.6px;text-transform:uppercase;margin:0 0 8px;">
    Rekap Ukuran Konveksi
</p>

<div style="display:grid;grid-template-columns:1fr 1fr 1fr;border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;margin-bottom:14px;">

    {{-- Kolom Anak --}}
    <div style="padding:20px;background:#fff;border-right:1px solid #e2e8f0;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;">
            <div style="width:30px;height:30px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-child" style="font-size:13px;color:#3b82f6;"></i>
            </div>
            <div>
                <div style="font-size:11px;font-weight:600;color:#94a3b8;letter-spacing:.5px;text-transform:uppercase;">Anak</div>
                <div style="font-size:11px;color:#cbd5e1;margin-top:1px;">{{ $rekapAnak->count() }} ukuran</div>
            </div>
        </div>
        @foreach($rekapAnak as $r)
        <div style="display:flex;align-items:center;gap:10px;padding:6px 0;{{ !$loop->last ? 'border-bottom:1px solid #f8fafc;' : '' }}">
            <span style="font-size:13px;color:#374151;min-width:44px;">{{ $r->ukuran_kaos }}</span>
            <div style="flex:1;height:4px;border-radius:2px;background:#f1f5f9;overflow:hidden;">
                <div style="height:4px;border-radius:2px;background:#3b82f6;opacity:.3;width:{{ round($r->total / $maxAnak * 100) }}%;"></div>
            </div>
            <span style="font-size:13px;font-weight:500;color:#3b82f6;min-width:28px;text-align:right;">{{ $r->total }}</span>
        </div>
        @endforeach
    </div>

    {{-- Kolom Lengan Panjang --}}
    <div style="padding:20px;background:#fff;border-right:1px solid #e2e8f0;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;">
            <div style="width:30px;height:30px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-arrow-right" style="font-size:13px;color:#16a34a;"></i>
            </div>
            <div>
                <div style="font-size:11px;font-weight:600;color:#94a3b8;letter-spacing:.5px;text-transform:uppercase;">Lengan Panjang</div>
                <div style="font-size:11px;color:#cbd5e1;margin-top:1px;">{{ $rekapPanjang->count() }} ukuran</div>
            </div>
        </div>
        @forelse($rekapPanjang as $r)
        <div style="display:flex;align-items:center;gap:10px;padding:6px 0;{{ !$loop->last ? 'border-bottom:1px solid #f8fafc;' : '' }}">
            <span style="font-size:13px;color:#374151;min-width:44px;">{{ $r->ukuran_kaos }}</span>
            <div style="flex:1;height:4px;border-radius:2px;background:#f1f5f9;overflow:hidden;">
                <div style="height:4px;border-radius:2px;background:#16a34a;opacity:.3;width:{{ round($r->total / $maxPanjang * 100) }}%;"></div>
            </div>
            <span style="font-size:13px;font-weight:500;color:#16a34a;min-width:28px;text-align:right;">{{ $r->total }}</span>
        </div>
        @empty
        <span style="font-size:12px;color:#cbd5e1;font-style:italic;">Tidak ada data</span>
        @endforelse
    </div>

    {{-- Kolom Lengan Pendek --}}
    <div style="padding:20px;background:#fff;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;">
            <div style="width:30px;height:30px;border-radius:8px;background:#ecfeff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-shirt" style="font-size:13px;color:#0891b2;"></i>
            </div>
            <div>
                <div style="font-size:11px;font-weight:600;color:#94a3b8;letter-spacing:.5px;text-transform:uppercase;">Lengan Pendek</div>
                <div style="font-size:11px;color:#cbd5e1;margin-top:1px;">{{ $rekapPendek->count() }} ukuran</div>
            </div>
        </div>
        @forelse($rekapPendek as $r)
        <div style="display:flex;align-items:center;gap:10px;padding:6px 0;{{ !$loop->last ? 'border-bottom:1px solid #f8fafc;' : '' }}">
            <span style="font-size:13px;color:#374151;min-width:44px;">{{ $r->ukuran_kaos }}</span>
            <div style="flex:1;height:4px;border-radius:2px;background:#f1f5f9;overflow:hidden;">
                <div style="height:4px;border-radius:2px;background:#0891b2;opacity:.3;width:{{ round($r->total / $maxPendek * 100) }}%;"></div>
            </div>
            <span style="font-size:13px;font-weight:500;color:#0891b2;min-width:28px;text-align:right;">{{ $r->total }}</span>
        </div>
        @empty
        <span style="font-size:12px;color:#cbd5e1;font-style:italic;">Tidak ada data</span>
        @endforelse
    </div>

</div>
@endif

{{-- ── FILTER BAR ───────────────────────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:10px;">
    <div class="card-body" style="padding:14px 18px;">
        <form method="GET" action="{{ route('konveksi.index') }}">
            <div class="filters" style="flex-wrap:wrap;gap:8px;">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama, NIK..."
                           value="{{ request('search') }}" style="width:220px;">
                </div>

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

                <select name="jenis" class="form-control" style="width:auto;min-width:120px;">
                    <option value="">Semua Jenis</option>
                    <option value="Dewasa" {{ request('jenis') == 'Dewasa' ? 'selected' : '' }}>Dewasa</option>
                    <option value="Anak"   {{ request('jenis') == 'Anak'   ? 'selected' : '' }}>Anak</option>
                </select>

                <select name="lengan" class="form-control" style="width:auto;min-width:140px;">
                    <option value="">Semua Lengan</option>
                    <option value="Lengan Pendek"  {{ request('lengan') == 'Lengan Pendek'  ? 'selected' : '' }}>Lengan Pendek</option>
                    <option value="Lengan Panjang" {{ request('lengan') == 'Lengan Panjang' ? 'selected' : '' }}>Lengan Panjang</option>
                </select>

                <select name="status_baju" class="form-control" style="width:auto;min-width:130px;">
                    <option value="">Semua Status</option>
                    <option value="sudah" {{ request('status_baju') == 'sudah' ? 'selected' : '' }}>Sudah Isi</option>
                    <option value="belum" {{ request('status_baju') == 'belum' ? 'selected' : '' }}>Belum Isi</option>
                </select>

                <select name="status_konfirmasi" class="form-control" style="width:105px;">
                    <option value="">Status baju</option>
                    <option value="sudah" {{ request('status_konfirmasi') == 'sudah' ? 'selected' : '' }}>Sudah Konfirmasi</option>
                    <option value="belum" {{ request('status_konfirmasi') == 'belum' ? 'selected' : '' }}>Belum Konfirmasi</option>
                </select>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(request()->hasAny(['search','hubungan','ukuran','jenis','lengan','status_baju','status_konfirmasi']))
                    <a href="{{ route('konveksi.index') }}" class="btn btn-outline">
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
                <i class="fa-solid fa-shirt" style="color:#0b4614;margin-right:4px;"></i>Data Konveksi
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $details->total() }} anggota
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">

            {{-- SCAN NIK --}}
            <div style="display:flex;align-items:center;border:1.5px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#f8fafc;">
                <div style="padding:0 10px;color:#94a3b8;font-size:13px;border-right:1px solid #e2e8f0;height:36px;display:flex;align-items:center;">
                    <i class="fa-solid fa-barcode"></i>
                </div>
                <input type="text" id="scanInput"
                       placeholder="Scan NIK..."
                       autocomplete="off"
                       style="border:none;background:transparent;padding:0 12px;height:36px;font-size:13px;width:160px;outline:none;color:#374151;">
                <button onclick="clearScan()"
                        id="btnClearScan"
                        style="display:none;border:none;background:transparent;padding:0 10px;height:36px;cursor:pointer;color:#94a3b8;border-left:1px solid #e2e8f0;">
                    <i class="fa-solid fa-xmark" style="font-size:12px;"></i>
                </button>
            </div>

            <div id="scanStatus" style="display:none;font-size:12px;padding:6px 12px;border-radius:8px;font-weight:600;"></div>

            <button onclick="doResetScan()" class="btn"
                    style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-rotate-left"></i> Reset Scan
            </button>
            <a href="{{ route('konveksi.export', request()->query()) }}"
               class="btn"
               style="background:#16a34a;color:#fff;border:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-file-excel"></i> Export
            </a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:45px;">#</th>
                    <th>NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Nama Anggota</th>
                    <th>Hubungan</th>
                    <th>Ukuran Baju</th>
                    <th>Jenis Kaos</th>
                    <th>Lengan</th>
                    <th style="width:60px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $prevNik = null; @endphp
                @forelse($details as $d)
                @php $k = $d->karyawan; $isNewNik = $d->nik !== $prevNik; $prevNik = $d->nik; @endphp
                <tr data-nik="{{ $d->nik }}" class="scan-row{{ $d->is_scanned ? ' row-scanned' : '' }}">
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($details->currentPage() - 1) * $details->perPage() }}
                    </td>
                    <td>
                        @if($isNewNik)
                            <span style="background:#0b4614;color:#fff;border-radius:6px;padding:2px 8px;font-size:12px;font-weight:700;">
                                {{ $d->nik }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px;">{{ $d->nik }}</span>
                        @endif
                    </td>
                    <td>
                        @if($isNewNik)
                            <div style="font-weight:600;font-size:13px;">{{ $k->nama ?? '-' }}</div>
                            <div style="font-size:11px;color:#64748b;">{{ $k->departemen ?? '' }}</div>
                        @else
                            <div style="font-size:12px;color:#cbd5e1;">{{ $k->nama ?? '-' }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#0369a1;">{{ $d->nama_keluarga }}</div>
                        <div style="font-size:11px;color:#94a3b8;">Anggota Keluarga</div>
                    </td>
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
                            <span style="background:#1e293b;color:#fff;border-radius:8px;padding:4px 12px;
                                         font-size:13px;font-weight:800;">
                                {{ $d->ukuran_kaos }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px;font-style:italic;">Belum diisi</span>
                        @endif
                    </td>
                    <td>
                        @if($d->jenis_kaos)
                            @if($d->jenis_kaos === 'Anak')
                                <span class="badge badge-danger">
                                    <i class="fa-solid fa-child" style="font-size:10px;margin-right:3px;"></i>Anak
                                </span>
                            @else
                                <span class="badge badge-success">
                                    <i class="fa-solid fa-person" style="font-size:10px;margin-right:3px;"></i>Dewasa
                                </span>
                            @endif
                        @else
                            <span style="color:#cbd5e1;font-size:12px;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($d->lengan_kaos === 'Lengan Pendek')
                            <span class="badge badge-gray">Pendek</span>
                        @elseif($d->lengan_kaos === 'Lengan Panjang')
                            <span class="badge" style="background:#e0f2fe;color:#0369a1;">Panjang</span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px;">-</span>
                        @endif
                    </td>
                    <td>
                        @if(in_array($d->hubungan, ['Karyawan','Karyawati']))
                            <a href="{{ route('konveksi.print', ['nik' => $d->nik]) }}"
                               target="_blank"
                               class="action-btn"
                               style="color:#0369a1;"
                               title="Cetak slip {{ $k->nama ?? '' }}">
                                <i class="fa-solid fa-print"></i>
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-shirt" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($details->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $details->firstItem() }}–{{ $details->lastItem() }} dari {{ $details->total() }} data
        </div>
        <div class="pagination">
            @if($details->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $details->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            @foreach($details->getUrlRange(max(1,$details->currentPage()-2), min($details->lastPage(),$details->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $details->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($details->hasMorePages())
                <a href="{{ $details->nextPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
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

@push('scripts')
<script>
const CSRF          = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const SCAN_URL      = '{{ route("konveksi.scan") }}';
const RESET_URL     = '{{ route("konveksi.resetScan") }}';
const scanInput     = document.getElementById('scanInput');
const btnClear      = document.getElementById('btnClearScan');
const scanStatus    = document.getElementById('scanStatus');
let   scanTimer     = null;

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
    document.getElementById('confirmTitle').innerHTML      = title;
    document.getElementById('confirmSubtitle').textContent = subtitle ?? '';
    document.getElementById('confirmMsg').innerHTML        = msg;
    document.getElementById('confirmOkLabel').textContent  = okLabel;

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

// ── Scan ──────────────────────────────────────────────────────────────────────
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
            showToast(data.message || 'NIK tidak ditemukan.', 'error');
            return;
        }

        const rows = document.querySelectorAll(`.scan-row[data-nik="${nik}"]`);
        rows.forEach(tr => tr.classList.add('row-scanned'));
        if (rows.length) rows[0].scrollIntoView({ behavior:'smooth', block:'center' });

        const cScanned   = document.getElementById('counterScanned');
        const cUnscanned = document.getElementById('counterUnscanned');
        if (cScanned)   cScanned.textContent   = parseInt(cScanned.textContent) + data.count;
        if (cUnscanned) cUnscanned.textContent = Math.max(0, parseInt(cUnscanned.textContent) - data.count);

        setStatus(data.nama + ' — ' + data.count + ' anggota discan', 'success');
        showToast(data.nama + ' berhasil discan!');

        setTimeout(() => {
            scanInput.value        = '';
            btnClear.style.display = 'none';
            scanInput.focus();
        }, 1200);

    } catch (e) {
        setStatus('Gagal menghubungi server.', 'error');
        showToast('Gagal menghubungi server.', 'error');
    }
}

// ── Reset scan ────────────────────────────────────────────────────────────────
function doResetScan() {
    showConfirm({
        title    : '<i class="fa-solid fa-rotate-left" style="color:#dc2626;margin-right:8px;"></i>Reset Semua Scan',
        subtitle : 'Tindakan ini tidak bisa dibatalkan',
        msg      : 'Yakin ingin mereset <strong>semua</strong> data scan konveksi? Semua tanda hijau akan dihapus.',
        okLabel  : 'Ya, Reset Semua',
        okClass  : 'k-btn-danger',
        onConfirm: async () => {
            try {
                await fetch(RESET_URL, {
                    method : 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                });

                document.querySelectorAll('.row-scanned').forEach(tr => tr.classList.remove('row-scanned'));

                const cScanned   = document.getElementById('counterScanned');
                const cUnscanned = document.getElementById('counterUnscanned');
                if (cScanned)   cScanned.textContent   = '0';
                if (cUnscanned) cUnscanned.textContent = parseInt('{{ $totalSudah + $totalBelum }}');

                setStatus('Semua scan direset.', 'error');
                showToast('Semua scan berhasil direset.', 'error');
            } catch (e) {
                showToast('Gagal reset.', 'error');
            }
        }
    });
}

// ── Helpers ───────────────────────────────────────────────────────────────────
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
        success : ['#f0fdf4','#16a34a','1px solid #86efac'],
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
document.getElementById('modalConfirm').addEventListener('click', function(e) {
    if (e.target === this) closeConfirm();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeConfirm();
});

scanInput.focus();
</script>

<style>
.row-scanned { background:#f0fdf4 !important; }
.row-scanned td { border-bottom-color:#dcfce7 !important; }
</style>
@endpush
@endsection