@extends('guest.layouts.app')
@section('title', 'Transportasi')

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

  .content { padding: 20px 16px; max-width: 520px; margin: 0 auto; }

  /* ── Status Card ── */
  .status-card {
    background: var(--white); border: 1px solid var(--border);
    border-radius: 20px; padding: 20px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 16px;
  }
  .status-icon-wrap {
    width: 52px; height: 52px; border-radius: 16px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 22px;
  }
  .status-icon-wrap.bus       { background: #e8f5e9; color: #2e7d32; }
  .status-icon-wrap.kendaraan { background: #fff8e1; color: #f57f17; }
  .status-detail { flex: 1; min-width: 0; }
  .status-label  { font-size: 11px; font-weight: 600; color: var(--text-light); letter-spacing:.8px; }
  .status-value  { font-size: 16px; font-weight: 800; color: var(--text); margin-top: 2px; }
  .status-sub    { font-size: 12px; color: var(--text-light); margin-top: 2px; }

  /* ── Pilihan Grid ── */
  .pilihan-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 12px; margin-bottom: 24px;
  }
  .pilihan-card {
    background: var(--white); border: 2px solid var(--border);
    border-radius: 20px; padding: 20px 12px;
    display: flex; flex-direction: column; align-items: center; gap: 12px;
    cursor: pointer; transition: border-color .2s, box-shadow .2s, transform .15s;
    -webkit-tap-highlight-color: transparent; user-select: none;
  }
  .pilihan-card:active { transform: scale(.95); }
  .pilihan-card.selected {
    border-color: var(--green-main);
    box-shadow: 0 0 0 3px rgba(61,122,71,.12);
  }
  .pilihan-icon {
    width: 60px; height: 60px; border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; transition: background .2s;
  }
  .pilihan-card:not(.selected) .pilihan-icon.bus-icon  { background: #f1f8f2; color: #3d7a47; }
  .pilihan-card:not(.selected) .pilihan-icon.kend-icon { background: #fffbf0; color: #b45309; }
  .pilihan-card.selected       .pilihan-icon.bus-icon  { background: #dcf0de; color: #1a5c20; }
  .pilihan-card.selected       .pilihan-icon.kend-icon { background: #fef3c7; color: #92400e; }
  .pilihan-label { font-size: 13px; font-weight: 700; color: var(--text); text-align: center; line-height: 1.3; }
  .pilihan-sub   { font-size: 11px; color: var(--text-light); text-align: center; margin-top: -6px; line-height: 1.4; }
  .pilihan-check {
    width: 22px; height: 22px; border-radius: 50%;
    border: 2px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    transition: all .2s; font-size: 11px; color: transparent;
  }
  .pilihan-card.selected .pilihan-check {
    background: var(--green-main); border-color: var(--green-main); color: #fff;
  }

  /* ── Plat Wrap ── */
  .plat-wrap {
    background: var(--white); border: 1px solid var(--border);
    border-radius: 16px; padding: 16px 18px;
    margin-bottom: 20px; display: none;
  }
  .plat-wrap.show { display: block; }
  .jenis-title { font-size: 12px; font-weight: 700; color: var(--text-light); letter-spacing: .5px; margin-bottom: 10px; }
  .jenis-grid  { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 16px; }
  .jenis-btn {
    border: 2px solid var(--border); border-radius: 14px;
    padding: 12px 6px; display: flex; flex-direction: column;
    align-items: center; gap: 5px; cursor: pointer;
    transition: border-color .2s, background .2s, box-shadow .2s, transform .15s;
    user-select: none; -webkit-tap-highlight-color: transparent; background: #fafcfa;
  }
  .jenis-btn:active { transform: scale(.93); }
  .jenis-btn.selected {
    border-color: var(--green-main); background: #f0faf1;
    box-shadow: 0 0 0 3px rgba(61,122,71,.10);
  }
  .jenis-btn-emoji { font-size: 24px; line-height: 1; }
  .jenis-btn-label { font-size: 11px; font-weight: 700; color: var(--text); }
  .plat-divider { border: none; border-top: 1px solid var(--border); margin: 4px 0 14px; }
  .plat-label   { font-size: 12px; font-weight: 700; color: var(--text-light); letter-spacing: .5px; margin-bottom: 8px; }
  .plat-input {
    width: 100%; padding: 12px 14px;
    border: 1.5px solid var(--border); border-radius: 12px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 17px; font-weight: 700; letter-spacing: 2px;
    text-transform: uppercase; color: var(--text);
    background: #f8faf8; outline: none; transition: border-color .2s;
  }
  .plat-input:focus { border-color: var(--green-main); background: #fff; }
  .plat-input::placeholder { font-weight: 400; letter-spacing: 0; color: #bbb; font-size: 14px; }

  /* ── Buttons ── */
  .btn-simpan {
    width: 100%; padding: 15px;
    background: var(--green-dark); border: none; border-radius: 14px;
    color: #fff; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 15px; font-weight: 700; cursor: pointer;
    transition: opacity .2s, transform .15s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
  }
  .btn-simpan:disabled { opacity: .5; cursor: not-allowed; }
  .btn-simpan:not(:disabled):active { transform: scale(.97); }
  .btn-batal {
    width: 100%; margin-top: 10px; padding: 12px;
    background: none; border: 1.5px solid #e5e7eb; border-radius: 14px;
    color: #9ca3af; font-family: inherit; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all .2s; display: none;
  }
  .btn-batal.show { display: flex; align-items: center; justify-content: center; gap: 6px; }
  .btn-batal:active { background: #f9fafb; }

  /* ── Bottom Nav ── */
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

  /* ── Toast ── */
  .toast {
    position:fixed; top:20px; left:50%; transform:translateX(-50%) translateY(-80px);
    background:#1a3320; color:#fff; border-radius:12px;
    padding:11px 18px; font-size:13px; font-weight:600;
    z-index:999; transition:transform .3s cubic-bezier(.34,1.56,.64,1);
    white-space:nowrap; box-shadow:0 4px 20px rgba(0,0,0,.25);
    display:flex; align-items:center; gap:8px;
  }
  .toast.show { transform:translateX(-50%) translateY(calc(var(--safe-top) + 60px)); }
  .toast.success { background:#2e7d32; }
  .toast.error   { background:#c62828; }

  @keyframes spin { to { transform:rotate(360deg); } }
  .fa-spin-custom { animation:spin .7s linear infinite; }
</style>
</head>
<body>

<div class="toast" id="toast"></div>

@php
  $pilihanBus       = \App\Models\Bus::where('nik', $karyawan->nik)->first();
  $pilihanKendaraan = \App\Models\Kendaraan::where('nik', $karyawan->nik)->first();
  $currentPilihan   = $pilihanBus ? 'bus' : ($pilihanKendaraan ? 'kendaraan' : null);
  $currentJenis     = $pilihanKendaraan->jenis_kendaraan ?? 'mobil';
  $isExpired        = $isExpired ?? false;
@endphp

{{-- Hero Card --}}
<div style="background:var(--green-dark);border-radius:18px;padding:18px;margin-bottom:14px;position:relative;overflow:hidden;">
  <div style="position:absolute;top:-20px;right:-30px;width:110px;height:110px;border-radius:50%;background:rgba(21,101,192,.2);pointer-events:none;"></div>
  <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.4);letter-spacing:1px;margin-bottom:6px;">Pilih / Ubah Jenis Transportasi</div>
  <div style="font-size:22px;font-weight:800;color:#fff;margin-bottom:8px;">Kamu mau naik apa nih nanti?</div>

  @if($isExpired)
    <div style="display:inline-flex;align-items:center;gap:6px;
                background:rgba(229,57,53,.25);border:1px solid rgba(229,57,53,.4);
                border-radius:20px;padding:5px 12px;">
      <i class="fa-solid fa-lock" style="color:#ff8a80;font-size:11px;"></i>
      <span style="font-size:12px;font-weight:700;color:#ff8a80;">Pendaftaran Ditutup</span>
    </div>
  @elseif($menu->berlaku_hingga)
    <div style="display:inline-flex;align-items:center;gap:6px;
                background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);
                border-radius:20px;padding:5px 12px;">
      <i class="fa-solid fa-hourglass-half" style="color:rgba(255,255,255,.6);font-size:11px;" id="heroIcon"></i>
      <span style="font-size:12px;font-weight:700;color:rgba(255,255,255,.8);">
        Ditutup dalam <span id="heroCountdown" style="font-variant-numeric:tabular-nums;">--:--:--</span>
      </span>
    </div>
  @else
    <div style="font-size:12px;color:rgba(255,255,255,.4);">Pilih transportasi sesuai rencanamu</div>
  @endif
</div>

<div class="content">

  {{-- Status Card --}}
  <div class="status-card" id="statusCard" style="{{ $currentPilihan ? '' : 'display:none;' }}">
    <div class="status-icon-wrap {{ $currentPilihan ?? 'bus' }}" id="statusIcon">
      <i class="fa-solid {{ $currentPilihan === 'kendaraan' ? 'fa-car' : 'fa-bus' }}"></i>
    </div>
    <div class="status-detail">
      <div class="status-label">PILIHAN SAAT INI</div>
      <div class="status-value" id="statusValue">
        @if($currentPilihan === 'bus') Naik Bus
        @elseif($currentPilihan === 'kendaraan') {{ ucfirst($currentJenis) }} Pribadi
        @endif
      </div>
      <div class="status-sub" id="statusSub">
        @if($currentPilihan === 'kendaraan' && $pilihanKendaraan)
          <i class="fa-solid fa-hashtag" style="font-size:10px;margin-right:3px;"></i>{{ $pilihanKendaraan->plat_no }}
        @else
          Sudah terdaftar
        @endif
      </div>
    </div>
    <div>
      <span style="background:#e8f5e9;color:#2e7d32;font-size:11px;font-weight:700;padding:5px 10px;border-radius:20px;">
        <i class="fa-solid fa-check" style="font-size:10px;margin-right:3px;"></i>Terdaftar
      </span>
    </div>
  </div>

  {{-- Pilihan Transport --}}
  <div class="pilihan-grid">
    <div class="pilihan-card {{ $currentPilihan === 'bus' ? 'selected' : '' }}"
         id="cardBus" onclick="selectPilihan('bus')">
      <div class="pilihan-check"><i class="fa-solid fa-check" style="font-size:10px;"></i></div>
      <div class="pilihan-icon bus-icon"><i class="fa-solid fa-bus"></i></div>
      <div class="pilihan-label">Naik Bus</div>
      <div class="pilihan-sub">Fasilitas dari<br>panitia</div>
    </div>
    <div class="pilihan-card {{ $currentPilihan === 'kendaraan' ? 'selected' : '' }}"
         id="cardKendaraan" onclick="selectPilihan('kendaraan')">
      <div class="pilihan-check"><i class="fa-solid fa-check" style="font-size:10px;"></i></div>
      <div class="pilihan-icon kend-icon"><i class="fa-solid fa-car"></i></div>
      <div class="pilihan-label">Kendaraan Pribadi</div>
      <div class="pilihan-sub">Bawa kendaraan<br>sendiri</div>
    </div>
  </div>

  {{-- Detail Kendaraan --}}
  <div class="plat-wrap {{ $currentPilihan === 'kendaraan' ? 'show' : '' }}" id="platWrap">
    <div class="jenis-title">JENIS KENDARAAN</div>
    <div class="jenis-grid">
      <div class="jenis-btn {{ $currentJenis === 'mobil' ? 'selected' : '' }}" data-jenis="mobil" onclick="selectJenis('mobil')">
        <span class="jenis-btn-emoji">🚗</span><span class="jenis-btn-label">Mobil</span>
      </div>
      <div class="jenis-btn {{ $currentJenis === 'motor' ? 'selected' : '' }}" data-jenis="motor" onclick="selectJenis('motor')">
        <span class="jenis-btn-emoji">🏍️</span><span class="jenis-btn-label">Motor</span>
      </div>
      <div class="jenis-btn {{ $currentJenis === 'truk' ? 'selected' : '' }}" data-jenis="truk" onclick="selectJenis('truk')">
        <span class="jenis-btn-emoji">🚛</span><span class="jenis-btn-label">Truk</span>
      </div>
    </div>
    <hr class="plat-divider">
    <div class="plat-label">PLAT NOMOR KENDARAAN</div>
    <input type="text" class="plat-input" id="platInput"
           placeholder="Contoh: KT 1234 AB" maxlength="12"
           value="{{ $pilihanKendaraan->plat_no ?? '' }}">
  </div>

  <button class="btn-simpan" id="btnSimpan" onclick="simpanPilihan()" disabled>
    <i class="fa-solid fa-floppy-disk"></i> Simpan Pilihan
  </button>
  <button class="btn-batal {{ $currentPilihan ? 'show' : '' }}" id="btnBatal" onclick="batalkanPilihan()">
    <i class="fa-solid fa-xmark"></i> Batalkan Pilihan
  </button>

</div>

<div class="bottom-nav">
  <a href="{{ route('guest.dashboard') }}" class="nav-item">
    <i class="fa-solid fa-house"></i><span>Beranda</span>
  </a>
  <a href="#" class="nav-item active">
    <i class="fa-solid fa-bus"></i><span>Transportasi</span>
  </a>
  <form method="POST" action="{{ route('logout') }}" style="margin:0;">
    @csrf
    <button type="submit" class="nav-item" style="background:none;border:none;cursor:pointer;">
      <i class="fa-solid fa-right-from-bracket"></i><span>Keluar</span>
    </button>
  </form>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
let selectedPilihan = @json($currentPilihan);
let selectedJenis   = @json($currentJenis);
let loading = false;

const IS_EXPIRED = @json($isExpired);
const DEADLINE   = @json($menu->berlaku_hingga ? $menu->berlaku_hingga->toIso8601String() : null);

const jenisLabel = { mobil: 'Mobil', motor: 'Motor', truk: 'Truk' };

/* ── Init expired: lock semua ── */
if (IS_EXPIRED) {
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.pilihan-card').forEach(el => {
      el.style.opacity = '0.5'; el.style.cursor = 'not-allowed'; el.style.pointerEvents = 'none';
    });
    document.querySelectorAll('.jenis-btn').forEach(el => {
      el.style.opacity = '0.5'; el.style.cursor = 'not-allowed'; el.style.pointerEvents = 'none';
    });
    const platInput = document.getElementById('platInput');
    if (platInput) {
      platInput.disabled = true; platInput.style.opacity = '0.5'; platInput.style.cursor = 'not-allowed';
    }
    document.getElementById('btnSimpan').style.display = 'none';
    document.getElementById('btnBatal').style.display  = 'none';
  });
}

/* ── Countdown (hero only) ── */
(function initCountdown() {
  if (!DEADLINE || IS_EXPIRED) return;

  const heroEl   = document.getElementById('heroCountdown');
  const heroIcon = document.getElementById('heroIcon');
  if (!heroEl) return;

  const deadlineMs = new Date(DEADLINE).getTime();

  function tick() {
    const diff = deadlineMs - Date.now();

    if (diff <= 0) {
      heroEl.textContent = '00:00:00';
      setTimeout(() => location.reload(), 1000);
      return;
    }

    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000)  / 60000);
    const s = Math.floor((diff % 60000)    / 1000);

    heroEl.textContent = d > 0
      ? `${d}h ${String(h).padStart(2,'0')}j ${String(m).padStart(2,'0')}m`
      : `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;

    // Warna hero icon saat < 1 jam
    if (diff < 3600000 && heroIcon) {
      heroIcon.style.color = '#ff8a80';
      heroIcon.className   = 'fa-solid fa-hourglass-end';
    } else if (diff < 86400000 && heroIcon) {
      heroIcon.style.color = '#ffd54f';
    }
  }

  tick();
  setInterval(tick, 1000);
})();

/* ── Toast ── */
function showToast(msg, type = 'success') {
  const t = document.getElementById('toast');
  t.className = 'toast ' + type;
  t.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'}"></i> ${msg}`;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2800);
}

/* ── Pilih Bus / Kendaraan ── */
function selectPilihan(pilihan) {
  if (IS_EXPIRED) return;
  selectedPilihan = pilihan;
  document.getElementById('cardBus').classList.toggle('selected',       pilihan === 'bus');
  document.getElementById('cardKendaraan').classList.toggle('selected', pilihan === 'kendaraan');
  const platWrap = document.getElementById('platWrap');
  if (pilihan === 'kendaraan') {
    platWrap.classList.add('show');
    document.getElementById('platInput').focus();
  } else {
    platWrap.classList.remove('show');
  }
  updateSimpanBtn();
}

/* ── Pilih Jenis Kendaraan ── */
function selectJenis(jenis) {
  if (IS_EXPIRED) return;
  selectedJenis = jenis;
  document.querySelectorAll('.jenis-btn').forEach(el => {
    el.classList.toggle('selected', el.dataset.jenis === jenis);
  });
}

/* ── Validasi tombol Simpan ── */
function updateSimpanBtn() {
  const btn = document.getElementById('btnSimpan');
  if (IS_EXPIRED || !selectedPilihan) { btn.disabled = true; return; }
  if (selectedPilihan === 'kendaraan') {
    btn.disabled = document.getElementById('platInput').value.trim().length < 4;
    return;
  }
  btn.disabled = false;
}

document.getElementById('platInput').addEventListener('input', function () {
  if (IS_EXPIRED) return;
  this.value = this.value.toUpperCase();
  updateSimpanBtn();
});

/* ── Simpan ── */
function simpanPilihan() {
  if (IS_EXPIRED) { showToast('Pendaftaran transportasi sudah ditutup', 'error'); return; }
  if (loading || !selectedPilihan) return;

  const plat = document.getElementById('platInput').value.trim();
  if (selectedPilihan === 'kendaraan' && plat.length < 4) {
    showToast('Plat nomor tidak valid', 'error'); return;
  }

  loading = true;
  const btn = document.getElementById('btnSimpan');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin-custom"></i> Menyimpan...';

  fetch('{{ route('guest.transportasi.store') }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: JSON.stringify({
      pilihan:         selectedPilihan,
      plat_no:         plat || null,
      jenis_kendaraan: selectedPilihan === 'kendaraan' ? selectedJenis : null,
    }),
  })
  .then(async r => {
    const text = await r.text();
    try { return { status: r.status, body: JSON.parse(text) }; }
    catch(e) { throw new Error('Server error: ' + text.substring(0, 100)); }
  })
  .then(({ status, body }) => {
    if (status === 200) {
      showToast(body.message, 'success');
      updateStatusCard(body);
      document.getElementById('btnBatal').classList.add('show');
    } else {
      const errMsg = body.errors
        ? Object.values(body.errors)[0][0]
        : (body.message ?? 'Terjadi kesalahan');
      showToast(errMsg, 'error');
    }
  })
  .catch(err => showToast(err.message ?? 'Gagal menghubungi server', 'error'))
  .finally(() => {
    loading = false;
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan Pilihan';
  });
}

