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

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'jenis']))
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
                </tr>
            </thead>
            <tbody>
                @forelse($kendaraans as $k)
                @php
                    $jenisConfig = [
                        'mobil' => ['emoji' => '🚗', 'label' => 'Mobil', 'bg' => '#e0f2fe', 'color' => '#0369a1'],
                        'motor' => ['emoji' => '🏍️', 'label' => 'Motor', 'bg' => '#fef9c3', 'color' => '#854d0e'],
                        'truk'  => ['emoji' => '🚛', 'label' => 'Truk',  'bg' => '#fce7f3', 'color' => '#9d174d'],
                    ];
                    $jenis = $jenisConfig[$k->jenis_kendaraan ?? 'mobil'] ?? $jenisConfig['mobil'];
                @endphp
                <tr>
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
                            <div style="font-weight:600;font-size:13.5px;">{{ $k->nama_karyawan }}</div>
                        </div>
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:5px;
                                     background:{{ $jenis['bg'] }};color:{{ $jenis['color'] }};
                                     padding:5px 12px;border-radius:20px;
                                     font-size:12px;font-weight:700;">
                            {{ $jenis['emoji'] }} {{ $jenis['label'] }}
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
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
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

@endsection