@extends('layouts.app')
@section('title', 'Layout Bus ' . $kode)
@section('page-title', 'Transportasi')

@section('content')

@php
    $totalTerisi = $terisi->count();
    $totalKosong = 54 - $totalTerisi;
    $persen      = round($totalTerisi / 54 * 100);
    $snack       = $totalTerisi + 2;

    function renderSeat($seatId, $penump, $isKetua) {
        $bg       = $penump ? ($isKetua ? '#1d4ed8' : '#16a34a') : '#f8fafc';
        $border   = $penump ? ($isKetua ? '#1e40af' : '#15803d') : '#e2e8f0';
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
        $tooltip = $penump
            ? htmlspecialchars($penump->nama_karyawan) . " ($seatId)"
            : "$seatId — Kosong";
        $hover  = $penump
            ? "onmouseover=\"this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 16px rgba(0,0,0,0.18)'\"
               onmouseout=\"this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.12)'\""
            : '';
        $shadow = $penump ? 'box-shadow:0 2px 8px rgba(0,0,0,0.12);' : '';
        $cursor = $penump ? 'pointer' : 'default';
        $inner  = $penump
            ? "<div style='font-size:8px;color:rgba(255,255,255,0.75);font-weight:500;line-height:1;margin-bottom:1px;'>$nik</div>
               <div style='font-size:9px;font-weight:700;color:#fff;line-height:1.2;
                           text-align:center;word-break:break-word;'>$shortName</div>"
            : "<i class='fa-solid fa-couch' style='font-size:14px;color:#e2e8f0;'></i>";

        return "<div style='display:flex;flex-direction:column;align-items:center;gap:2px;'>
                    <div style='font-size:9px;font-weight:700;color:#94a3b8;background:#f1f5f9;
                                border:1px solid #e2e8f0;border-radius:4px;padding:1px 5px;
                                line-height:1.4;letter-spacing:.3px;'>$seatId</div>
                    <div title='$tooltip'
                         style='width:100%;background:$bg;border:1.5px solid $border;border-radius:8px;
                                padding:5px 4px;text-align:center;min-height:48px;
                                display:flex;flex-direction:column;align-items:center;
                                justify-content:center;gap:2px;
                                transition:transform .15s,box-shadow .15s;cursor:$cursor;$shadow'
                         $hover>$inner</div>
                </div>";
    }

    // Baris 1–9: kiri 2 kursi, kanan 3 kursi
    $rows = [];
    $n = 1;
    for ($r = 1; $r <= 9; $r++) {
        $rows[$r] = ['left' => [$n, $n+1], 'right' => [$n+2, $n+3, $n+4]];
        $n += 5;
    }
    // Baris belakang: 6 kursi (49–54)
    $backRow = [49, 50, 51, 52, 53, 54];
    // Baris 10 kanan: 46, 47, 48
    $row10right = [46, 47, 48];
@endphp

