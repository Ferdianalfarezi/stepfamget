@extends('guest.layouts.app')
@section('title', 'Kursi Bus Saya')

@section('content')
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --green-dark: #1a3320; --green-main: #3d7a47;
    --bg: #f0f4f0; --white: #fff;
    --border: #e0e8e0; --text: #1a1a1a; --text-light: #999;
    --safe-top: env(safe-area-inset-top, 0px);
    --safe-bottom: env(safe-area-inset-bottom, 0px);
  }
  html { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); }
  body { min-height: 100dvh; padding-bottom: calc(72px + var(--safe-bottom)); }

  .content { padding: 16px; max-width: 560px; margin: 0 auto; }

  .bottom-nav {
    position: fixed; bottom: 0; left: 0; right: 0; z-index: 100;
    background: rgba(255,255,255,.96); backdrop-filter: blur(16px);
    border-top: 1px solid var(--border);
    display: flex; justify-content: space-around; align-items: center;
    padding: 10px 0 calc(10px + var(--safe-bottom));
  }
  .nav-item { display:flex; flex-direction:column; align-items:center; gap:3px; text-decoration:none; }
  .nav-item i    { font-size:18px; color:var(--text-light); }
  .nav-item span { font-size:10px; color:var(--text-light); font-weight:600; }
  .nav-item.active i    { color: var(--green-main); }
  .nav-item.active span { color: var(--green-main); }

  @keyframes pulse-seat {
    0%, 100% { box-shadow: 0 0 0 0 rgba(234,179,8,.5); }
    50%       { box-shadow: 0 0 0 8px rgba(234,179,8,0); }
  }
  .my-seat { animation: pulse-seat 1.8s ease-in-out infinite; }
</style>

@php
  $totalTerisi = $terisi->count();
  $totalKosong = 54 - $totalTerisi;

  function renderSeatGuest($seatId, $penump, $isKetua, $isMySeat) {
      if ($isMySeat) {
          $bg     = '#fef08a';
          $border = '#eab308';
      } elseif ($penump) {
          $bg     = $isKetua ? '#1d4ed8' : '#16a34a';
          $border = $isKetua ? '#1e40af' : '#15803d';
      } else {
          $bg     = '#f8fafc';
          $border = '#e2e8f0';
      }

      $shortName = '';
      $nik = '';
      if ($penump) {
          $parts     = explode(' ', trim($penump->nama_karyawan));
          $shortName = count($parts) > 1
              ? $parts[0] . ' ' . substr($parts[1], 0, 1) . '.'
              : $parts[0];
          if (strlen($shortName) > 10) $shortName = substr($shortName, 0, 10) . '…';
          $nik = $penump->nik ?? '';
      }

      $tooltip     = $isMySeat
          ? '✨ Kursi Kamu! (' . $seatId . ')'
          : ($penump ? htmlspecialchars($penump->nama_karyawan) . " ($seatId)" : "$seatId — Kosong");

      $mySeatClass = $isMySeat ? 'my-seat' : '';

      $inner = $isMySeat
          ? "<div style='font-size:8px;color:#854d0e;font-weight:700;line-height:1;margin-bottom:1px;'>KAMU</div>
             <div style='font-size:9px;font-weight:800;color:#713f12;line-height:1.2;text-align:center;'>$shortName</div>"
          : ($penump
              ? "<div style='font-size:8px;color:rgba(255,255,255,.75);font-weight:500;line-height:1;margin-bottom:1px;'>$nik</div>
                 <div style='font-size:9px;font-weight:700;color:#fff;line-height:1.2;text-align:center;word-break:break-word;'>$shortName</div>"
              : "<i class='fa-solid fa-couch' style='font-size:14px;color:#e2e8f0;'></i>"
          );

      $shadow = ($penump || $isMySeat) ? 'box-shadow:0 2px 8px rgba(0,0,0,0.12);' : '';

      return "<div style='display:flex;flex-direction:column;align-items:center;gap:2px;'>
                  <div style='font-size:9px;font-weight:700;color:#94a3b8;background:#f1f5f9;
                              border:1px solid #e2e8f0;border-radius:4px;padding:1px 5px;
                              line-height:1.4;letter-spacing:.3px;'>$seatId</div>
                  <div title='$tooltip'
                       class='$mySeatClass'
                       style='width:100%;background:$bg;border:1.5px solid $border;border-radius:8px;
                              padding:5px 4px;text-align:center;min-height:48px;
                              display:flex;flex-direction:column;align-items:center;
                              justify-content:center;gap:2px;$shadow'>$inner</div>
              </div>";
  }

  $rows = [];
  $n = 1;
  for ($r = 1; $r <= 9; $r++) {
      $rows[$r] = ['left' => [$n, $n+1], 'right' => [$n+2, $n+3, $n+4]];
      $n += 5;
  }
  $backRow    = [49, 50, 51, 52, 53, 54];
  $row10right = [46, 47, 48];
