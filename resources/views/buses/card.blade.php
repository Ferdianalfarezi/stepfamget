@extends('layouts.app')
@section('title', 'Layout Kursi Bus')
@section('page-title', 'Transportasi')

@section('content')

<div class="card" style="margin-bottom:14px;">
    <div class="card-header">
        <div>
            <div class="card-title">
                <i class="fa-solid fa-bus" style="color:#0b4614;margin-right:6px;"></i>Layout Kursi Bus
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                Total {{ $cards->count() }} bus terdaftar
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('bus.ketua.index') }}" class="btn btn-outline"
               style="display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-users-gear"></i> Master Ketua Bus
            </a>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
    @forelse($cards as $c)
    <div class="card" style="overflow:hidden;">

        {{-- Header bus --}}
        <div style="background:linear-gradient(135deg,#0b4614,#16a34a);padding:16px 20px;display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,0.2);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-bus" style="color:#fff;font-size:18px;"></i>
            </div>
            <div>
                <div style="font-size:18px;font-weight:800;color:#fff;letter-spacing:-.3px;">
                    Bus {{ $c->kode }}
                </div>
                <div style="font-size:11px;color:rgba(255,255,255,0.7);margin-top:2px;">
                    <i class="fa-solid fa-building" style="margin-right:4px;"></i>{{ $c->dept }}
                </div>
            </div>
        </div>

        <div style="padding:16px 20px;">

            {{-- Info ketua --}}
            <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;
                        background:#f8fafc;border-radius:10px;margin-bottom:14px;border:1px solid #f1f5f9;">
                <div style="width:36px;height:36px;border-radius:10px;background:#e8f5e9;color:#2e7d32;
                            display:flex;align-items:center;justify-content:center;
                            font-size:12px;font-weight:700;flex-shrink:0;">
                    {{ strtoupper(substr($c->ketua->karyawan?->nama ?? 'X', 0, 2)) }}
                </div>
                <div>
                    <div style="font-weight:600;font-size:13px;color:#1e293b;">
                        {{ $c->ketua->karyawan?->nama ?? '-' }}
                    </div>
                    <div style="font-size:11px;color:#64748b;display:flex;align-items:center;gap:4px;">
                        <i class="fa-solid fa-phone" style="font-size:10px;"></i>
                        {{ $c->ketua->no_telp ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:14px;">
                <div style="text-align:center;padding:8px 4px;background:#f8fafc;border-radius:8px;">
                    <div style="font-size:18px;font-weight:800;color:#1e293b;">{{ $c->totalKursi }}</div>
                    <div style="font-size:10px;color:#94a3b8;font-weight:600;margin-top:2px;">Kursi</div>
                </div>
                <div style="text-align:center;padding:8px 4px;background:#f0fdf4;border-radius:8px;">
                    <div style="font-size:18px;font-weight:800;color:#16a34a;">{{ $c->terisi }}</div>
                    <div style="font-size:10px;color:#94a3b8;font-weight:600;margin-top:2px;">Terisi</div>
                </div>
                <div style="text-align:center;padding:8px 4px;background:#fefce8;border-radius:8px;">
                    <div style="font-size:18px;font-weight:800;color:#ca8a04;">{{ $c->kosong }}</div>
                    <div style="font-size:10px;color:#94a3b8;font-weight:600;margin-top:2px;">Kosong</div>
                </div>
                <div style="text-align:center;padding:8px 4px;background:#eff6ff;border-radius:8px;">
                    <div style="font-size:18px;font-weight:800;color:#3b82f6;">{{ $c->snack }}</div>
                    <div style="font-size:10px;color:#94a3b8;font-weight:600;margin-top:2px;">Snack</div>
                </div>
            </div>

            {{-- Progress --}}
            <div style="margin-bottom:14px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <span style="font-size:11px;color:#64748b;font-weight:600;">Kapasitas terisi</span>
                    <span style="font-size:12px;font-weight:700;color:{{ $c->persen >= 90 ? '#16a34a' : ($c->persen >= 60 ? '#ca8a04' : '#ef4444') }};">
                        {{ $c->persen }}%
                    </span>
                </div>
                <div style="height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                    <div style="height:6px;border-radius:3px;width:{{ $c->persen }}%;
                                background:{{ $c->persen >= 90 ? '#16a34a' : ($c->persen >= 60 ? '#f59e0b' : '#ef4444') }};
                                transition:width .5s ease;">
                    </div>
                </div>
            </div>

            {{-- Tombol lihat layout --}}
            <a href="{{ route('bus.layout', $c->kode) }}"
               style="display:flex;align-items:center;justify-content:center;gap:8px;
                      padding:10px;background:#0b4614;color:#fff;border-radius:10px;
                      font-size:13px;font-weight:600;text-decoration:none;
                      transition:background .2s;"
               onmouseover="this.style.background='#16a34a'"
               onmouseout="this.style.background='#0b4614'">
                <i class="fa-solid fa-eye"></i> Lihat Layout
            </a>

        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px;color:#94a3b8;">
        <i class="fa-solid fa-bus-slash" style="font-size:40px;display:block;margin-bottom:12px;"></i>
        Belum ada data bus. Tambah ketua bus terlebih dahulu.
    </div>
    @endforelse
</div>

@endsection