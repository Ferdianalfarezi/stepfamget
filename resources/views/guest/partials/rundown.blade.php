@extends('guest.layouts.app')
@section('title', 'Rundown Acara')

@section('content')

{{-- Header card --}}
<div style="background:var(--green-dark);border-radius:20px;padding:20px;margin-bottom:14px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:rgba(61,122,71,.25);pointer-events:none;"></div>
    <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.4);letter-spacing:1px;margin-bottom:8px;">TIMELINE ACARA</div>
    <div style="font-size:22px;font-weight:800;color:#fff;margin-bottom:4px;">Rundown Acara</div>
    <div style="font-size:12px;color:rgba(255,255,255,.4);">
        {{ $rundowns->count() }} kegiatan
        @if($rundowns->count())
            &nbsp;·&nbsp;
            {{ \Carbon\Carbon::createFromFormat('H:i:s', $rundowns->first()->mulai)->format('H:i') }}
            –
            {{ \Carbon\Carbon::createFromFormat('H:i:s', $rundowns->last()->selesai)->format('H:i') }}
        @endif
    </div>
</div>

{{-- Timeline list --}}
@forelse($rundowns as $i => $r)
    @php
        $mulai   = \Carbon\Carbon::createFromFormat('H:i:s', $r->mulai);
        $selesai = \Carbon\Carbon::createFromFormat('H:i:s', $r->selesai);
        $now     = \Carbon\Carbon::now();
        $isNow   = $now->between($mulai, $selesai);
        $isPast  = $now->gt($selesai);
    @endphp

    <div style="display:flex;gap:12px;margin-bottom:4px;">

        {{-- Dot & garis --}}
        <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0;width:32px;">
            <div style="width:12px;height:12px;border-radius:50%;flex-shrink:0;margin-top:16px;
                        background:{{ $isNow ? '#16a34a' : ($isPast ? '#94a3b8' : '#e2e8f0') }};
                        border:2px solid {{ $isNow ? '#bbf7d0' : ($isPast ? '#e2e8f0' : '#cbd5e1') }};
                        box-shadow:{{ $isNow ? '0 0 0 4px rgba(22,163,74,.15)' : 'none' }};
                        position:relative;z-index:1;">
            </div>
            @if(!$loop->last)
            <div style="width:2px;flex:1;min-height:20px;margin-top:2px;
                        background:{{ $isPast ? '#e2e8f0' : '#f1f5f9' }};"></div>
            @endif
        </div>

        {{-- Card --}}
        <div style="flex:1;background:#fff;border-radius:14px;padding:14px 16px;margin-bottom:8px;
                    border:1px solid {{ $isNow ? '#bbf7d0' : '#e8edf0' }};
                    box-shadow:{{ $isNow ? '0 2px 12px rgba(22,163,74,.12)' : '0 1px 4px rgba(0,0,0,.04)' }};
                    opacity:{{ $isPast ? '.55' : '1' }};">

            {{-- Waktu --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;flex-wrap:wrap;gap:6px;">
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-size:13px;font-weight:700;color:#15803d;font-family:monospace;">
                        {{ $mulai->format('H:i') }}
                    </span>
                    <span style="font-size:11px;color:#94a3b8;">–</span>
                    <span style="font-size:13px;font-weight:700;color:#c2410c;font-family:monospace;">
                        {{ $selesai->format('H:i') }}
                    </span>
                    <span style="font-size:11px;color:#94a3b8;background:#f1f5f9;border-radius:4px;padding:2px 6px;font-family:monospace;">
                        {{ $r->durasi }}
                    </span>
                </div>

                @if($isNow)
                <span style="font-size:10px;font-weight:700;color:#16a34a;background:#f0fdf4;
                             border:1px solid #bbf7d0;border-radius:20px;padding:3px 10px;
                             letter-spacing:.5px;">● BERLANGSUNG</span>
                @elseif($isPast)
                <span style="font-size:10px;font-weight:600;color:#94a3b8;background:#f8fafc;
                             border:1px solid #e2e8f0;border-radius:20px;padding:3px 10px;">Selesai</span>
                @endif
            </div>

            {{-- Nama kegiatan --}}
            <div style="font-size:15px;font-weight:700;color:#1e293b;margin-bottom:6px;line-height:1.3;">
                {{ $r->kegiatan }}
            </div>

            {{-- PIC & Properti --}}
            @if($r->pic || $r->properti)
            <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:4px;">
                @if($r->pic)
                <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:#64748b;">
                    <i class="fa-solid fa-user" style="color:#94a3b8;font-size:10px;"></i>
                    {{ $r->pic }}
                </div>
                @endif
                @if($r->properti)
                <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:#64748b;">
                    <i class="fa-solid fa-box" style="color:#94a3b8;font-size:10px;"></i>
                    {{ $r->properti }}
                </div>
                @endif
            </div>
            @endif

            {{-- Keterangan --}}
            @if($r->keterangan)
            <div style="font-size:12px;color:#64748b;line-height:1.5;white-space:pre-line;margin-top:4px;">
                {{ $r->keterangan }}
            </div>
            @endif

        </div>
    </div>

@empty
<div style="text-align:center;padding:60px 20px;color:#94a3b8;">
    <i class="fa-solid fa-calendar-xmark" style="font-size:40px;display:block;margin-bottom:14px;opacity:.3;"></i>
    <div style="font-weight:700;font-size:15px;margin-bottom:4px;color:#64748b;">Belum ada rundown</div>
    <div style="font-size:12px;">Jadwal acara belum tersedia</div>
</div>
@endforelse

@endsection