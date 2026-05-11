@extends('layouts.app')
@section('title', 'Data Naik Bus')
@section('page-title', 'Transportasi')

@section('content')

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
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search']))
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
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total <strong>{{ $total }}</strong> karyawan naik bus
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            {{-- Link ke kendaraan pribadi --}}
            <a href="{{ route('kendaraans.index') }}"
               class="btn btn-outline"
               style="display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-car"></i> Kendaraan Pribadi
            </a>
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
                    <th>Nama Karyawan</th>
                    <th>Terdaftar Pada</th>
                </tr>
            </thead>
            <tbody>
                @forelse($buses as $b)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($buses->currentPage() - 1) * $buses->perPage() }}
                    </td>
                    <td>
                        <span style="font-size:13px;">{{ $b->nik }}</span>
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
                    <td style="font-size:13px;color:#64748b;">
                        <i class="fa-regular fa-clock" style="margin-right:4px;"></i>
                        {{ $b->created_at->format('d M Y, H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-bus-slash" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Belum ada karyawan yang memilih naik bus
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
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

            @foreach($buses->getUrlRange(max(1, $buses->currentPage()-2), min($buses->lastPage(), $buses->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $buses->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($buses->hasMorePages())
                <a href="{{ $buses->nextPageUrl() }}" class="page-link">
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