@extends('guest.layouts.app')
@section('title', 'Kehadiran')

@section('content')

{{-- Status card --}}
<div style="background:var(--green-dark);border-radius:20px;padding:20px;margin-bottom:14px;position:relative;overflow:hidden;">
  <div style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:rgba(61,122,71,.25);"></div>
  <div style="font-size:11px;font-weight:600;color:rgba(255,255,255,.4);letter-spacing:1px;margin-bottom:6px;">STATUS HARI INI</div>

  <div style="font-size:32px;font-weight:800;margin-bottom:4px;position:relative;"
       id="bigStatus"
       class="{{ $karyawan->status_kehadiran ? '' : '' }}"
       style="color:{{ $karyawan->status_kehadiran ? '#7ec88a' : '#ef9a9a' }}">
    @if($karyawan->status_kehadiran)
      <span style="color:#7ec88a;">✓ Hadir</span>
    @else
      <span style="color:#ef9a9a;">✗ Belum Hadir</span>
    @endif
  </div>
  <div style="font-size:11.5px;color:rgba(255,255,255,.4);margin-bottom:18px;" id="statusSub">
    {{ $karyawan->status_kehadiran ? 'Kehadiran sudah tercatat' : 'Belum melakukan konfirmasi' }}
  </div>

  <button onclick="toggleKehadiran()"
          id="btnKonfirmasi"
          style="width:100%;padding:13px;border:none;border-radius:12px;
                 font-family:inherit;font-size:14px;font-weight:700;cursor:pointer;
                 transition:all .2s;
                 background:{{ $karyawan->status_kehadiran ? 'rgba(126,200,138,.2)' : 'rgba(245,158,11,.2)' }};
                 border:1px solid {{ $karyawan->status_kehadiran ? 'rgba(126,200,138,.35)' : 'rgba(245,158,11,.35)' }};
                 color:{{ $karyawan->status_kehadiran ? '#7ec88a' : '#fcd97d' }};">
    <span id="btnText">
      <i class="fa-solid {{ $karyawan->status_kehadiran ? 'fa-calendar-xmark' : 'fa-calendar-check' }}"></i>
      {{ $karyawan->status_kehadiran ? 'Batalkan Kehadiran' : 'Konfirmasi Hadir Sekarang' }}
    </span>
  </button>
</div>



{{-- Toast --}}
<div class="toast" id="toast" style="position:fixed;top:20px;left:50%;transform:translateX(-50%) translateY(-80px);background:#1a3320;color:#fff;border-radius:12px;padding:11px 18px;font-size:13px;font-weight:600;z-index:999;transition:transform .3s cubic-bezier(.34,1.56,.64,1);white-space:nowrap;box-shadow:0 4px 20px rgba(0,0,0,.25);display:flex;align-items:center;gap:8px;"></div>

@endsection

@section('scripts')
<script>
let loading = false;
let isHadir = {{ $karyawan->status_kehadiran ? 'true' : 'false' }};

function showToast(msg, type) {
  const t = document.getElementById('toast');
  t.style.background = type === 'success' ? '#2e7d32' : '#c62828';
  t.innerHTML = `<i class="fa-solid ${type==='success'?'fa-circle-check':'fa-circle-xmark'}"></i> ${msg}`;
  t.style.transform = 'translateX(-50%) translateY(70px)';
  setTimeout(() => t.style.transform = 'translateX(-50%) translateY(-80px)', 2800);
}

function toggleKehadiran() {
  if (loading) return;
  loading = true;
  document.getElementById('btnText').innerHTML = '<i class="fa-solid fa-circle-notch" style="animation:spin .7s linear infinite"></i> Memproses...';

  fetch('{{ route('guest.kehadiran') }}', {
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
    body:JSON.stringify({}),
  })
  .then(r => r.json())
  .then(data => {
    isHadir = data.status_kehadiran;
    const btn = document.getElementById('btnKonfirmasi');

    document.getElementById('bigStatus').innerHTML = isHadir
      ? '<span style="color:#7ec88a;">✓ Hadir</span>'
      : '<span style="color:#ef9a9a;">✗ Belum Hadir</span>';

    document.getElementById('statusSub').textContent = isHadir
      ? 'Kehadiran sudah tercatat' : 'Belum melakukan konfirmasi';

    document.getElementById('statusLabel').innerHTML = isHadir
      ? '<span class="badge badge-success"><span style="width:6px;height:6px;border-radius:50%;background:#4caf50;"></span> Hadir</span>'
      : '<span class="badge badge-danger"><span style="width:6px;height:6px;border-radius:50%;background:#ef5350;"></span> Belum Hadir</span>';

    btn.style.background = isHadir ? 'rgba(126,200,138,.2)' : 'rgba(245,158,11,.2)';
    btn.style.borderColor = isHadir ? 'rgba(126,200,138,.35)' : 'rgba(245,158,11,.35)';
    btn.style.color = isHadir ? '#7ec88a' : '#fcd97d';

    document.getElementById('btnText').innerHTML = isHadir
      ? '<i class="fa-solid fa-calendar-xmark"></i> Batalkan Kehadiran'
      : '<i class="fa-solid fa-calendar-check"></i> Konfirmasi Hadir Sekarang';

    showToast(data.message, 'success');
  })
  .catch(() => showToast('Gagal memperbarui', 'error'))
  .finally(() => loading = false);
}
</script>
<style>@keyframes spin { to { transform:rotate(360deg); } }</style>
@endsection