@endphp

{{-- Hero --}}
{{-- Hero --}}
<div style="background:var(--green-dark);border-radius:18px;padding:18px;margin-bottom:14px;position:relative;overflow:hidden;">
  <div style="position:absolute;top:-20px;right:-30px;width:110px;height:110px;
              border-radius:50%;background:rgba(234,179,8,.15);pointer-events:none;"></div>
  <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.4);letter-spacing:1px;margin-bottom:6px;">
    BUS {{ $kode }}
  </div>
  <div style="font-size:22px;font-weight:800;color:#fff;">
    Kursi Kamu & Keluarga 🎉
  </div>
</div>

<div class="content">

  {{-- Back button --}}
  <a href="{{ route('guest.menu', ['key' => 'bus']) }}"
     style="display:inline-flex;align-items:center;gap:6px;margin-bottom:14px;
            background:var(--white);border:1px solid var(--border);border-radius:12px;
            padding:8px 14px;font-size:12px;font-weight:600;color:var(--text);
            text-decoration:none;">
    <i class="fa-solid fa-arrow-left" style="font-size:11px;"></i> Kembali ke Transportasi
  </a>

  {{-- Info Ketua --}}
  @if($ketua)
  <div style="background:var(--white);border:1px solid var(--border);border-radius:16px;
              padding:14px 16px;margin-bottom:14px;display:flex;align-items:center;gap:12px;">
    <div style="width:40px;height:40px;border-radius:12px;background:#1e293b;flex-shrink:0;
                display:flex;align-items:center;justify-content:center;">
      <i class="fa-solid fa-user-tie" style="color:#fff;font-size:15px;"></i>
    </div>
    <div style="flex:1;min-width:0;">
      <div style="font-size:10px;font-weight:600;color:var(--text-light);letter-spacing:.5px;">
        KETUA BUS {{ $kode }}
      </div>
      <div style="font-size:14px;font-weight:700;color:var(--text);margin-top:2px;">
        {{ $ketua->karyawan?->nama ?? '-' }}
      </div>
      @if($ketua->no_telp)
      <div style="font-size:12px;color:var(--text-light);margin-top:2px;">
        <i class="fa-solid fa-phone" style="font-size:10px;margin-right:3px;"></i>{{ $ketua->no_telp }}
      </div>
      @endif
    </div>
  </div>
  @endif

  {{-- Legend --}}
  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
    <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:#475569;">
      <div style="width:14px;height:14px;border-radius:4px;background:#fef08a;border:1.5px solid #eab308;"></div>
      Kamu / Keluarga
    </div>
    <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:#475569;">
      <div style="width:14px;height:14px;border-radius:4px;background:#16a34a;"></div>
      Terisi
    </div>
    <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:#475569;">
      <div style="width:14px;height:14px;border-radius:4px;background:#f1f5f9;border:1.5px solid #e2e8f0;"></div>
      Kosong
    </div>
    <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:#475569;">
      <div style="width:14px;height:14px;border-radius:4px;background:#1d4ed8;"></div>
      Ketua Bus
    </div>
  </div>

  {{-- Bus Body --}}
  <div style="background:#fff;border:2px solid #e2e8f0;border-radius:24px;
              padding:16px 12px;box-shadow:0 4px 24px rgba(0,0,0,0.06);">

    {{-- Depan --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
                margin-bottom:14px;padding-bottom:12px;border-bottom:2px dashed #f1f5f9;">
      <div style="display:flex;align-items:center;gap:8px;">
        <div style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;
                    display:flex;align-items:center;justify-content:center;">
          <i class="fa-solid fa-door-open" style="font-size:12px;color:#64748b;"></i>
        </div>
        <span style="font-size:10px;font-weight:700;color:#94a3b8;letter-spacing:.8px;text-transform:uppercase;">
          Pintu Masuk
        </span>
      </div>
      <div style="display:flex;align-items:center;gap:8px;">
        <span style="font-size:10px;font-weight:700;color:#94a3b8;letter-spacing:.8px;text-transform:uppercase;">
          Sopir
        </span>
        <div style="width:28px;height:28px;border-radius:8px;background:#1e293b;
                    display:flex;align-items:center;justify-content:center;">
          <i class="fa-solid fa-user" style="font-size:12px;color:#fff;"></i>
        </div>
      </div>
    </div>

    {{-- Baris 1–9 --}}
    <div style="display:flex;flex-direction:column;gap:5px;">
      @foreach($rows as $rowNum => $row)
      <div style="display:flex;align-items:center;gap:5px;">

        <div style="display:flex;gap:4px;flex:2;">
          @foreach($row['left'] as $n)
            @php
              $seatId  = $kode . '-' . $n;
              $penump  = $terisi[$seatId] ?? null;
              $isKetua = $penump && $ketua && $penump->tipe === 'karyawan' && $penump->nik === $ketua->nik;
              $isMySeat = $kursiSaya->contains($seatId);
            @endphp
            <div style="flex:1;">{!! renderSeatGuest($seatId, $penump, $isKetua, $isMySeat) !!}</div>
          @endforeach
        </div>

        <div style="width:24px;flex-shrink:0;text-align:center;">
          <span style="font-size:9px;font-weight:700;color:#cbd5e1;background:#f8fafc;
                       border-radius:5px;padding:2px 4px;display:block;">{{ $rowNum }}</span>
        </div>

        <div style="display:flex;gap:4px;flex:3;">
          @foreach($row['right'] as $n)
            @php
              $seatId  = $kode . '-' . $n;
              $penump  = $terisi[$seatId] ?? null;
              $isKetua = $penump && $ketua && $penump->tipe === 'karyawan' && $penump->nik === $ketua->nik;
              $isMySeat = $kursiSaya->contains($seatId);
            @endphp
            <div style="flex:1;">{!! renderSeatGuest($seatId, $penump, $isKetua, $isMySeat) !!}</div>
          @endforeach
        </div>

      </div>
      @endforeach
    </div>

    {{-- Baris 10 + Pintu Keluar --}}
    <div style="display:flex;align-items:stretch;gap:5px;margin-top:5px;">
      <div style="flex:2;display:flex;align-items:center;gap:8px;padding:0 2px;">
        <div style="width:24px;height:24px;border-radius:7px;background:#f1f5f9;flex-shrink:0;
                    display:flex;align-items:center;justify-content:center;">
          <i class="fa-solid fa-door-open" style="font-size:11px;color:#64748b;"></i>
        </div>
        <span style="font-size:9px;font-weight:700;color:#94a3b8;letter-spacing:.7px;text-transform:uppercase;">
          Pintu Keluar
        </span>
      </div>
      <div style="width:24px;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
        <span style="font-size:9px;font-weight:700;color:#cbd5e1;background:#f8fafc;
                     border-radius:5px;padding:2px 4px;display:block;">10</span>
      </div>
      <div style="display:flex;gap:4px;flex:3;">
        @foreach($row10right as $n)
          @php
            $seatId  = $kode . '-' . $n;
            $penump  = $terisi[$seatId] ?? null;
            $isKetua = $penump && $ketua && $penump->tipe === 'karyawan' && $penump->nik === $ketua->nik;
            $isMySeat = $kursiSaya->contains($seatId);
          @endphp
          <div style="flex:1;min-width:0;">{!! renderSeatGuest($seatId, $penump, $isKetua, $isMySeat) !!}</div>
        @endforeach
      </div>
    </div>

    {{-- Baris Belakang --}}
    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:4px;margin-top:16px;
                padding-top:14px;border-top:2px dashed #f1f5f9;">
      @foreach($backRow as $n)
        @php
          $seatId  = $kode . '-' . $n;
          $penump  = $terisi[$seatId] ?? null;
          $isKetua = $penump && $ketua && $penump->tipe === 'karyawan' && $penump->nik === $ketua->nik;
          $isMySeat = $kursiSaya->contains($seatId);
        @endphp
        {!! renderSeatGuest($seatId, $penump, $isKetua, $isMySeat) !!}
      @endforeach
    </div>

  </div>

  {{-- Footer Stats --}}
  <div style="background:var(--white);border:1px solid var(--border);border-radius:16px;
              padding:14px;margin-top:14px;display:flex;justify-content:space-around;text-align:center;">
    <div>
      <div style="font-size:16px;font-weight:700;color:#16a34a;">{{ $totalTerisi }}</div>
      <div style="font-size:10px;color:var(--text-light);">Terisi</div>
    </div>
    <div style="width:1px;background:var(--border);"></div>
    <div>
      <div style="font-size:16px;font-weight:700;color:#64748b;">{{ $totalKosong }}</div>
      <div style="font-size:10px;color:var(--text-light);">Kosong</div>
    </div>
    <div style="width:1px;background:var(--border);"></div>
    <div>
      <div style="font-size:14px;font-weight:700;color:#eab308;">{{ $kursiSaya->implode(', ') }}</div>
      <div style="font-size:10px;color:var(--text-light);">Kursi Kamu & Keluarga</div>
    </div>
  </div>

</div>

@endsection