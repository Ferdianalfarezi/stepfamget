@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ─── STATS ROW ─── --}}
<div class="stats-grid" style="margin-bottom:16px;">

    <div class="stat-card">
        <div class="stat-sublabel">Total Karyawan</div>
        <div class="stat-value">{{ number_format($totalKaryawan) }}</div>
        <div class="stat-trend up">
            <i class="fa-solid fa-arrow-trend-up" style="font-size:9px;"></i>
            Data keseluruhan
        </div>
        <div class="mini-bars">
            <div class="mini-bar" style="height:8px;"></div>
            <div class="mini-bar g" style="height:14px;"></div>
            <div class="mini-bar" style="height:10px;"></div>
            <div class="mini-bar g" style="height:18px;"></div>
            <div class="mini-bar g" style="height:22px;"></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-sublabel">Karyawan Aktif</div>
        <div class="stat-value">
            {{ number_format($totalAktif) }}
            <sup>/{{ number_format($totalKaryawan) }}</sup>
        </div>
        <div class="stat-trend up">
            <i class="fa-solid fa-circle-check" style="font-size:9px;"></i>
            Status aktif
        </div>
        <div class="mini-bars">
            <div class="mini-bar g" style="height:16px;"></div>
            <div class="mini-bar g" style="height:12px;"></div>
            <div class="mini-bar g" style="height:20px;"></div>
            <div class="mini-bar g" style="height:14px;"></div>
            <div class="mini-bar g" style="height:22px;"></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-sublabel">Total Peserta</div>
        <div class="stat-value">{{ number_format($totalPeserta) }}</div>
        <div class="stat-trend up">
            <i class="fa-solid fa-users" style="font-size:9px;"></i>
            Karyawan hadir + keluarga
        </div>
        <div class="mini-bars">
            <div class="mini-bar g" style="height:10px;"></div>
            <div class="mini-bar g" style="height:16px;"></div>
            <div class="mini-bar g" style="height:20px;"></div>
            <div class="mini-bar g" style="height:14px;"></div>
            <div class="mini-bar g" style="height:24px;"></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-sublabel">Total Anggota Keluarga</div>
        <div class="stat-value">{{ number_format($totalKeluarga) }}</div>
        <div class="stat-trend up">
            <i class="fa-solid fa-people-group" style="font-size:9px;"></i>
            Semua departemen
        </div>
        <div class="mini-bars">
            <div class="mini-bar g" style="height:10px;"></div>
            <div class="mini-bar g" style="height:16px;"></div>
            <div class="mini-bar g" style="height:12px;"></div>
            <div class="mini-bar g" style="height:20px;"></div>
            <div class="mini-bar g" style="height:24px;"></div>
        </div>
    </div>

</div>

