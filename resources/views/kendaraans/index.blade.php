@extends('layouts.app')
@section('title', 'Data Kendaraan Pribadi')
@section('page-title', 'Transportasi')

@section('content')

{{-- FILTER BAR --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('kendaraans.index') }}">
            <div class="filters">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama, NIK, atau plat no..."
                           value="{{ request('search') }}" style="width:280px;">
                </div>

                {{-- Filter jenis kendaraan --}}
                <select name="jenis" class="form-control"
                        style="width:140px;border-radius:10px;border:1.5px solid #e2e8f0;padding:8px 12px;font-size:13px;">
                    <option value="">Semua Jenis</option>
                    <option value="mobil" {{ request('jenis') === 'mobil' ? 'selected' : '' }}>🚗 Mobil</option>
                    <option value="motor" {{ request('jenis') === 'motor' ? 'selected' : '' }}>🏍️ Motor</option>
                    <option value="truk"  {{ request('jenis') === 'truk'  ? 'selected' : '' }}>🚛 Truk</option>
                </select>

                {{-- Filter jenis tiket --}}
                <select name="jenis_tiket" class="form-control"
                        style="width:140px;border-radius:10px;border:1.5px solid #e2e8f0;padding:8px 12px;font-size:13px;">
                    <option value="">Semua Tiket</option>
                    <option value="0" {{ request('jenis_tiket') === '0' ? 'selected' : '' }}>Regular</option>
                    <option value="1" {{ request('jenis_tiket') === '1' ? 'selected' : '' }}>VIP</option>
                    <option value="2" {{ request('jenis_tiket') === '2' ? 'selected' : '' }}>VVIP</option>
                </select>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'jenis', 'jenis_tiket']))
                    <a href="{{ route('kendaraans.index') }}" class="btn btn-outline">
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
                <i class="fa-solid fa-car" style="color:#0b4614;margin-right:6px;"></i>Data Kendaraan Pribadi
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total <strong>{{ $total }}</strong> karyawan bawa kendaraan pribadi
                @if(isset($totalPerJenis))
                    &nbsp;·&nbsp;
                    🚗 <strong>{{ $totalPerJenis['mobil'] ?? 0 }}</strong> mobil
                    &nbsp;·&nbsp;
                    🏍️ <strong>{{ $totalPerJenis['motor'] ?? 0 }}</strong> motor
                    &nbsp;·&nbsp;
                    🚛 <strong>{{ $totalPerJenis['truk'] ?? 0 }}</strong> truk
                @endif
                @if(isset($totalPerTiket))
                    &nbsp;·&nbsp;
                    Regular: <strong>{{ $totalPerTiket[0] ?? 0 }}</strong>
                    &nbsp;·&nbsp;
                    <span style="color:#854d0e;">VIP: <strong>{{ $totalPerTiket[1] ?? 0 }}</strong></span>
                    &nbsp;·&nbsp;
                    <span style="color:#991b1b;">VVIP: <strong>{{ $totalPerTiket[2] ?? 0 }}</strong></span>
                @endif
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="{{ route('buses.index') }}"
               class="btn btn-outline"
               style="display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-bus"></i> Naik Bus
            </a>
            <a href="{{ route('kendaraans.export', request()->query()) }}"
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
                    <th>Nama Karyawan</th>
                    <th>Jenis Kendaraan</th>
                    <th>Plat Nomor</th>
                    <th>Terdaftar Pada</th>
                    <th style="width:60px;text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="kendaraan-tbody">
                @forelse($kendaraans as $k)
                @php
                    $jenisConfig = [
                        'mobil' => ['emoji' => '🚗', 'label' => 'Mobil', 'bg' => '#e0f2fe', 'color' => '#0369a1'],
                        'motor' => ['emoji' => '🏍️', 'label' => 'Motor', 'bg' => '#fef9c3', 'color' => '#854d0e'],
                        'truk'  => ['emoji' => '🚛', 'label' => 'Truk',  'bg' => '#fce7f3', 'color' => '#9d174d'],
                    ];
                    $jenis = $jenisConfig[$k->jenis_kendaraan ?? 'mobil'] ?? $jenisConfig['mobil'];

                    // Row highlight berdasarkan jenis_tiket: 0=Regular, 1=VIP(kuning), 2=VVIP(merah)
                    $rowStyle = '';
                    if (($k->jenis_tiket ?? 0) == 1) {
                        $rowStyle = 'background:#fef9c3;';
                    } elseif (($k->jenis_tiket ?? 0) == 2) {
                        $rowStyle = 'background:#fee2e2;';
                    }
                @endphp
                <tr id="row-kendaraan-{{ $k->id }}" data-jenis-tiket="{{ $k->jenis_tiket ?? 0 }}" style="{{ $rowStyle }}">
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($kendaraans->currentPage() - 1) * $kendaraans->perPage() }}
                    </td>
                    <td>
                        <span style="font-size:13px;">{{ $k->nik }}</span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:10px;
                                        background:#fff8e1;color:#f57f17;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:12px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($k->nama_karyawan, 0, 2)) }}
                            </div>
                            <div style="font-weight:600;font-size:13.5px;">
                                {{ $k->nama_karyawan }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:5px;
                                     background:{{ $jenis['bg'] }};color:{{ $jenis['color'] }};
                                     padding:5px 12px;border-radius:20px;
                                     font-size:12px;font-weight:700;">
                            {{ $jenis['emoji'] }} {{ $jenis['label'] }}
                        </span>
                        <span class="tiket-badge" style="display:inline-block;margin-left:6px;font-size:10px;font-weight:700;color:{{ ($k->jenis_tiket ?? 0) == 2 ? '#991b1b' : (($k->jenis_tiket ?? 0) == 1 ? '#854d0e' : '#94a3b8') }};">
                            · {{ \App\Models\Kendaraan::tiketOptions()[$k->jenis_tiket ?? 0] }}
                        </span>
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:6px;
                                     background:#f1f5f9;padding:5px 12px;border-radius:8px;
                                     font-size:13px;font-weight:700;letter-spacing:1.5px;
                                     font-family:monospace;color:#334155;">
                            <i class="fa-solid fa-hashtag" style="font-size:10px;color:#94a3b8;"></i>
                            {{ $k->plat_no }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:#64748b;">
                        <i class="fa-regular fa-clock" style="margin-right:4px;"></i>
                        {{ $k->created_at->format('d M Y, H:i') }}
                    </td>
                    <td style="text-align:center;">
                        <div style="position:relative;display:inline-block;width:32px;height:32px;">
                            <button type="button" tabindex="-1"
                                    style="background:#f1f5f9;border:none;width:32px;height:32px;border-radius:8px;
                                           color:#475569;pointer-events:none;display:flex;align-items:center;justify-content:center;">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <select class="select-tiket" onchange="markTiket({{ $k->id }}, this.value)"
                                    title="Tandai jenis tiket"
                                    style="position:absolute;inset:0;width:100%;height:100%;
                                           opacity:0;cursor:pointer;z-index:1;">
                                <option value="0" {{ ($k->jenis_tiket ?? 0) == 0 ? 'selected' : '' }}>Regular</option>
                                <option value="1" {{ ($k->jenis_tiket ?? 0) == 1 ? 'selected' : '' }}>VIP</option>
                                <option value="2" {{ ($k->jenis_tiket ?? 0) == 2 ? 'selected' : '' }}>VVIP</option>
                            </select>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-car" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3;"></i>
                        Belum ada karyawan yang memilih kendaraan pribadi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($kendaraans->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $kendaraans->firstItem() }}–{{ $kendaraans->lastItem() }} dari {{ $kendaraans->total() }} data
        </div>
        <div class="pagination">
            @if($kendaraans->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $kendaraans->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            @foreach($kendaraans->getUrlRange(max(1, $kendaraans->currentPage()-2), min($kendaraans->lastPage(), $kendaraans->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $kendaraans->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($kendaraans->hasMorePages())
                <a href="{{ $kendaraans->nextPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/* ─── Toast (pola sama kayak konsumsi) ─── */
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

function markTiket(id, jenisTiket) {
        jenisTiket = parseInt(jenisTiket);
        const row = document.getElementById('row-kendaraan-' + id);
        const previousValue = row.dataset.jenisTiket ?? 0;
        fetch(`{{ url('/kendaraans') }}/${id}/tiket`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ jenis_tiket: jenisTiket }),
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Gagal update');

            const badge = row.querySelector('.tiket-badge');

            const colors = {
                0: { bg: '', badge: '#94a3b8' },
                1: { bg: '#fef9c3', badge: '#854d0e' },
                2: { bg: '#fee2e2', badge: '#991b1b' },
            };
            const c = colors[jenisTiket];

            row.style.background = c.bg;
            row.dataset.jenisTiket = jenisTiket;
            badge.textContent = '· ' + data.label;
            badge.style.color = c.badge;

            showToast(data.message ?? 'Jenis tiket berhasil diperbarui.');
        })
        .catch(err => {
            const select = row.querySelector('.select-tiket');
            if (select) select.value = previousValue;

            showToast(err.message ?? 'Gagal menandai.', 'error');
        });
    }
</script>
@endpush

@endsection