{{-- HEADER --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <a href="{{ route('bus.card') }}"
           style="width:36px;height:36px;border-radius:10px;background:#f1f5f9;border:1px solid #e2e8f0;
                  display:flex;align-items:center;justify-content:center;color:#64748b;text-decoration:none;transition:all .2s;"
           onmouseover="this.style.background='#0b4614';this.style.color='#fff'"
           onmouseout="this.style.background='#f1f5f9';this.style.color='#64748b'">
            <i class="fa-solid fa-arrow-left" style="font-size:13px;"></i>
        </a>
        <div>
            <div style="font-size:20px;font-weight:800;color:#0f172a;letter-spacing:-.4px;">
                Layout Bus <span style="color:#0b4614;">{{ $kode }}</span>
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">
                <i class="fa-solid fa-user-tie" style="margin-right:4px;"></i>
                <strong>{{ $ketua->karyawan?->nama ?? '-' }}</strong>
                @if($ketua->no_telp)
                    &nbsp;·&nbsp;<i class="fa-solid fa-phone" style="margin-right:3px;"></i>{{ $ketua->no_telp }}
                @endif
                &nbsp;·&nbsp;<i class="fa-solid fa-building" style="margin-right:3px;"></i>{{ $ketua->karyawan?->departemen ?? '-' }}
            </div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#475569;">
            <div style="width:14px;height:14px;border-radius:4px;background:#16a34a;"></div> Terisi
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#475569;">
            <div style="width:14px;height:14px;border-radius:4px;background:#f1f5f9;border:1.5px solid #e2e8f0;"></div> Kosong
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#475569;">
            <div style="width:14px;height:14px;border-radius:4px;background:#1d4ed8;"></div> Ketua Bus
        </div>
        <div style="display:flex;align-items:center;gap:8px;background:#f8fafc;border:1px solid #e2e8f0;
                    border-radius:10px;padding:6px 14px;font-size:12px;">
            <span style="color:#16a34a;font-weight:700;">{{ $totalTerisi }} terisi</span>
            <span style="color:#e2e8f0;">|</span>
            <span style="color:#64748b;">{{ $totalKosong }} kosong</span>
            <span style="color:#e2e8f0;">|</span>
            <span style="color:#0b4614;font-weight:700;">{{ $snack }} snack</span>
        </div>
    </div>
</div>

{{-- PROGRESS --}}
<div style="margin-bottom:16px;">
    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
        <span style="font-size:11px;color:#94a3b8;font-weight:600;letter-spacing:.5px;text-transform:uppercase;">Kapasitas</span>
        <span style="font-size:12px;font-weight:700;color:#0b4614;">{{ $persen }}% terisi</span>
    </div>
    <div style="height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
        <div style="height:6px;border-radius:3px;background:linear-gradient(90deg,#0b4614,#16a34a);
                    width:{{ $persen }}%;transition:width .8s cubic-bezier(.22,.61,.36,1);"></div>
    </div>
</div>

{{-- 2 KOLOM: BUS + PANEL PENUMPANG --}}
<div style="display:flex;gap:16px;align-items:flex-start;">

    {{-- KIRI: BUS BODY --}}
    <div style="flex:1;min-width:0;">
        <div style="display:flex;justify-content:center;">
        <div style="background:#fff;border:2px solid #e2e8f0;border-radius:24px;
                    padding:20px 16px;width:100%;max-width:560px;
                    box-shadow:0 4px 24px rgba(0,0,0,0.06);">

            {{-- DEPAN --}}
            <div style="display:flex;align-items:center;justify-content:space-between;
                        margin-bottom:16px;padding-bottom:14px;border-bottom:2px dashed #f1f5f9;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:32px;height:32px;border-radius:8px;background:#f1f5f9;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-door-open" style="font-size:13px;color:#64748b;"></i>
                    </div>
                    <span style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.8px;text-transform:uppercase;">Pintu Masuk</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:.8px;text-transform:uppercase;">Sopir</span>
                    <div style="width:32px;height:32px;border-radius:8px;background:#1e293b;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-user" style="font-size:13px;color:#fff;"></i>
                    </div>
                </div>
            </div>

            {{-- BARIS 1–9 --}}
            <div style="display:flex;flex-direction:column;gap:6px;">
                @foreach($rows as $rowNum => $row)
                <div style="display:flex;align-items:center;gap:6px;">
                    <div style="display:flex;gap:5px;flex:2;">
                        @foreach($row['left'] as $n)
                            @php $seatId = $kode.'-'.$n; $penump = $terisi[$seatId] ?? null; $isKetua = $penump && $penump->nik === $ketua->nik; @endphp
                            <div style="flex:1;">{!! renderSeat($seatId, $penump, $isKetua) !!}</div>
                        @endforeach
                    </div>
                    <div style="width:28px;flex-shrink:0;text-align:center;">
                        <span style="font-size:10px;font-weight:700;color:#cbd5e1;background:#f8fafc;
                                     border-radius:6px;padding:3px 5px;display:block;">{{ $rowNum }}</span>
                    </div>
                    <div style="display:flex;gap:5px;flex:3;">
                        @foreach($row['right'] as $n)
                            @php $seatId = $kode.'-'.$n; $penump = $terisi[$seatId] ?? null; $isKetua = $penump && $penump->nik === $ketua->nik; @endphp
                            <div style="flex:1;">{!! renderSeat($seatId, $penump, $isKetua) !!}</div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- BARIS 10 + PINTU KELUAR --}}
            <div style="display:flex;align-items:stretch;gap:6px;margin-top:6px;">

                {{-- Kiri: Pintu Keluar --}}
                <div style="flex:2;min-height:56px;border-radius:8px;background:#fff;
                            display:flex;align-items:center;gap:8px;padding:0 0px;">
                    <div style="width:28px;height:28px;border-radius:8px;background:#f1f5f9;flex-shrink:0;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-door-open" style="font-size:12px;color:#64748b;"></i>
                    </div>
                    <span style="font-size:10px;font-weight:700;color:#94a3b8;letter-spacing:.7px;text-transform:uppercase;">Pintu Keluar</span>
                </div>

                {{-- Nomor baris 10 --}}
                <div style="width:28px;flex-shrink:0;text-align:center;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:10px;font-weight:700;color:#cbd5e1;background:#f8fafc;
                                 border-radius:6px;padding:3px 5px;display:block;">10</span>
                </div>

                {{-- Kanan: 46, 47, 48 --}}
                <div style="display:flex;gap:5px;flex:3;">
                    @foreach($row10right as $n)
                        @php $seatId = $kode.'-'.$n; $penump = $terisi[$seatId] ?? null; $isKetua = $penump && $penump->nik === $ketua->nik; @endphp
                        <div style="flex:1;min-width:0;">{!! renderSeat($seatId, $penump, $isKetua) !!}</div>
                    @endforeach
                </div>

            </div>

            {{-- BARIS BELAKANG: 6 kursi --}}
            <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:5px;margin-top:20px;">
                @foreach($backRow as $n)
                    @php $seatId = $kode.'-'.$n; $penump = $terisi[$seatId] ?? null; $isKetua = $penump && $penump->nik === $ketua->nik; @endphp
                    {!! renderSeat($seatId, $penump, $isKetua) !!}
                @endforeach
            </div>

        </div>
        </div>
    </div>
    {{-- END KIRI --}}

    {{-- KANAN: PANEL PENUMPANG --}}
    <div style="width:280px;flex-shrink:0;background:#fff;border:1px solid #e2e8f0;
                border-radius:16px;overflow:hidden;position:sticky;top:16px;">

        {{-- Header --}}
        <div style="background:#0b4614;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;">
            <div style="color:#fff;font-size:13px;font-weight:700;display:flex;align-items:center;gap:8px;">
                <i class="fa-solid fa-users" style="font-size:14px;"></i>
                Penumpang Bus {{ $kode }}
            </div>
            <span id="paxBadgeCount"
                  style="background:rgba(255,255,255,.2);color:#fff;font-size:11px;
                         padding:2px 9px;border-radius:20px;font-weight:600;">
                {{ $totalTerisi }}
            </span>
        </div>

        {{-- Search --}}
        <div style="padding:10px 12px;border-bottom:1px solid #f1f5f9;">
            <div style="position:relative;">
                <i class="fa-solid fa-magnifying-glass"
                   style="position:absolute;left:9px;top:50%;transform:translateY(-50%);
                          font-size:11px;color:#94a3b8;pointer-events:none;"></i>
                <input type="text" id="paxSearch"
                       placeholder="Cari nama / NIK / kursi..."
                       oninput="filterPenumpang(this.value)"
                       style="width:100%;box-sizing:border-box;font-size:12px;
                              padding:6px 10px 6px 28px;border-radius:8px;
                              border:1px solid #e2e8f0;background:#f8fafc;
                              color:#1e293b;outline:none;" />
            </div>
        </div>

        {{-- List --}}
        <div id="paxList" style="max-height:560px;overflow-y:auto;">
            @forelse($terisi->sortBy(fn($v,$k) => (int) explode('-', $k)[1] ?? 0) as $seatId => $p)
            @php
                $isKetua  = $p->nik === $ketua->nik;
                $parts    = explode(' ', trim($p->nama_karyawan));
                $ini      = strtoupper(substr($parts[0],0,1) . (isset($parts[1]) ? substr($parts[1],0,1) : substr($parts[0],1,1)));
                $deptVal  = $p->departemen ?? ($p->karyawan?->departemen ?? '');
            @endphp
            <div class="pax-row"
                 data-nama="{{ strtolower($p->nama_karyawan) }}"
                 data-nik="{{ $p->nik }}"
                 data-seat="{{ strtolower($seatId) }}"
                 data-dept="{{ strtolower($deptVal) }}"
                 style="display:flex;align-items:center;gap:10px;padding:9px 14px;
                        border-bottom:1px solid #f8fafc;cursor:default;transition:background .12s;"
                 onmouseover="this.style.background='#f8fafc'"
                 onmouseout="this.style.background=''">

                {{-- Avatar --}}
                <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;
                            display:flex;align-items:center;justify-content:center;
                            font-size:11px;font-weight:700;
                            background:{{ $isKetua ? '#dbeafe' : '#dcfce7' }};
                            color:{{ $isKetua ? '#1d4ed8' : '#15803d' }};">
                    {{ $ini }}
                </div>

                {{-- Info --}}
                <div style="flex:1;min-width:0;">

                    {{-- Nama + badge ketua --}}
                    <div style="display:flex;align-items:center;gap:5px;">
                        <span style="font-size:12px;font-weight:700;color:#1e293b;
                                     white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                              title="{{ $p->nama_karyawan }}">
                            {{ $p->nama_karyawan }}
                        </span>
                        @if($isKetua)
                        <span style="font-size:9px;padding:1px 5px;border-radius:8px;white-space:nowrap;flex-shrink:0;
                                     background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                            Ketua
                        </span>
                        @endif
                    </div>

                    {{-- NIK + Kursi --}}
                    <div style="display:flex;align-items:center;gap:5px;margin-top:2px;">
                        <span style="font-size:10px;color:#94a3b8;display:flex;align-items:center;gap:3px;">
                            <i class="fa-solid fa-id-card" style="font-size:9px;"></i>
                            {{ $p->nik }}
                        </span>
                        <span style="font-size:10px;padding:1px 6px;border-radius:10px;font-weight:600;
                                     background:{{ $isKetua ? '#eff6ff' : '#f0fdf4' }};
                                     color:{{ $isKetua ? '#1d4ed8' : '#15803d' }};
                                     border:1px solid {{ $isKetua ? '#bfdbfe' : '#bbf7d0' }};">
                            {{ $seatId }}
                        </span>
                    </div>

                    {{-- Departemen --}}
                    @if($deptVal)
                    <div style="font-size:10px;color:#94a3b8;margin-top:2px;
                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
                                display:flex;align-items:center;gap:3px;">
                        <i class="fa-solid fa-building" style="font-size:9px;"></i>
                        {{ $deptVal }}
                    </div>
                    @endif

                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;color:#94a3b8;font-size:12px;">
                <i class="fa-solid fa-seat" style="font-size:24px;display:block;margin-bottom:8px;color:#e2e8f0;"></i>
                Belum ada penumpang
            </div>
            @endforelse

            {{-- Empty state saat search --}}
            <div id="paxEmptyState"
                 style="display:none;padding:32px;text-align:center;color:#94a3b8;font-size:12px;">
                <i class="fa-solid fa-magnifying-glass" style="font-size:20px;display:block;margin-bottom:8px;color:#e2e8f0;"></i>
                Tidak ditemukan
            </div>
        </div>

        {{-- Footer Stats --}}
        <div style="padding:10px 14px;border-top:1px solid #f1f5f9;background:#f8fafc;">
            <div style="display:flex;justify-content:space-between;text-align:center;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#16a34a;">{{ $totalTerisi }}</div>
                    <div style="font-size:10px;color:#94a3b8;">Terisi</div>
                </div>
                <div style="width:1px;background:#e2e8f0;"></div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:#64748b;">{{ $totalKosong }}</div>
                    <div style="font-size:10px;color:#94a3b8;">Kosong</div>
                </div>
                <div style="width:1px;background:#e2e8f0;"></div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:#0b4614;">{{ $snack }}</div>
                    <div style="font-size:10px;color:#94a3b8;">Snack</div>
                </div>
                <div style="width:1px;background:#e2e8f0;"></div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:#1d4ed8;">1</div>
                    <div style="font-size:10px;color:#94a3b8;">Ketua</div>
                </div>
            </div>
        </div>

    </div>
    {{-- END KANAN --}}

</div>
{{-- END 2 KOLOM --}}

<script>
function filterPenumpang(q) {
    q = q.trim().toLowerCase();
    const rows = document.querySelectorAll('.pax-row');
    let visible = 0;

    rows.forEach(function(el) {
        const match = !q
            || el.dataset.nama.includes(q)
            || el.dataset.nik.includes(q)
            || el.dataset.seat.includes(q)
            || el.dataset.dept.includes(q);
        el.style.display = match ? 'flex' : 'none';
        if (match) visible++;
    });

    document.getElementById('paxEmptyState').style.display = visible === 0 ? 'block' : 'none';
    document.getElementById('paxBadgeCount').textContent = visible;
}
</script>

@endsection