{{-- ─── MID ROW ─── --}}
<div style="display:grid;grid-template-columns:1fr 1fr 1.3fr;gap:12px;margin-bottom:16px;">

    {{-- Donut: Kehadiran --}}
    <div class="card" style="padding:16px;">
        <div class="stat-sublabel" style="margin-bottom:8px;">Tingkat Kehadiran</div>
        @php
            $hadirPct  = $totalKaryawan > 0 ? round(($totalHadir / $totalKaryawan) * 100) : 0;
            $hadirDash = round(($hadirPct / 100) * 201);
            $sisaDash  = 201 - $hadirDash;
        @endphp
        <div style="font-size:36px;font-weight:800;color:#111;line-height:1;">
            {{ $hadirPct }}<sup style="font-size:16px;font-weight:600;">%</sup>
        </div>
        <div style="display:flex;align-items:center;justify-content:center;margin:8px 0 4px;">
            <svg viewBox="0 0 80 80" width="88" height="88">
                <circle cx="40" cy="40" r="32" fill="none" stroke="#e8ede8" stroke-width="10"/>
                <circle cx="40" cy="40" r="32" fill="none" stroke="#3d7a47" stroke-width="10"
                    stroke-dasharray="{{ $hadirDash }} {{ $sisaDash }}"
                    stroke-dashoffset="25" stroke-linecap="round"/>
                <circle cx="40" cy="40" r="32" fill="none" stroke="#1a3320" stroke-width="10"
                    stroke-dasharray="{{ $sisaDash }} {{ $hadirDash }}"
                    stroke-dashoffset="{{ -1 * ($hadirDash - 25) }}" stroke-linecap="round"/>
            </svg>
        </div>
        <div style="font-size:10.5px;color:#aaa;text-align:center;">
            {{ $totalHadir }} dari {{ $totalKaryawan }} hadir
        </div>
    </div>

    {{-- Dept breakdown --}}
    <div class="card" style="padding:16px;">
        <div class="stat-sublabel" style="margin-bottom:10px;">Per Departemen</div>
        @foreach($perDepartemen->take(4) as $dept)
        <div style="display:flex;align-items:center;margin-bottom:8px;font-size:12px;">
            <span style="width:8px;height:8px;border-radius:50%;background:#3d7a47;flex-shrink:0;margin-right:8px;"></span>
            <span style="flex:1;color:#555;">{{ $dept->departemen }}</span>
            <span style="font-weight:600;color:#111;">{{ $dept->total }}</span>
        </div>
        @endforeach
        <a href="{{ route('karyawan.index') }}" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;margin-top:10px;">
            Lihat Detail
        </a>
    </div>

    {{-- Top 5 Departemen Paling Aktif --}}
    <div class="card" style="padding:14px 16px;">
        <div class="stat-sublabel" style="margin-bottom:4px;">Departemen Teraktif</div>
        <div style="font-size:10px;color:#ccc;margin-bottom:10px;">5 departemen login terbanyak</div>
        @foreach($topDepartemen as $dept)
        <div style="display:flex;align-items:center;margin-bottom:7px;font-size:11.5px;">
            <span style="width:8px;height:8px;border-radius:50%;background:#3d7a47;flex-shrink:0;margin-right:7px;"></span>
            <span style="flex:1;color:#444;">{{ $dept->departemen }}</span>
            <span style="font-weight:600;color:#111;">{{ $dept->total_login }} login</span>
        </div>
        @endforeach
    </div>

</div>

{{-- ─── BOTTOM ROW ─── --}}
@php
    $votingTempats = \App\Models\VotingTempat::withCount('votes')->orderByDesc('votes_count')->get();
    $totalVotesAll = \App\Models\VotingVote::count();
