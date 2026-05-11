<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#1a3320">
<title>Dashboard — {{ $karyawan->nama }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

  /* HEADER */
  .header {
    background: var(--green-dark);
    padding: calc(16px + var(--safe-top)) 20px 24px;
  }
  .header-row { display:flex; align-items:flex-start; justify-content:space-between; }
  .header-label { font-size:11px; font-weight:600; color:rgba(255,255,255,.4); letter-spacing:1px; }
  .header-name  { font-size:20px; font-weight:800; color:#fff; margin-top:2px; line-height:1.2; }
  .header-dept  { font-size:12px; color:rgba(255,255,255,.4); margin-top:3px; }

  .avatar-sm {
    width:44px; height:44px; border-radius:12px; flex-shrink:0;
    background:rgba(255,255,255,.15);
    display:flex; align-items:center; justify-content:center;
    font-size:16px; font-weight:800; color:#fff;
  }

  /* STATUS BAR */
  .status-bar {
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 14px; padding: 12px 16px;
    margin-top: 16px;
    display: flex; align-items: center; justify-content: space-between;
  }
  .status-left { display:flex; align-items:center; gap:10px; }
  .status-dot {
    width:10px; height:10px; border-radius:50%; flex-shrink:0;
    transition: background .3s;
  }
  .status-dot.hadir { background:#7ec88a; box-shadow:0 0 0 3px rgba(126,200,138,.25); }
  .status-dot.absen { background:#fcd97d; }
  .status-text { font-size:13px; font-weight:700; color:#fff; }
  .status-sub  { font-size:11px; color:rgba(255,255,255,.4); margin-top:1px; }

  .btn-hadir {
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
    border-radius: 10px; padding: 8px 14px;
    color: #fff; font-size: 12px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    transition: all .2s; white-space: nowrap;
  }
  .btn-hadir.hadir  { background:rgba(126,200,138,.2); border-color:rgba(126,200,138,.35); }
  .btn-hadir.absen  { background:rgba(245,158,11,.18); border-color:rgba(245,158,11,.3); color:#fcd97d; }
  .btn-hadir:active { transform:scale(.95); }

  /* CONTENT */
  .content { padding: 16px; max-width: 520px; margin: 0 auto; }

  .section-title {
    font-size: 11px; font-weight: 700; color: var(--text-light);
    letter-spacing: 1px; margin-bottom: 12px; margin-top: 4px;
  }

  /* MENU GRID */
  .menu-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 8px;
  }

  .menu-item {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 16px 12px;
    display: flex; flex-direction: column;
    align-items: center; gap: 10px;
    text-decoration: none;
    transition: transform .18s, box-shadow .18s;
    cursor: pointer;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
    -webkit-tap-highlight-color: transparent;
  }
  .menu-item:active { transform: scale(.94); box-shadow: none; }

  .menu-icon {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    transition: transform .2s;
  }
  .menu-label {
    font-size: 11.5px; font-weight: 700; color: var(--text);
    text-align: center; line-height: 1.3;
  }

  /* BOTTOM NAV */
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

  /* Toast */
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
  .spinner { animation:spin .7s linear infinite; }
</style>
</head>
<body>

<div class="toast" id="toast"></div>

<!-- HEADER -->
<div class="header">
  <div class="header-row">
    <div>
      <div class="header-label">SELAMAT DATANG</div>
      <div class="header-name">{{ $karyawan->nama }}</div>
      <div class="header-dept">{{ $karyawan->departemen }}</div>
    </div>
    <div class="avatar-sm">{{ strtoupper(substr($karyawan->nama, 0, 2)) }}</div>
  </div>

  <!-- Konfirmasi Kehadiran -->
  <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:18px;padding:18px 18px 16px;margin-top:16px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-30px;right:-30px;width:110px;height:110px;border-radius:50%;background:rgba(61,122,71,.3);pointer-events:none;"></div>

    {{-- Label --}}
    <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.4);letter-spacing:1px;margin-bottom:10px;">STATUS KEHADIRAN GATHERING</div>

    {{-- Status besar --}}
    <div id="bigStatus" style="font-size:28px;font-weight:800;line-height:1.1;margin-bottom:4px;color:{{ $karyawan->status_kehadiran ? '#7ec88a' : '#ef9a9a' }};">
      {{ $karyawan->status_kehadiran ? '✓ Hadir' : '✗ Belum Hadir' }}
    </div>
    <div id="statusSub" style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:16px;">
      {{ $karyawan->status_kehadiran ? 'Kehadiran sudah tercatat' : 'Ketuk tombol untuk konfirmasi' }}
    </div>

    {{-- Tombol --}}
    <button id="btnHadir" onclick="toggleKehadiran()"
            style="width:100%;padding:13px;border-radius:12px;
                   font-family:inherit;font-size:14px;font-weight:700;cursor:pointer;
                   transition:all .2s;position:relative;z-index:1;
                   background:{{ $karyawan->status_kehadiran ? 'rgba(126,200,138,.18)' : 'rgba(245,158,11,.18)' }};
                   border:1px solid {{ $karyawan->status_kehadiran ? 'rgba(126,200,138,.35)' : 'rgba(245,158,11,.35)' }};
                   color:{{ $karyawan->status_kehadiran ? '#7ec88a' : '#fcd97d' }};">
      <span id="btnText">
        <i class="fa-solid {{ $karyawan->status_kehadiran ? 'fa-calendar-xmark' : 'fa-calendar-check' }}" style="margin-right:8px;"></i>{{ $karyawan->status_kehadiran ? 'Batalkan Kehadiran' : 'Konfirmasi Hadir Sekarang' }}
      </span>
    </button>
  </div>
</div>

<!-- CONTENT -->
<div class="content">

  <div class="section-title">MENU LAYANAN</div>

  <div class="menu-grid">
    @foreach($menus as $menu)
    <a href="{{ $menu->key === 'voting' ? route('guest.voting') : route('guest.menu', $menu->key) }}" class="menu-item" style="position:relative;">
      @if(!empty($notif[$menu->key]))
      <div style="position:absolute;top:10px;right:10px;width:9px;height:9px;border-radius:50%;background:#ef5350;border:2px solid #fff;"></div>
      @endif
      <div class="menu-icon" style="background:{{ $menu->bg_color }}; color:{{ $menu->color }};">
        <i class="fa-solid {{ $menu->icon }}"></i>
      </div>
      <span class="menu-label">{{ $menu->label }}</span>
    </a>
    @endforeach
  </div>

</div>

<!-- BOTTOM NAV -->
<div class="bottom-nav">
  <a href="{{ route('guest.dashboard') }}" class="nav-item active">
    <i class="fa-solid fa-house"></i>
    <span>Beranda</span>
  </a>
  
  <form method="POST" action="{{ route('logout') }}" style="margin:0;">
    @csrf
    <button type="submit" class="nav-item" style="background:none;border:none;cursor:pointer;">
      <i class="fa-solid fa-right-from-bracket"></i>
      <span>Keluar</span>
    </button>
  </form>
</div>

<script>
let loading = false;

function showToast(msg, type = 'success') {
  const t = document.getElementById('toast');
  t.className = 'toast ' + type;
  t.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'}"></i> ${msg}`;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2800);
}

function toggleKehadiran() {
  if (loading) return;
  loading = true;

  const btn  = document.getElementById('btnHadir');
  const text = document.getElementById('btnText');
  text.innerHTML = '<i class="fa-solid fa-circle-notch spinner"></i>';

  fetch('{{ route('guest.kehadiran') }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({}),
  })
  .then(r => r.json())
  .then(data => {
    const hadir = data.status_kehadiran;
    const btn   = document.getElementById('btnHadir');

    document.getElementById('bigStatus').style.color = hadir ? '#7ec88a' : '#ef9a9a';
    document.getElementById('bigStatus').textContent  = hadir ? '✓ Hadir' : '✗ Belum Hadir';
    document.getElementById('statusSub').textContent  = hadir ? 'Kehadiran sudah tercatat' : 'Ketuk tombol untuk konfirmasi';

    btn.style.background  = hadir ? 'rgba(126,200,138,.18)' : 'rgba(245,158,11,.18)';
    btn.style.borderColor = hadir ? 'rgba(126,200,138,.35)'  : 'rgba(245,158,11,.35)';
    btn.style.color       = hadir ? '#7ec88a' : '#fcd97d';
    document.getElementById('btnText').innerHTML = hadir
      ? '<i class="fa-solid fa-calendar-xmark" style="margin-right:8px;"></i>Batalkan Kehadiran'
      : '<i class="fa-solid fa-calendar-check" style="margin-right:8px;"></i>Konfirmasi Hadir Sekarang';

    showToast(data.message, 'success');
  })
  .catch(() => {
    showToast('Gagal memperbarui kehadiran', 'error');
    text.textContent = '{{ $karyawan->status_kehadiran ? "Batalkan" : "Konfirmasi" }}';
  })
  .finally(() => { loading = false; });
}
</script>
</body>
</html>