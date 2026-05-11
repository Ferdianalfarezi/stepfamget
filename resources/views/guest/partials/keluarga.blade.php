@extends('guest.layouts.app')
@section('title', 'Keluarga')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
  <div class="section-title" style="margin-bottom:0;">ANGGOTA KELUARGA</div>
  <span style="background:#e8f5e9;color:#2e7d32;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">
    {{ $karyawan->details->count() }} orang
  </span>
</div>

@forelse($karyawan->details as $d)
<div class="card" style="padding:14px;">
  <div style="display:flex;align-items:center;gap:12px;">
    <div style="width:44px;height:44px;border-radius:12px;background:#e8f5e9;color:#2e7d32;display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:800;flex-shrink:0;">
      {{ strtoupper(substr($d->nama_keluarga, 0, 1)) }}
    </div>
    <div style="flex:1;">
      <div style="font-size:14px;font-weight:700;color:#111;">{{ $d->nama_keluarga }}</div>
      <div style="font-size:11.5px;color:#999;margin-top:2px;">
        {{ $d->hubungan }} &middot; {{ $d->jenis_kelamin }}
      </div>
    </div>
    @if($d->umur)
    <div style="font-size:13px;font-weight:700;color:#555;flex-shrink:0;">
      {{ $d->umur }} th
    </div>
    @endif
  </div>

  @if($d->tanggal_lahir || $d->ukuran_kaos)
  <div style="border-top:1px solid #f0f4f0;margin-top:12px;padding-top:10px;display:flex;gap:16px;">
    @if($d->tanggal_lahir)
    <div style="font-size:11.5px;color:#999;">
      <i class="fa-solid fa-cake-candles" style="color:#3d7a47;margin-right:5px;"></i>
      {{ \Carbon\Carbon::parse($d->tanggal_lahir)->translatedFormat('d F Y') }}
    </div>
    @endif
    @if($d->ukuran_kaos)
    <div style="font-size:11.5px;color:#999;">
      <i class="fa-solid fa-shirt" style="color:#3d7a47;margin-right:5px;"></i>
      Kaos {{ $d->ukuran_kaos }}
    </div>
    @endif
  </div>
  @endif
</div>
@empty
<div style="text-align:center;padding:40px 20px;color:#ccc;">
  <i class="fa-solid fa-user-slash" style="font-size:36px;display:block;margin-bottom:12px;"></i>
  <div style="font-size:13px;">Belum ada data anggota keluarga</div>
</div>
@endforelse

@endsection