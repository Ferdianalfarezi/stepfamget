@extends('guest.layouts.app')
@section('title', 'Profil Saya')

@section('content')

{{-- Avatar + nama --}}
<div style="text-align:center;padding:20px 0 16px;">
  <div style="width:72px;height:72px;border-radius:20px;background:linear-gradient(135deg,#3d7a47,#1a3320);display:inline-flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;color:#fff;margin-bottom:12px;">
    {{ strtoupper(substr($karyawan->nama, 0, 2)) }}
  </div>
  <div style="font-size:18px;font-weight:800;color:#111;">{{ $karyawan->nama }}</div>
  <div style="font-size:12px;color:#999;margin-top:3px;">{{ $karyawan->departemen }}</div>
  <div style="margin-top:8px;">
    @if($karyawan->keterangan === 'Aktif')
      <span class="badge badge-success"><span style="width:6px;height:6px;border-radius:50%;background:#4caf50;"></span> Aktif</span>
    @else
      <span class="badge badge-danger">{{ $karyawan->keterangan }}</span>
    @endif
  </div>
</div>

{{-- Data karyawan --}}
<div class="section-title">DATA KARYAWAN</div>
<div class="card">
  <div class="info-row">
    <span class="info-key"><i class="fa-solid fa-hashtag"></i> NIK</span>
    <span class="info-val" style="font-family:monospace;letter-spacing:1px;">{{ $karyawan->nik }}</span>
  </div>
  @if($karyawan->nik_login)
  <div class="info-row">
    <span class="info-key"><i class="fa-solid fa-key"></i> NIK Login</span>
    <span class="info-val" style="font-family:monospace;letter-spacing:1px;">{{ $karyawan->nik_login }}</span>
  </div>
  @endif
  <div class="info-row">
    <span class="info-key"><i class="fa-solid fa-building"></i> Departemen</span>
    <span class="info-val">{{ $karyawan->departemen }}</span>
  </div>
  <div class="info-row">
    <span class="info-key"><i class="fa-solid fa-people-group"></i> Anggota Keluarga</span>
    <span class="info-val">{{ $karyawan->jumlah_keluarga }} orang</span>
  </div>
  <div class="info-row">
    <span class="info-key"><i class="fa-solid fa-circle-dot"></i> Status</span>
    <span class="info-val" style="color:{{ $karyawan->keterangan === 'Aktif' ? '#2e7d32' : '#c62828' }}">
      {{ $karyawan->keterangan }}
    </span>
  </div>
</div>

@endsection