/* ── Batalkan ── */
function batalkanPilihan() {
  if (IS_EXPIRED) { showToast('Pendaftaran transportasi sudah ditutup', 'error'); return; }
  if (loading) return;

  loading = true;
  const btn = document.getElementById('btnBatal');
  btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin-custom"></i> Membatalkan...';

  fetch('{{ route('guest.transportasi.cancel') }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: JSON.stringify({}),
  })
  .then(r => r.json())
  .then(data => {
    showToast(data.message, 'success');
    selectedPilihan = null;
    selectedJenis   = 'mobil';
    document.getElementById('cardBus').classList.remove('selected');
    document.getElementById('cardKendaraan').classList.remove('selected');
    document.getElementById('platWrap').classList.remove('show');
    document.getElementById('platInput').value = '';
    document.querySelectorAll('.jenis-btn').forEach(el => {
      el.classList.toggle('selected', el.dataset.jenis === 'mobil');
    });
    document.getElementById('btnSimpan').disabled = true;
    document.getElementById('btnBatal').classList.remove('show');
    document.getElementById('statusCard').style.display = 'none';
  })
  .catch(() => showToast('Gagal membatalkan pilihan', 'error'))
  .finally(() => {
    loading = false;
    btn.innerHTML = '<i class="fa-solid fa-xmark"></i> Batalkan Pilihan';
  });
}

/* ── Update Status Card setelah simpan ── */
function updateStatusCard(body) {
  const card  = document.getElementById('statusCard');
  const icon  = document.getElementById('statusIcon');
  const value = document.getElementById('statusValue');
  const sub   = document.getElementById('statusSub');

  if (body.pilihan === 'bus') {
    icon.className    = 'status-icon-wrap bus';
    icon.innerHTML    = '<i class="fa-solid fa-bus"></i>';
    value.textContent = 'Naik Bus';
    sub.textContent   = 'Sudah terdaftar';
  } else {
    const j = body.jenis_kendaraan ?? 'mobil';
    icon.className    = 'status-icon-wrap kendaraan';
    icon.innerHTML    = '<i class="fa-solid fa-car"></i>';
    value.textContent = (jenisLabel[j] ?? 'Kendaraan') + ' Pribadi';
    sub.innerHTML     = `<i class="fa-solid fa-hashtag" style="font-size:10px;margin-right:3px;"></i>${body.plat_no}`;
  }
  card.style.display = 'flex';
}

updateSimpanBtn();
</script>
</body>
</html>
@endsection