@endphp
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">

    {{-- Voting Tempat --}}
    <div style="background:#1a3320;border-radius:12px;padding:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <div style="font-size:13px;font-weight:700;color:#fff;">
                <i class="fa-solid fa-map-location-dot" style="margin-right:7px;color:#7ec88a;"></i>
                Voting Tempat
            </div>
            <a href="{{ route('voting.index') }}" style="font-size:10px;color:rgba(255,255,255,.4);text-decoration:none;">Kelola →</a>
        </div>
        <div style="font-size:10px;color:rgba(255,255,255,.35);margin-bottom:10px;">
            {{ $totalVotesAll }} suara masuk
        </div>
        @forelse($votingTempats->take(3) as $t)
        @php $pct = $totalVotesAll > 0 ? round($t->votes_count / $totalVotesAll * 100) : 0; @endphp
        <div style="margin-bottom:8px;">
            <div style="display:flex;justify-content:space-between;margin-bottom:3px;">
                <span style="font-size:11.5px;color:#fff;font-weight:{{ $loop->first ? '700' : '400' }};">
                    @if($loop->first)🏆 @endif{{ $t->nama }}
                </span>
                <span style="font-size:11px;color:rgba(255,255,255,.5);">{{ $pct }}%</span>
            </div>
            <div style="background:rgba(255,255,255,.1);border-radius:4px;height:5px;overflow:hidden;">
                <div style="height:100%;border-radius:4px;
                     background:{{ $loop->first ? '#7ec88a' : 'rgba(255,255,255,.3)' }};
                     width:{{ $pct }}%;"></div>
            </div>
        </div>
        @empty
        <div style="font-size:11px;color:rgba(255,255,255,.3);text-align:center;padding:12px 0;">
            Belum ada kandidat tempat
        </div>
        @endforelse
    </div>

    {{-- Last Login Guest ─── --}}
    <div class="card" style="padding:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <div style="font-size:13px;font-weight:700;color:#111;">
                <i class="fa-solid fa-right-to-bracket" style="margin-right:7px;color:#3d7a47;"></i>
                Login Karyawan Terakhir
            </div>
            <a href="{{ route('aktifitas-login.index') }}"
               style="font-size:10px;color:#94a3b8;text-decoration:none;display:flex;align-items:center;gap:4px;">
                Lihat Semua →
            </a>
        </div>

        @forelse($lastLoginGuests as $k)
        <div style="display:flex;align-items:center;gap:10px;padding:7px 0;
                    border-bottom:1px solid #f1f5f9;">
            {{-- Avatar --}}
            <div style="width:32px;height:32px;border-radius:9px;background:#e8f5e9;
                        color:#2e7d32;display:flex;align-items:center;justify-content:center;
                        font-size:12px;font-weight:800;flex-shrink:0;">
                {{ strtoupper(substr($k->nama, 0, 2)) }}
            </div>
            {{-- Info --}}
            <div style="flex:1;min-width:0;">
                <div style="font-size:12.5px;font-weight:700;color:#111;
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $k->nama }}
                </div>
                <div style="font-size:10.5px;color:#94a3b8;">{{ $k->departemen }}</div>
            </div>
            {{-- Waktu + detail --}}
            <div style="text-align:right;flex-shrink:0;display:flex;align-items:center;gap:8px;">
                <div>
                    <div style="font-size:10.5px;color:#64748b;font-weight:600;">
                        {{ \Carbon\Carbon::parse($k->last_login_at)->diffForHumans() }}
                    </div>
                    <div style="font-size:10px;color:#cbd5e1;">
                        {{ \Carbon\Carbon::parse($k->last_login_at)->format('d M, H:i') }}
                    </div>
                </div>
                <a href="{{ route('aktifitas-login.index') }}"
                   style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;
                          display:flex;align-items:center;justify-content:center;
                          color:#64748b;text-decoration:none;flex-shrink:0;
                          transition:background .15s;"
                   title="Lihat detail">
                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:10px;"></i>
                </a>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:24px 0;color:#aaa;">
            <i class="fa-solid fa-user-clock" style="font-size:24px;display:block;margin-bottom:8px;"></i>
            <div style="font-size:11px;">Belum ada Karyawan yang login</div>
        </div>
        @endforelse
    </div>

</div>

{{-- ─── RECENT KARYAWAN ─── --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-clock-rotate-left"></i>
            Karyawan Terbaru
        </div>
        <a href="{{ route('karyawan.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Departemen</th>
                    <th>Keluarga</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentKaryawan as $i => $k)
                <tr>
                    <td style="color:#ccc;font-size:11px;">{{ $i + 1 }}</td>
                    <td>
                        <span style="font-family:monospace;font-size:11px;background:#f5f5f5;padding:2px 7px;border-radius:5px;color:#888;">
                            {{ $k->nik }}
                        </span>
                    </td>
                    <td style="font-weight:600;font-size:13px;">{{ $k->nama }}</td>
                    <td><span class="badge badge-primary">{{ $k->departemen }}</span></td>
                    <td style="color:#888;font-size:12px;">
                        <i class="fa-solid fa-people-group" style="color:#3d7a47;font-size:11px;margin-right:4px;"></i>
                        {{ $k->jumlah_keluarga }} orang
                    </td>
                    <td>
                        @if($k->keterangan == 'Aktif')
                            <span class="badge badge-success">
                                <i class="fa-solid fa-circle" style="font-size:6px;"></i> Aktif
                            </span>
                        @else
                            <span class="badge badge-danger">
                                <i class="fa-solid fa-circle" style="font-size:6px;"></i> {{ $k->keterangan }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:30px;color:#aaa;">
                        <i class="fa-solid fa-users-slash" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                        Belum ada data karyawan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection