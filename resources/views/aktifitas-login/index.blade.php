@extends('layouts.app')
@section('title', 'Aktivitas Login Guest')
@section('page-title', 'Aktivitas Login')

@section('content')
{{-- ── FILTER BAR ── --}}
<div class="card" style="margin-bottom:5px;">
    <div class="card-body" style="padding:16px 20px;">
        <form method="GET" action="{{ route('aktifitas-login.index') }}">
            <div class="filters">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama atau departemen..."
                           value="{{ request('search') }}" style="width:280px;">
                </div>
                <select name="departemen" class="form-control" style="width:180px;">
                    <option value="">Semua Departemen</option>
                    @foreach($departemenList as $dept)
                        <option value="{{ $dept }}" {{ request('departemen') === $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->filled('search') || request()->filled('departemen'))
                    <a href="{{ route('aktifitas-login.index') }}" class="btn btn-outline">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── TABLE CARD ── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-right-to-bracket" style="color:#0b4614;margin-right:6px;"></i>
                Aktivitas Login Guest
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $items->total() }} karyawan pernah login
            </div>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Karyawan</th>
                    <th>NIK</th>
                    <th>Departemen</th>
                    <th>Login Terakhir</th>
                    <th>Waktu Relatif</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $k)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:10px;background:#e8f5e9;
                                        color:#2e7d32;display:flex;align-items:center;justify-content:center;
                                        font-size:12px;font-weight:800;flex-shrink:0;">
                                {{ strtoupper(substr($k->nama, 0, 2)) }}
                            </div>
                            <div style="font-weight:600;font-size:13px;">{{ $k->nama }}</div>
                        </div>
                    </td>
                    <td>
                        <span style="font-family:monospace;font-size:11px;background:#f5f5f5;
                                     padding:2px 7px;border-radius:5px;color:#888;">
                            {{ $k->nik }}
                        </span>
                    </td>
                    <td><span class="badge badge-primary">{{ $k->departemen }}</span></td>
                    <td style="font-size:12px;color:#475569;white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($k->last_login_at)->format('d M Y, H:i') }}
                    </td>
                    <td>
                        <span style="font-size:12px;font-weight:600;
                            color:{{ \Carbon\Carbon::parse($k->last_login_at)->diffInMinutes() < 60 ? '#16a34a' :
                                    (\Carbon\Carbon::parse($k->last_login_at)->diffInHours() < 24 ? '#d97706' : '#94a3b8') }};">
                            {{ \Carbon\Carbon::parse($k->last_login_at)->diffForHumans() }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fa-solid fa-user-clock" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                        Belum ada aktivitas login
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($items->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }}
            dari {{ $items->total() }} data
        </div>
        <div class="pagination">
            @if($items->onFirstPage())
                <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $items->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif
            @foreach($items->getUrlRange(
                max(1, $items->currentPage()-2),
                min($items->lastPage(), $items->currentPage()+2)
            ) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $items->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach
            @if($items->hasMorePages())
                <a href="{{ $items->nextPageUrl() }}" class="page-link">
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