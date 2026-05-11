@extends('guest.layouts.app')
@section('title', 'Voting Tempat')

@section('content')

{{-- Header --}}
<div style="background:var(--green-dark);border-radius:18px;padding:18px;margin-bottom:14px;position:relative;overflow:hidden;">
  <div style="position:absolute;top:-30px;right:-30px;width:110px;height:110px;border-radius:50%;background:rgba(21,101,192,.2);pointer-events:none;"></div>
  <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.4);letter-spacing:1px;margin-bottom:6px;">VOTING TEMPAT GATHERING</div>
  <div style="font-size:22px;font-weight:800;color:#fff;margin-bottom:4px;">Pilih Tempatmu!</div>
  <div style="font-size:12px;color:rgba(255,255,255,.4);">
    @if($myVote)
      <span style="color:#7ec88a;font-weight:600;">✓ Kamu sudah memberikan suara</span>
    @else
      Pilih salah satu tempat di bawah
    @endif
  </div>
</div>

{{-- Sudah vote — tampilkan terima kasih --}}
@if($myVote)
<div style="background:#e8f5e9;border:1px solid #c8e6c9;border-radius:14px;padding:14px 16px;margin-bottom:14px;display:flex;align-items:center;gap:12px;">
  <div style="width:36px;height:36px;border-radius:10px;background:#3d7a47;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
    <i class="fa-solid fa-check" style="color:#fff;font-size:15px;"></i>
  </div>
  <div>
    <div style="font-size:13px;font-weight:700;color:#2e7d32;">Terima kasih telah voting!</div>
    <div style="font-size:11.5px;color:#555;margin-top:2px;">Suaramu sudah tercatat. Kamu bisa mengubah pilihan kapan saja.</div>
  </div>
</div>
@endif

{{-- List kandidat --}}
@forelse($tempats as $t)
@php $isMyVote = $myVote && $myVote->voting_tempat_id == $t->id; @endphp

<div class="tempat-card" data-id="{{ $t->id }}"
     onclick="doVote({{ $t->id }})"
     style="border-radius:16px;padding:16px;margin-bottom:10px;cursor:pointer;
            transition:all .2s;-webkit-tap-highlight-color:transparent;
            @if($isMyVote)
              background:#fff;border:2px solid #3d7a47;box-shadow:0 4px 16px rgba(61,122,71,.15);
            @elseif($myVote)
              background:#f5f5f5;border:2px solid transparent;opacity:0.55;
            @else
              background:#fff;border:2px solid #e0e8e0;
            @endif">

  <div style="display:flex;align-items:center;gap:12px;">

    {{-- Icon/check --}}
    <div style="width:44px;height:44px;border-radius:12px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
                background:{{ $isMyVote ? '#e8f5e9' : ($myVote ? '#e0e0e0' : '#e8f0fe') }};">
      @if($isMyVote)
        <i class="fa-solid fa-check" style="color:#3d7a47;font-size:18px;"></i>
      @else
        <i class="fa-solid fa-location-dot" style="color:{{ $myVote ? '#bbb' : '#1565c0' }};font-size:18px;"></i>
      @endif
    </div>

    {{-- Info --}}
    <div style="flex:1;">
      <div style="font-size:15px;font-weight:{{ $isMyVote ? '800' : '600' }};color:{{ $isMyVote ? '#111' : ($myVote ? '#999' : '#111') }};">
        {{ $t->nama }}
        @if($isMyVote)
          <span style="background:#e8f5e9;color:#2e7d32;border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;margin-left:6px;">Pilihanmu</span>
        @endif
      </div>
      @if($t->lokasi)
      <div style="font-size:12px;color:{{ $isMyVote ? '#999' : ($myVote ? '#bbb' : '#777') }};margin-top:2px;">
        <i class="fa-solid fa-location-dot" style="margin-right:4px;font-size:11px;"></i>{{ $t->lokasi }}
      </div>
      @endif
      @if($t->deskripsi && $isMyVote)
      <div style="font-size:12px;color:#666;margin-top:4px;line-height:1.5;">{{ $t->deskripsi }}</div>
      @endif
    </div>

    {{-- Arrow --}}
    <i class="fa-solid fa-chevron-right" style="color:{{ $isMyVote ? '#3d7a47' : ($myVote ? '#ddd' : '#bbb') }};font-size:13px;flex-shrink:0;"></i>

  </div>

</div>
@empty
<div style="text-align:center;padding:48px 20px;color:#ccc;">
  <i class="fa-solid fa-map-location-dot" style="font-size:40px;display:block;margin-bottom:12px;"></i>
  <div style="font-size:13px;">Belum ada kandidat tempat</div>
  <div style="font-size:11px;margin-top:4px;color:#ddd;">Tunggu admin menambahkan pilihan</div>
</div>
@endforelse

{{-- Toast --}}
<div id="toast" style="position:fixed;top:20px;left:50%;transform:translateX(-50%) translateY(-80px);
     background:#1a3320;color:#fff;border-radius:12px;padding:11px 18px;font-size:13px;font-weight:600;
     z-index:999;transition:transform .3s cubic-bezier(.34,1.56,.64,1);white-space:nowrap;
     box-shadow:0 4px 20px rgba(0,0,0,.25);display:flex;align-items:center;gap:8px;"></div>

@endsection

@section('scripts')
<script>
let myVoteId = {{ $myVote ? $myVote->voting_tempat_id : 'null' }};
let loading  = false;

function showToast(msg, type) {
  const t = document.getElementById('toast');
  t.style.background = type === 'success' ? '#2e7d32' : '#c62828';
  t.innerHTML = `<i class="fa-solid ${type==='success'?'fa-circle-check':'fa-circle-xmark'}"></i> ${msg}`;
  t.style.transform = 'translateX(-50%) translateY(70px)';
  setTimeout(() => t.style.transform = 'translateX(-50%) translateY(-80px)', 2800);
}

function doVote(tempatId) {
  if (loading) return;
  if (myVoteId === tempatId) { showToast('Ini sudah pilihanmu!', 'success'); return; }
  loading = true;

  fetch('{{ route('guest.voting.post') }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ tempat_id: tempatId }),
  })
  .then(r => r.json())
  .then(() => location.reload())
  .catch(() => { showToast('Gagal menyimpan vote', 'error'); loading = false; });
}
</script>
@endsection